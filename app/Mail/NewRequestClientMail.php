<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRequestClientMail extends Mailable
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
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for your request on Gigat'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-request-client',
            with: [
                'request' => $this->request,
                'finance' => $this->finance,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
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
