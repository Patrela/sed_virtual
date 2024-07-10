<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProductController;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Auth;

class MailController extends Controller
{
    public function sendMail(Request $request, string $sku) //Request $request
    {
        $sender = ($request->has('user')) ? $request->user()->email :
                ((Auth::check()) ? Auth::user()->email : env('MAIL_FROM_ADDRESS'));
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
            'from' => $sender,
            'subject' => 'SED: ' .$product->name,
            'message' => "Approval the request...",
            'product' => $product,
        ];

        SendEmail::dispatchAfterResponse($dispatchData);
        /*
        Mail::to($$email)
            ->cc($sender)
            ->send(new ItemMail($sender, $product), function ($message, $sender,$product) {
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

}
