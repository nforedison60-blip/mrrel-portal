<?php

return [
    // Letting fee suggested when MrRel invoices a landlord (0.10 = 10% of one month's rent).
    // The admin can change the amount on every invoice before it is sent.
    'letting_fee_rate' => (float) env('MRREL_LETTING_FEE_RATE', 0.10),

    // Days a landlord has to pay an invoice
    'invoice_due_days' => (int) env('MRREL_INVOICE_DUE_DAYS', 7),

    // Shown on invoices and receipts
    'business' => [
        'name'     => env('MRREL_BUSINESS_NAME', 'MrRel'),
        'email'    => env('MRREL_BUSINESS_EMAIL', 'makamu@mrrel.co.za'),
        'whatsapp' => env('MRREL_BUSINESS_WHATSAPP', '075 354 3550'),
        'reg_no'   => env('MRREL_BUSINESS_REG_NO'),
        'vat_no'   => env('MRREL_BUSINESS_VAT_NO'),
    ],

    // Where landlords can pay by EFT. Leave empty until you have set these in .env
    'bank' => [
        'name'    => env('MRREL_BANK_NAME', 'FNB (First National Bank)'),
        'holder'  => env('MRREL_BANK_HOLDER', 'MRREL (PTY) LTD'),
        'account' => env('MRREL_BANK_ACCOUNT', '63236576625'),
        'type'    => env('MRREL_BANK_TYPE', 'Gold Business Account'),
        'branch'  => env('MRREL_BANK_BRANCH', '203809'),
        'swift'   => env('MRREL_BANK_SWIFT', 'FIRNZAJJ'),
    ],

    // Where people email their proof of payment after paying by bank transfer
    'pop_email' => env('MRREL_POP_EMAIL', 'relocations@mrrel.co.za'),
];
