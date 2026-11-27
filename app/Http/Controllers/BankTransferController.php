<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\PropertyMatch;
use App\Models\TenantPayment;
use App\Models\User;
use App\Support\BankTransfer;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    // The instructions page: bank details, the reference to use, and where to email proof of payment
    public function show(string $reference)
    {
        $info = BankTransfer::find($reference, (int) auth()->id());
        abort_unless($info, 404);

        return view('payment.bank', [
            'reference' => $reference,
            'info'      => $info,
            'bank'      => BankTransfer::details(),
            'ready'     => BankTransfer::isReady(),
            'popEmail'  => BankTransfer::popEmail(),
            'layout'    => auth()->user()->role === 'Landlord' ? 'layouts.landlord' : 'layouts.app',
        ]);
    }

    // Admin: everything still waiting for money, with a button to confirm once the payment is seen
    public function adminIndex()
    {
        $rows = collect();

        foreach (Booking::where('status', 'Awaiting payment')->latest()->get() as $b) {
            $rows->push(['ref' => 'BKG-' . BankTransfer::pad($b->id), 'who' => $b->customer_name ?: optional(User::find($b->user_id))->name,
                'what' => 'Moving booking', 'amount' => (float) $b->total_price, 'at' => $b->created_at, 'confirmable' => true]);
        }
        foreach (TenantPayment::with('user')->where('status', 'Pending')->latest()->get() as $p) {
            $rows->push(['ref' => $p->reference(), 'who' => optional($p->user)->name, 'what' => $p->label(),
                'amount' => (float) $p->amount, 'at' => $p->created_at, 'confirmable' => true]);
        }
        foreach (PropertyMatch::with('property.user')->where('intro_status', 'Pending')->latest()->get() as $m) {
            $rows->push(['ref' => $m->introReference(), 'who' => optional(optional($m->property)->user)->name, 'what' => 'Tenant introduction fee (landlord)',
                'amount' => (float) $m->intro_amount, 'at' => $m->updated_at, 'confirmable' => true]);
        }
        foreach (Invoice::with('landlord')->where('status', 'Unpaid')->latest()->get() as $i) {
            $rows->push(['ref' => $i->number, 'who' => optional($i->landlord)->name, 'what' => 'Landlord invoice',
                'amount' => (float) $i->amount, 'at' => $i->created_at, 'confirmable' => false]);
        }

        return view('admin.bank-payments', ['rows' => $rows->sortByDesc('at')->values(), 'popEmail' => BankTransfer::popEmail()]);
    }

    public function adminConfirm(Request $request)
    {
        $data = $request->validate(['reference' => ['required', 'string', 'max:40', 'regex:/^(BKG|TPY|LIN)-\d+$/']]);
        [$ok, $message] = BankTransfer::confirm($data['reference']);

        return redirect()->route('admin.bank-payments')->with($ok ? 'success' : 'error', $message);
    }
}
