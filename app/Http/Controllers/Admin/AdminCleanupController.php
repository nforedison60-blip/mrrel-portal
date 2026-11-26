<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Problem;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Quote;
use App\Models\ServiceRequest;
use App\Models\TenantPayment;
use App\Models\ViewingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Admin tools to remove a property or a request, and to record a refund.
 * Anything with real money behind it is refused, so records are never lost.
 * "Mark refunded" only RECORDS a refund: the money itself is returned in the Ozow dashboard.
 */
class AdminCleanupController extends Controller
{
    public function deleteProperty(Property $property)
    {
        if ($property->status === 'Under Offer') {
            return redirect()->route('admin.properties')->with('error', 'A tenant accepted "' . $property->title . '", so it was kept. Finish or cancel that first.');
        }
        $hasPaidInvoice = Invoice::where('property_id', $property->id)->where('status', 'Paid')->exists();
        $hasPaidIntro = PropertyMatch::where('property_id', $property->id)->where('intro_status', 'Paid')->exists();
        if ($hasPaidInvoice || $hasPaidIntro) {
            return redirect()->route('admin.properties')->with('error', '"' . $property->title . '" has paid fees, so it was kept for your records.');
        }

        $files = array_values(array_filter(array_merge([$property->image_path], (array) ($property->photo_paths ?? []))));
        $title = $property->title;

        DB::transaction(function () use ($property) {
            PropertyMatch::where('property_id', $property->id)->delete();
            ViewingRequest::where('property_id', $property->id)->delete();
            Invoice::where('property_id', $property->id)->delete(); // only unpaid or cancelled ones get this far
            $property->delete();
        });
        $this->deleteFiles($files);

        return redirect()->route('admin.properties')->with('success', 'Removed "' . $title . '" and everything linked to it.');
    }

    public function deleteRequest(ServiceRequest $serviceRequest)
    {
        $quoteIds = Quote::where('service_request_id', $serviceRequest->id)->pluck('id');
        $bookings = Booking::whereIn('quote_id', $quoteIds)->get();
        if ($bookings->where('status', 'Paid')->isNotEmpty()) {
            return redirect()->route('admin.requests')->with('error', 'This request has a paid booking, so it was kept for your records.');
        }

        $files = array_values(array_filter((array) ($serviceRequest->photo_paths ?? [])));

        DB::transaction(function () use ($serviceRequest, $quoteIds, $bookings) {
            Problem::whereIn('booking_id', $bookings->pluck('id'))->delete();
            Booking::whereIn('id', $bookings->pluck('id'))->delete();
            Quote::whereIn('id', $quoteIds)->delete();
            $serviceRequest->delete();
        });
        $this->deleteFiles($files);

        return redirect()->route('admin.requests')->with('success', 'Request removed together with its quotes.');
    }

    // Records that a tenant payment was refunded. The tenant loses what that payment unlocked.
    public function refundTenantPayment(TenantPayment $payment)
    {
        $done = false;
        DB::transaction(function () use ($payment, &$done) {
            $locked = TenantPayment::lockForUpdate()->find($payment->id);
            if ($locked && $locked->status === 'Paid') {
                $locked->update(['status' => 'Refunded', 'meta' => array_merge((array) $locked->meta, ['refunded_at' => now()->toDateTimeString()])]);
                $done = true;
            }
        });

        return redirect()->route('admin.tenant-payments')->with($done ? 'success' : 'error',
            $done ? 'Recorded as refunded. Remember to return the money in your Ozow dashboard.' : 'Only a paid payment can be marked as refunded.');
    }

    // Records that a landlord's introduction fee was refunded. The landlord no longer sees the tenant's contact details.
    public function refundIntroduction(PropertyMatch $propertyMatch)
    {
        $done = false;
        DB::transaction(function () use ($propertyMatch, &$done) {
            $locked = PropertyMatch::lockForUpdate()->find($propertyMatch->id);
            if ($locked && $locked->intro_status === 'Paid') {
                $locked->forceFill(['intro_status' => 'Refunded'])->save();
                $done = true;
            }
        });

        return redirect()->route('admin.introductions')->with($done ? 'success' : 'error',
            $done ? 'Recorded as refunded. Remember to return the money in your Ozow dashboard.' : 'Only a paid introduction can be marked as refunded.');
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            try { Storage::disk('s3')->delete($path); } catch (\Throwable $e) { Log::warning('Could not delete a file: ' . $e->getMessage()); }
        }
    }
}
