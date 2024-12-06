<?php

namespace App\Jobs;

use App\Mail\OrderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOrderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->data['mail_to'])->send(new OrderMail([
                'subject' => $this->data['subject'],
                'mail_to' =>  $this->data['mail_to'],
                'owner' => $this->data['owner'],
                'from' => $this->data['from'],
                'order' =>$this->data['order'],
                'customer' => $this->data['customer'],
                'customer_mail' => $this->data['customer_mail'],
                'nit' => $this->data['nit']
            ]));
        } catch (\Exception $e) {
            Log::info("error Message = " . $e->getMessage() . "code= " . $e->getCode());
            // return response()->json([
            //     'error' => $e->getMessage(),
            //     'code' => $e->getCode(),
            // ], 403);
        }
    }
}
