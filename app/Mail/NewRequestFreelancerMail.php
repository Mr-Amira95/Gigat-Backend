<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRequestFreelancerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $finance;
    public $contractPath;

    /**
     * Create a new message instance.
     */
    public function __construct($request, $finance, $contractPath)
    {
        $this->request = $request;
        $this->finance = $finance;
        $this->contractPath = $contractPath;
    }

    /**
     * Email Subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wow! You got a new request'
        );
    }

    /**
     * Email View
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-request-freelancer',
            with: [
                'request' => $this->request,
                'finance' => $this->finance,
            ],
        );
    }

    /**
     * Attach Contract PDF
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path($this->contractPath))
                ->as('Contract.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
