<?php

namespace App\Support;

use App\Mail\BookingPaidCustomer;
use App\Mail\BookingPaidProvider;
use App\Models\Booking;
use App\Models\PropertyMatch;
use App\Models\TenantPayment;
use App\Models\User;
use App\Models\ViewingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Paying MrRel by direct bank transfer. The person pays into MrRel's account with the reference shown
 * (BKG-0002, TPY-0007 or LIN-0007), then emails proof of payment. Only an admin can mark it paid.
 */
class BankTransfer
{
    public static function details(): array
    {
        $b = (array) config('mrrel.bank');

        return [
            'name'    => $b['name'] ?? null,
            'holder'  => $b['holder'] ?? null,
            'account' => $b['account'] ?? null,
            'type'    => $b['type'] ?? null,
            'branch'  => $b['branch'] ?? null,
            'swift'   => $b['swift'] ?? null,
        ];
    }

    public static function popEmail(): string
    {
        return (string) config('mrrel.pop_email', 'relocations@mrrel.co.za');
    }

    public static function isReady(): bool
    {
        $d = self::details();

        return !empty($d['account']) && !empty($d['holder']);
    }

    public static function pad(int $id): string
    {
        return str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * What a reference stands for, or null. When $userId is given, only that person's own item is returned.
     * Returns: label, amount, paid (bool), back (url), and the model.
     */
    public static function find(string $reference, ?int $userId = null): ?array
    {
        if (preg_match('/^BKG-(\d+)$/', $reference, $m)) {
            $q = Booking::with('quote')->where('id', (int) $m[1]);
            if ($userId) {
                $q->where('user_id', $userId);
            }
            $b = $q->first();
            if (!$b || $b->status === 'Cancelled') {
                return null;
            }

            return ['model' => $b, 'amount' => (float) $b->total_price, 'paid' => $b->status === 'Paid',
                'label' => 'Booking ' . $reference . (optional($b->quote)->provider_name ? ' with ' . $b->quote->provider_name : ''),
                'back' => route('customer.bookings', ['booking' => $b->id])];
        }

        if (preg_match('/^TPY-(\d+)$/', $reference, $m)) {
            $q = TenantPayment::where('id', (int) $m[1]);
            if ($userId) {
                $q->where('user_id', $userId);
            }
            $p = $q->first();
            if (!$p || $p->status === 'Refunded') {
                return null;
            }

            return ['model' => $p, 'amount' => (float) $p->amount, 'paid' => $p->status === 'Paid',
                'label' => $p->label(),
                'back' => $p->type === TenantPayment::MATCHING_FEE ? route('customer.matches') : route('customer.shortlist')];
        }

        if (preg_match('/^LIN-(\d+)$/', $reference, $m)) {
            $match = PropertyMatch::with('property')->find((int) $m[1]);
            if (!$match || $match->intro_status === null || !$match->property) {
                return null;
            }
            if ($userId && (int) $match->property->user_id !== $userId) {
                return null;
            }

            return ['model' => $match, 'amount' => (float) $match->intro_amount, 'paid' => $match->intro_status === 'Paid',
                'label' => 'Tenant introduction fee',
                'back' => route('landlord.introductions')];
        }

        return null;
    }

    /** Admin has seen the money: do the same things the online confirmation does. Safe to run twice. */
    public static function confirm(string $reference): array
    {
        $info = self::find($reference);
        if (!$info) {
            return [false, 'That payment could not be found.'];
        }
        if ($info['paid']) {
            return [true, $reference . ' was already marked paid.'];
        }

        $justPaidBooking = null;

        DB::transaction(function () use ($reference, &$justPaidBooking) {
            if (str_starts_with($reference, 'BKG-')) {
                $b = Booking::lockForUpdate()->find((int) substr($reference, 4));
                if ($b && $b->status !== 'Paid' && $b->status !== 'Cancelled') {
                    $b->update(['status' => 'Paid']);
                    $justPaidBooking = $b;
                }
            } elseif (str_starts_with($reference, 'TPY-')) {
                $p = TenantPayment::lockForUpdate()->find((int) substr($reference, 4));
                if ($p && $p->status === 'Pending') {
                    $p->update(['status' => 'Paid', 'paid_at' => now(), 'ozow_transaction_id' => 'BANK']);
                    if ($p->type === TenantPayment::ATTENDANCE && $p->viewing_request_id) {
                        $v = ViewingRequest::find($p->viewing_request_id);
                        if ($v) {
                            $v->forceFill(['mrrel_attends' => true])->save();
                        }
                    }
                }
            } elseif (str_starts_with($reference, 'LIN-')) {
                $m = PropertyMatch::lockForUpdate()->find((int) substr($reference, 4));
                if ($m && $m->intro_status === 'Pending') {
                    $m->forceFill([
                        'intro_status' => 'Paid', 'intro_paid_at' => now(),
                        'intro_ozow_id' => 'BANK', 'landlord_status' => 'Interested',
                    ])->save();
                }
            }
        });

        if ($justPaidBooking) {
            try {
                $justPaidBooking->loadMissing('quote');
                $customer = User::find($justPaidBooking->user_id);
                $provider = User::find(optional($justPaidBooking->quote)->provider_user_id);
                if ($customer && $customer->email) {
                    Mail::to($customer->email)->send(new BookingPaidCustomer($justPaidBooking));
                }
                if ($provider && $provider->email) {
                    Mail::to($provider->email)->send(new BookingPaidProvider($justPaidBooking));
                }
            } catch (\Throwable $e) {
                Log::error('Bank payment emails failed: ' . $e->getMessage());
            }
        }

        Log::info('Bank payment confirmed by admin', ['reference' => $reference, 'admin' => auth()->id()]);

        return [true, $reference . ' is marked as paid.'];
    }
}
