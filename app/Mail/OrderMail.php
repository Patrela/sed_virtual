<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Mail\Mailables\Address;
class OrderMail extends Mailable
{
    use Queueable, SerializesModels;


    public $mailData;



    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            /* use Illuminate\Mail\Mailables\Address; */
            //from: new Address("{$this->mailData['from']}"),
            subject: "{$this->mailData['subject']}",
        );

    }

    public function content(): Content
    {
        return new Content(
            //view: 'emails.product',
            view: 'emails.order',
            with: ['order' =>$this->mailData['order'], 'customer_mail'=> $this->mailData['customer_mail'], 'customer' => $this->mailData['customer'], 'nit' => $this->mailData['nit']],
        );
    }


    public function attachments(): array
    {
        return [];
    }

    /*
    public function build()
    {
        return $this->view('emails.product')
                    ->with([
                        'product' => $this->mailData['product'],
                        'sender' => $this->mailData['from'],
                    ])
                    ->subject($this->mailData['subject'])
                    ->from($this->mailData['from']);
    }
    */
}
