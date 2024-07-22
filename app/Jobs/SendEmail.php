<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ItemMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SendEmail implements ShouldQueue
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
            Mail::to($this->data['mail_to'])->send(new ItemMail([
                'subject' => $this->data['subject'],
                'mail_to' =>  $this->data['mail_to'],
                'owner' => $this->data['owner'],
                'from' => $this->data['from'],
                'product' =>$this->data['product'],
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
