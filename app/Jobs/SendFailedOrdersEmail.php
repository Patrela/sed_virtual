<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\FailedOrdersMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendFailedOrdersEmail implements ShouldQueue
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
            Mail::to($this->data['mail_to'])->send(new FailedOrdersMail($this->data));
        } catch (\Exception $e) {
            Log::error("Error sending failed orders email: " . $e->getMessage());
        }
    }
}
