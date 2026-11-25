<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'number', 'user_id', 'property_id', 'description', 'amount', 'status', 'due_at',
        'paid_at', 'payment_method', 'payment_reference', 'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_at' => 'date',
        'paid_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'Paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'Unpaid' && $this->due_at && $this->due_at->isPast() && ! $this->due_at->isToday();
    }

    // The reference sent to Ozow (the notify handler recognises the INV- prefix)
    public function gatewayReference(): string
    {
        return 'INV-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    // Creates an unpaid invoice for the landlord who owns the property
    public static function issue(Property $property, float $amount, ?string $description = null, ?int $dueDays = null): self
    {
        $invoice = self::create([
            'user_id' => $property->user_id,
            'property_id' => $property->id,
            'description' => $description ?: 'Letting fee: ' . $property->title,
            'amount' => round($amount, 2),
            'status' => 'Unpaid',
            'due_at' => now()->addDays($dueDays ?: (int) config('mrrel.invoice_due_days', 7))->toDateString(),
        ]);
        $invoice->update(['number' => 'INV-' . now()->format('Y') . '-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT)]);

        return $invoice;
    }

    // Records the payment and issues the receipt number. Call inside a transaction with the row locked.
    public function markPaid(string $method, ?string $reference = null): void
    {
        $this->update([
            'status' => 'Paid',
            'paid_at' => now(),
            'payment_method' => $method,
            'payment_reference' => $reference,
            'receipt_number' => 'RCT-' . now()->format('Y') . '-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT),
        ]);
    }
}
