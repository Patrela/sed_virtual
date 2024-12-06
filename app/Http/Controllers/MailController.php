<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Trade;
//use Illuminate\Support\Facades\Mail;
//use App\Mail\QuoteMail;
use App\Jobs\SendOrderEmail;
use App\Jobs\SendQuoteEmail;
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

    public function sendOrderMail(string $emailTrade, int $ordernumber) //Request $request
    {
        $sender =  config('mail.from.address');
        $emailTo =  config('mail.to.order_address'); // env('MAIL_ORDER_ADDRESS')
       // $email = $request->header('x-api-receiver');
        // send mail just for trades in production environments
        $responseCode= 200;
        if (!app()->isProduction() || !str_contains($emailTrade, "@sedint") ) $responseCode= 422;
        if ( $responseCode= 200 ){

            $trade=  Trade::where('email', "{$emailTrade}")->first();
            if(!$trade) {
                $responseCode= 404;
            }
            else {
                $order = Order::where('order_number', $ordernumber)
                        ->where('trade_nit', $trade->nit)
                        ->first();
                if(!$order) {
                    $responseCode= 404;
                }
            }

        }
        //Log::info("user.  " . $sender . " order " . $order->order);


        if ($responseCode !== 200) {
            return response()->json([
                'message' => "Error recovering Order",
                'code' => $responseCode,
            ], $responseCode);
        }

        $dispatchData = [
            'subject' => 'SED Order from ' .$trade->name,
            'mail_to' => $emailTo,
            'owner' =>  $sender,
            'from' => $sender,
            'message' => "Approval the request...",
            'customer' => $trade->name,
            'customer_mail' => $emailTrade,
            'nit' => $trade->nit,
            'order' => $order
        ];

        //Log::info("MAILCONTROLLER.SendOrderMail email data  ", $dispatchData);
        SendOrderEmail::dispatchAfterResponse($dispatchData);

        return response()->json([
            'result' => 'Email sending successful: ' . $emailTrade .' Order = ' . $order->order,
            'code' => $responseCode,
        ],  $responseCode);
        //return redirect('/');
    }

}
