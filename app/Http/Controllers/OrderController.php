<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('items')
            ->orderBy('order_number', 'DESC')
            ->get();
        return response()->json($orders->toArray(), 200);
    }

    public function show(string $order)
    {
        if (!$order) {
            return response()->json([
                'message' => 'invalid order data',
                'code' => 500,
            ], 500);
        }
        $orders = Order::with('items')->where('order_number', $order)->get();

        if (count($orders) == 0) {
            return response()->json([
                'message' => "Error Order not found",
                'code' => 404,
            ], 404);
        }
        return response()->json($orders->toArray(), 200);
    }

    public function createOrUpdateOrder(Request $request)
    {

        $trade = app(ConnectController::class)->validateTradeBasicAuthentication($request);

        if (!$trade) {
            return response()->json(['message' => __('Auth.failure'), 'code' => 422,], 422);
        }
        // Validate the JSON body
        $data = $request->json()->all();
        $validator = Validator::make(
            $data,
            [
                'trade_nit' => 'required|string',
                'buyer_name' => 'required|string',
                'buyer_email' => 'required|email',
                'trade_request_code' => 'required|string',
                'request_status' => 'required|integer',
                'transaction_cus' => 'required|string',
                'transaction_date_time' => 'required|date',
                'receiver_name' => 'required|string',
                'receiver_identification' => 'required|string',
                'receiver_phone' => 'required|string',
                'receiver_address' => 'required|string',
                'receiver_department_id' => 'required|integer',
                'receiver_country_id' => 'required|integer',
                'delivery_purpose' => 'required|integer',
                'delivery_type' => 'nullable|string',
                'delivery_extra_cost' => 'nullable|numeric',
                'delivery_extra_cost_tax' => 'nullable|numeric',
                'transport_type' => 'nullable|string',
                'transport_company' => 'nullable|string',
                'notes' => 'nullable|string',
                'coupon_id' => 'nullable|string',
                'coupon_name' => 'nullable|string',
                'coupon_value' => 'nullable|numeric',
                'coupon_date' => 'nullable|date',
                'coupon_currency' => 'nullable|string',
                'items' => 'required|array',
                'items.*.item' => 'required|integer',
                'items.*.part_num' => 'required|string',
                'items.*.product_name' => 'required|string',
                'items.*.brand' => 'required|string',
                'items.*.quantity' => 'required|integer',
                'items.*.unit_price' => 'required|numeric',
                'items.*.total_price' => 'required|numeric',
                'items.*.tax_value' => 'required|numeric',
                'items.*.currency' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors(), 'code' => 422,], 422);
        }

        // Generate the order attribute

        $order = Order::where('trade_nit', $data['trade_nit'])
            ->where('trade_request_code', $data['trade_request_code'])
            ->first();

        if (!$order) {
            $code = 201;
            $message = "created";
            $maxOrder = Order::max('order_number');
            $data['order_number'] = $maxOrder + 1;
            // Create the order
            $order = Order::create($data);
            // Create the order items
            foreach ($data['items'] as $item) {
                $item['order_number'] = $data['order_number'];
                $order->items()->create($item);
            }
        } else {
            $code = 200;
            $message = "updated";
            $data['order_number'] = $order->order_number;
            $order = Order::updateOrCreate(['trade_nit' => $data['trade_nit'], 'trade_request_code' => $data['trade_request_code'],], $data);
            //$order->save();
            $itemIds = [];
            foreach ($data['items'] as $itemData) {
                $itemData['order_number'] = $order->order_number;
                $item = OrderItem::updateOrCreate(['order_number' => $itemData['order_number'], 'item' => $itemData['item']], $itemData);
                $itemIds[] = $item->id;
            } // Remove items that are not in the new data
            $order->items()->whereNotIn('id', $itemIds)->delete();
        }

        //report the order
        app(MailController::class)->sendOrderMail($trade->email, $order->order_number);

        //app(MailController::class)->sendSkuMail($request, "09314-3208");

        return response()->json([
            'message' => "Order {$message} successfully",
            'order_number' => $order->order_number,
            'buyer_name' => $order->buyer_name,
            'buyer_email' => $order->buyer_email,
            'trade_request_code' => $order->trade_request_code,
            'transaction_date_time' => $order->transaction_date_time,
            'code' => $code,
        ], $code);
    }

    public function getTradePeriodOrders(string $trade, string $year, string $month) //{trade}/{year}/{month}', '
    {
        if (!$trade) {
            return response()->json([
                'message' => 'invalid order data',
                'code' => 500,
            ], 500);
        }

        $orders = Order::where('trade_nit', "{$trade}")
            ->when($year !== "all" && $year !== "0", function ($query) use ($year) {
                $query->whereYear('transaction_date_time', $year);
            })
            ->when($month !== "all"  && $month !== "0", function ($query) use ($month) {
                $query->whereMonth('transaction_date_time', $month);
            })
            ->orderBy('order_number', 'DESC')
            ->get();
        if (count($orders) == 0) {
            return response()->json([
                'message' => "Error Order not found",
                'code' => 404,
            ], 404);
        }
        return response()->json($orders->toArray(), 200);
    }
}
