<?php

namespace App\Jobs;

use App\Models\Product;
use App\Mail\ProductMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessProductMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $receiver;
    protected $sender;
    protected $product;
    public function __construct(string $receiver, string $sender, Product $product )
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->receiver)
            ->cc($this->sender)
            ->send(new ProductMailable($this->sender, $this->product), function ($message, $sender,) {
                $message->subject('SKU imperativo');
                $message->from($sender);
                $message->setContentType('text/html'); // Set Content-Type header
            });

    }
}
