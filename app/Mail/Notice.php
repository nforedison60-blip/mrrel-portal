<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// One plain, branded message used for the "something happened" emails
class Notice extends Mailable
{
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public array $lines,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notice');
    }
}
