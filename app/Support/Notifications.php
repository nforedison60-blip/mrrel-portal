<?php

namespace App\Support;

use App\Mail\Notice;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Provider;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * The emails that tell people something happened. A failed email is logged and never breaks the page or a payment.
 * The listeners are registered once from AppServiceProvider.
 */
class Notifications
{
    public static function register(): void
    {
        // A tenant payment was confirmed (matching fee or viewing attendance)
        TenantPayment::updated(function (TenantPayment $payment) {
            if ($payment->wasChanged('status') && $payment->status === 'Paid') {
                self::tenantPaid($payment);
            }
        });

        // A landlord paid the introduction fee
        PropertyMatch::updated(function (PropertyMatch $match) {
            if ($match->wasChanged('intro_status') && $match->intro_status === 'Paid') {
                self::introductionPaid($match);
            }
        });

        // MrRel approved a property
        Property::updated(function (Property $property) {
            if ($property->wasChanged('status') && $property->status === 'Approved' && $property->getOriginal('status') === 'Draft') {
                self::propertyApproved($property);
            }
        });

        // MrRel verified a mover
        Provider::updated(function (Provider $provider) {
            if ($provider->wasChanged('verification_status') && $provider->verification_status === 'Verified') {
                self::providerVerified($provider);
            }
        });
    }

    // One email. Skips an empty address, and logs instead of failing.
    public static function send(?string $to, string $subject, string $heading, array $lines, ?string $ctaLabel = null, ?string $ctaUrl = null): void
    {
        if (!$to) {
            return;
        }
        try {
            Mail::to($to)->send(new Notice($subject, $heading, $lines, $ctaLabel, $ctaUrl));
        } catch (\Throwable $e) {
            Log::warning('Notification email failed', ['to' => $to, 'subject' => $subject, 'error' => $e->getMessage()]);
        }
    }

    private static function adminEmail(): ?string
    {
        return config('mrrel.business.email');
    }

    private static function link(string $route): ?string
    {
        try {
            return route($route);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // MrRel sent a tenant their matches (called from the admin matching screen)
    public static function matchesSent(RentalNeed $need, int $count): void
    {
        $tenant = $need->user;
        if (!$tenant) {
            return;
        }
        self::send($tenant->email, 'MrRel found ' . ($count === 1 ? 'a home' : $count . ' homes') . ' for you',
            'Good news, ' . LandlordIntro::firstName($tenant->name) . '!',
            [
                'MrRel has matched ' . ($count === 1 ? 'a home' : $count . ' homes') . ' to what you asked for.',
                'You can see a preview now. Pay the once-off R' . number_format(TenantFees::matchingFee(), 0) . ' matching fee to see the full details and ask for viewings.',
            ],
            'See my matches', self::link('customer.matches'));
    }

    private static function tenantPaid(TenantPayment $payment): void
    {
        $tenant = User::find($payment->user_id);
        if (!$tenant) {
            return;
        }

        if ($payment->type === TenantPayment::MATCHING_FEE) {
            self::send($tenant->email, 'Payment received: your matches are unlocked', 'Thank you, ' . LandlordIntro::firstName($tenant->name) . '!',
                ['We received your matching fee of R' . number_format((float) $payment->amount, 2) . '.', 'You can now see the full details of your matches and ask to view them.'],
                'See my matches', self::link('customer.matches'));

            // The landlords of the matched homes hear about a tenant waiting for them (one email per landlord)
            $landlordIds = PropertyMatch::where('user_id', $tenant->id)->where('status', 'Offered')->whereNull('landlord_status')
                ->with('property')->get()
                ->filter(fn ($m) => $m->property && $m->property->status === 'Approved' && $m->property->user_id)
                ->pluck('property.user_id')->unique();
            foreach (User::whereIn('id', $landlordIds)->get() as $landlord) {
                self::send($landlord->email, 'A tenant matches your property', 'A tenant is interested in your property',
                    ['MrRel found a tenant who fits one of your properties.', 'Open Tenant matches to see a short profile. If you would like to proceed, you pay a once-off introduction fee and we release their contact details.'],
                    'See the tenant match', self::link('landlord.introductions'));
            }
            return;
        }

        // Viewing attendance
        self::send($tenant->email, 'MrRel will attend your viewing', 'Payment received',
            ['We received your payment of R' . number_format((float) $payment->amount, 2) . '. Someone from MrRel will attend the viewing with you.'],
            'See my shortlist', self::link('customer.shortlist'));
        self::send(self::adminEmail(), 'Viewing attendance paid', 'A tenant paid for MrRel to attend a viewing',
            [($tenant->name ?: 'A tenant') . ' (' . $tenant->email . ') paid R' . number_format((float) $payment->amount, 2) . ' for attendance. Please arrange who attends.'],
            'Open tenant payments', self::link('admin.tenant-payments'));
    }

    private static function introductionPaid(PropertyMatch $match): void
    {
        $match->loadMissing('property', 'user');
        $property = $match->property;
        $tenant = $match->user;
        $landlord = $property ? User::find($property->user_id) : null;

        if ($landlord) {
            self::send($landlord->email, 'Payment received: your tenant\'s details are ready', 'Thank you, ' . LandlordIntro::firstName($landlord->name) . '!',
                ['We received your introduction fee for ' . optional($property)->title . '.', 'The tenant\'s name, phone and email are now under Tenant matches. MrRel will coordinate the viewing with you.'],
                'See the tenant\'s details', self::link('landlord.introductions'));
        }
        self::send(self::adminEmail(), 'Introduction paid: arrange the viewing', 'A landlord paid an introduction fee',
            [
                'Property: ' . optional($property)->title . ' (' . optional($property)->suburb . ').',
                'Landlord: ' . optional($landlord)->name . ', ' . (optional($landlord)->phone ?: 'no phone') . ', ' . optional($landlord)->email . '.',
                'Tenant: ' . optional($tenant)->name . ', ' . (optional($tenant)->phone ?: 'no phone') . ', ' . optional($tenant)->email . '.',
            ],
            'Open landlord introductions', self::link('admin.introductions'));
    }

    private static function propertyApproved(Property $property): void
    {
        $landlord = $property->user_id ? User::find($property->user_id) : null;
        if (!$landlord) {
            return;
        }
        self::send($landlord->email, 'Your property is live', 'Your property was approved',
            ['MrRel approved "' . $property->title . '". It is now live, and MrRel can match it to tenants.'],
            'See my properties', self::link('landlord.properties'));
    }

    private static function providerVerified(Provider $provider): void
    {
        self::send($provider->email, 'You are verified: you can now send quotes', 'Welcome aboard!',
            ['MrRel verified your account. You can now see requests in your area and send quotes.'],
            'See requests', self::link('provider.requests'));
    }
}
