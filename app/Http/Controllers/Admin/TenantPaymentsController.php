<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantPayment;

/** Read-only list of the tenant fees. */
class TenantPaymentsController extends Controller
{
    public function index()
    {
        $payments = TenantPayment::with('user')->latest()->get();

        return view('admin.tenant-payments', [
            'payments'  => $payments,
            'collected' => $payments->where('status', 'Paid')->sum('amount'),
            'pending'   => $payments->where('status', 'Pending')->sum('amount'),
        ]);
    }
}
