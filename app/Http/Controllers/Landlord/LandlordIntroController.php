<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\PropertyMatch;
use App\Support\LandlordIntro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The landlord's side of finding a tenant. MrRel introduces a matched tenant, the landlord sees a limited
 * profile, decides, and pays the once-off introduction fee. Nothing here ever marks a payment as paid:
 * only the signed message at /payment/notify does that.
 */
class LandlordIntroController extends Controller
{
    public function index()
    {
        $matches = LandlordIntro::visibleFor(auth()->id());

        return view('landlord.introductions', [
            'waiting' => $matches->filter(fn ($m) => !$m->introPaid())->values(),
            'released' => $matches->filter(fn ($m) => $m->introPaid())->values(),
            'fee' => LandlordIntro::fee(),
        ]);
    }

    // "Not interested": the tenant is not shown to this landlord again
    public function decline(PropertyMatch $propertyMatch)
    {
        $match = LandlordIntro::actionable(auth()->id(), $propertyMatch->id);
        abort_unless($match, 404);

        if ($match->introPaid()) {
            return redirect()->route('landlord.introductions')->with('error', 'You have already paid for this introduction.');
        }
        $match->forceFill(['landlord_status' => 'Declined', 'landlord_decided_at' => now()])->save();

        return redirect()->route('landlord.introductions')->with('success', 'Noted. MrRel will look for another tenant for you.');
    }

    // "Proceed": the landlord agrees to a viewing and pays the introduction fee
    public function pay(PropertyMatch $propertyMatch)
    {
        $match = LandlordIntro::actionable(auth()->id(), $propertyMatch->id);
        abort_unless($match, 404);

        if ($match->introPaid()) {
            return redirect()->route('landlord.introductions')->with('success', 'This introduction is already paid. The tenant\'s details are below.');
        }
        if ($match->property->status !== 'Approved') {
            return redirect()->route('landlord.introductions')->with('error', 'This property is no longer live, so the introduction cannot go ahead.');
        }

        // The fee is fixed on the server when the landlord starts paying; the browser never sends an amount
        $match->forceFill([
            'landlord_status' => 'Interested',
            'landlord_decided_at' => $match->landlord_decided_at ?? now(),
            'intro_status' => 'Pending',
            'intro_amount' => LandlordIntro::fee(),
        ])->save();

        if (request('method') === 'bank') {
            return redirect()->route('bank.show', $match->introReference());
        }

        return $this->ozowForm($match);
    }

    // Back from the bank. This page NEVER marks anything as paid.
    public function back(Request $request)
    {
        $match = $this->ownFromReference((string) $request->query('TransactionReference'));
        if (!$match) {
            return redirect()->route('landlord.introductions');
        }
        if ($match->introPaid()) {
            return redirect()->route('landlord.introductions')->with('success', 'Thank you! Your payment is confirmed and the tenant\'s details are released below.');
        }
        $status = $request->query('Status');
        if ($status && strtolower($status) !== 'complete') {
            return redirect()->route('landlord.introductions')->with('error', 'Your bank did not confirm the payment. You can try again.');
        }

        return redirect()->route('landlord.introductions')->with('success', 'Thank you! We are waiting for your bank to confirm the payment. Refresh in a minute or two.');
    }

    public function cancelled()
    {
        return redirect()->route('landlord.introductions')
            ->with('error', 'The payment was cancelled, so nothing was charged. You can try again.');
    }

    private function ownFromReference(string $reference): ?PropertyMatch
    {
        if (!preg_match('/^LIN-(\d+)$/', $reference, $m)) {
            return null;
        }
        $match = PropertyMatch::with('property')->find((int) $m[1]);

        return ($match && $match->property && (int) $match->property->user_id === (int) auth()->id()) ? $match : null;
    }

    private function ozowForm(PropertyMatch $match)
    {
        $siteCode = config('services.ozow.site_code');
        $privateKey = config('services.ozow.private_key');
        if (!$siteCode || !$privateKey) {
            Log::error('Ozow is not configured: OZOW_SITE_CODE or OZOW_PRIVATE_KEY is missing.');
            return redirect()->route('landlord.introductions')->with('error', 'Online payment is not available right now. Please try again later.');
        }

        $isTest = config('services.ozow.is_test') ? 'true' : 'false';
        $amount = number_format((float) $match->intro_amount, 2, '.', '');
        $reference = $match->introReference();
        $bankReference = 'MrRel';

        $successUrl = route('landlord.introductions.back');
        $cancelUrl = route('landlord.introductions.cancelled');
        $errorUrl = route('landlord.introductions.cancelled');
        $notifyUrl = url('/payment/notify'); // the one signed address that confirms payments

        $hashCheck = hash('sha512', strtolower($siteCode . 'ZA' . 'ZAR' . $amount . $reference . $bankReference
            . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey));

        $fields = [
            'SiteCode' => $siteCode, 'CountryCode' => 'ZA', 'CurrencyCode' => 'ZAR', 'Amount' => $amount,
            'TransactionReference' => $reference, 'BankReference' => $bankReference, 'HashCheck' => $hashCheck,
            'SuccessUrl' => $successUrl, 'CancelUrl' => $cancelUrl, 'ErrorUrl' => $errorUrl,
            'NotifyUrl' => $notifyUrl, 'IsTest' => $isTest,
        ];

        $html = '<form id="ozow_form" action="https://pay.ozow.com/" method="post">';
        foreach ($fields as $name => $value) {
            $html .= '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '">';
        }
        $html .= '</form><script>document.getElementById("ozow_form").submit();</script>';

        return response($html);
    }
}
