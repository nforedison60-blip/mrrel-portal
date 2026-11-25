<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// The admin's side: send an invoice after the lease is concluded, record EFT payments
class AdminInvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('property')->latest()->get();
        $properties = Property::whereIn('status', ['Under Offer', 'Approved'])
            ->orderByRaw("CASE WHEN status = 'Under Offer' THEN 0 ELSE 1 END")->latest()->get();
        $owners = User::whereIn('id', $properties->pluck('user_id')->merge($invoices->pluck('user_id'))->filter()->unique())->pluck('name', 'id');

        return view('admin.invoices', [
            'invoices' => $invoices,
            'properties' => $properties,
            'owners' => $owners,
            'feeRate' => (float) config('mrrel.letting_fee_rate'),
            'dueDays' => (int) config('mrrel.invoice_due_days'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255',
            'due_days' => 'nullable|integer|min:1|max:60',
        ]);

        $property = Property::findOrFail($data['property_id']);
        if (! $property->user_id) {
            return back()->withInput()->with('error', 'This property has no landlord on it, so it cannot be invoiced.');
        }
        if (Invoice::where('property_id', $property->id)->where('status', 'Unpaid')->exists()) {
            return back()->withInput()->with('error', 'This property already has an unpaid invoice. Cancel it first if you want to send a new one.');
        }

        $invoice = Invoice::issue($property, (float) $data['amount'], $data['description'] ?? null, isset($data['due_days']) ? (int) $data['due_days'] : null);

        return redirect()->route('admin.invoices')->with('success', 'Invoice ' . $invoice->number . ' sent. The landlord sees it under Invoices in their dashboard.');
    }

    // The landlord paid by EFT and the money is in the bank account
    public function markPaid(Request $request, Invoice $invoice)
    {
        $request->validate(['reference' => 'nullable|string|max:100']);

        $marked = false;
        DB::transaction(function () use ($invoice, $request, &$marked) {
            $locked = Invoice::lockForUpdate()->find($invoice->id);
            if ($locked && $locked->status === 'Unpaid') {
                $locked->markPaid('EFT', $request->input('reference') ?: null);
                $marked = true;
            }
        });

        return redirect()->route('admin.invoices')->with(
            $marked ? 'success' : 'error',
            $marked ? 'Recorded as paid by EFT. The receipt is ready for the landlord.' : 'Only an unpaid invoice can be marked as paid.'
        );
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status !== 'Unpaid') {
            return redirect()->route('admin.invoices')->with('error', 'Only an unpaid invoice can be cancelled.');
        }
        $invoice->update(['status' => 'Cancelled']);

        return redirect()->route('admin.invoices')->with('success', 'Invoice ' . $invoice->number . ' cancelled.');
    }
}
