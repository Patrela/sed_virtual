<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Mail\Mailables\Address;
class ItemMail extends Mailable
{
    use Queueable, SerializesModels;


    public $mailData;


    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            /* use Illuminate\Mail\Mailables\Address; */
            //from: new Address("{$this->mailData['from']}"),
            subject: "{$this->mailData['subject']}",
        );

    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            //view: 'emails.prueba',
            view: 'emails.product',
            with: ['product' =>$this->mailData['product'], 'sender' => $this->mailData['from']],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
