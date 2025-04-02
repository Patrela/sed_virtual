<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Trade;


//use App\Mail\QuoteMail;
use App\Jobs\SendOrderEmail;
use App\Jobs\SendQuoteEmail;
use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use Ramsey\Uuid\Type\Integer;

class MailController extends Controller
{
    public function sendSkuMail(Request $request, string $sku) //Request $request
    {
        // $sender = ($request->has('user')) ? $request->user()->email :
        //         ((Auth::check()) ? Auth::user()->email : env('MAIL_FROM_ADDRESS'));
        $sender =  config('mail.from.address'); // env('MAIL_FROM_ADDRESS')
        $owner = ((Auth::check()) ? Auth::user()->email : $sender);
        $email = $request->header('x-api-receiver');

        //Log::info("user.  " . $sender . " sku " . $sku);
        $products=  app(ProductController::class)->searchProductBySku( $sku);

        if (count($products) == 0) {
            return response()->json([
                'message' => "Error Product not found {$sku} ",
                'code' => 404,
            ], 404);
        }
        $product = $products[0];

        $dispatchData = [
            'mail_to' => $email,
            //'to' => $email,
            'from' => $sender,
            'owner' => $owner,
            'subject' => 'SED: ' .$product->name,
            'message' => "Approval the request...",
            'product' => $product,
        ];

        //Mail::mailer('msgraph')->to($dispatchData['to'])->send(new QuoteMail($dispatchData));


        
        SendQuoteEmail::dispatchAfterResponse($dispatchData);

        /*
        Mail::to($$email)
            ->cc($sender)
            ->send(new QuoteMail($sender, $product), function ($message, $sender,$product) {
                $message->subject('SED: ' .$product->name);
                $message->from($sender);
                $message->setContentType('text/html'); // Set Content-Type header
            });
            */

        //flash()->success('Mail sent successfully.');
        //flash('It works for me!!!');
        return response()->json([
            'result' => 'Email sending successful: ' . $email .' sku= ' . $sku,
            'code' => 200,
        ], 200);
        //return redirect('/');
    }

    //public function sendOrderMail(string $emailTrade, int $ordernumber) //Request $request
    public function sendOrderMail(array $tradeData, object $order) //Request $request
    
    {
        $sender =  config('mail.from.address');
        $emailTo =  config('mail.to.order_address'); // env('MAIL_ORDER_ADDRESS')

        $dispatchData = [
            'subject' => 'SED Order from ' .$tradeData["name"],
            'mail_to' => $emailTo,
            'To' => $emailTo,
            //'mail_to' => "patorela@gmail.com",
            'owner' =>  $sender,
            'from' => $sender,
            'message' => "Approval the request...",
            'customer' => $tradeData["name"],
            'customer_mail' => $tradeData["email"],
            'nit' => $tradeData["nit"],
            'order' => $order
        ];

        Log::info("MAILCONTROLLER.SendOrderMail email data ", $dispatchData);
        SendOrderEmail::dispatchAfterResponse($dispatchData);

        return response()->json([
            'result' => 'Email sending successful: ' . $tradeData["email"] .' Order = ' . $order->order_number,
            'code' => 200,
        ],  200);
        //return redirect('/');
    }

}
