<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;

class OrderController extends Controller
{

    public function index()
    {
        $orders_object = DB::table('view_orders')
                            ->limit(80) // last 80 orders
                            ->get();
        $orders = app(MaintenanceController::class)->object_to_array($orders_object);
        return view('order.list', ['orders' => $orders,  'profile_list' => array_flip(User::ALLROLES) , 'rolevalue' => User::ALLROLES["OrderManager"]]);

        //return $orders;        
        //return response()->json($orders->toArray(), 200);
    }

    public function show(string $order)
    {
        if (!$order) {
            return response()->json([
                'message' => 'invalid order data',
                'code' => 500,
            ], 500);
        }
        $order = Order::with('items')->where('order_number', $order)->first();

        //if (count($order) == 0) {
        if ($order == null) {
            return response()->json([
                'message' => "Error Order not found",
                'code' => 404,
            ], 404);
        }
        //log::info("orders = " ,$order->toArray());
        return response()->json($order, 200);
    }

    public function createOrUpdateOrder(Request $request, string $username = "standard")
    {

        $trade = app(ConnectController::class)->connectValidation($request, $username);
        $tradeData = json_decode($trade->getContent(), true);
        //Log::info("data: ",$tradeData);

        if (isset($tradeData['nit'])) {
            //Log::info("Paso nit");
            if ($tradeData['nit'] !== $request->input('trade_nit')) {
                return response()->json(['error' => 'Trade NIT error. Order not created',
                                    'nit' => $tradeData['nit'] . ' - ' . $request->input('trade_nit')], 401);
            }
        
        }        
        else {
            //Log::info("Paso nit else");
            return $trade;
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
                'items.*.trade_part_num' => 'required|string',
                'items.*.product_name' => 'required|string',
                'items.*.brand' => 'nullable|string',
                'items.*.quantity' => 'required|integer',
                'items.*.sed_unit_price' => 'required|numeric',
                'items.*.sed_total_price' => 'required|numeric',
                'items.*.sed_tax_value' => 'nullable|numeric',
                'items.*.is_tax_applied' => 'nullable|numeric',
                'items.*.currency' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error. Order not created', 'errors' => $validator->errors(), 'code' => 422,], 422);
        }

        // Generate the order attribute

        $order = Order::where('trade_nit', $data['trade_nit'])
            ->where('trade_request_code', $data['trade_request_code'])
            ->first();
        $products = 0;
        // Check if the product exists and has enough stock
        foreach ($data['items'] as $item) {
            //Log::info("item ",$item);
            //Log::info($item['part_num']);
            //PVR
            //$product= Product::find($item['part_num']);
            $product = Product::where('part_num', $item['part_num'])->first();
            //Log::info($product->part_num);
            if(!$product){
                return response()->json(['message' => 'Product not found. Order not created', 
                    'item' => $item['item'],
                    'part_num' => $item['part_num'],
                    'code' => 403], 403);
            }
            elseif($product->is_active==0){
                return response()->json(['message' => 'Product is not active. Order not created', 
                    'item' => $item['item'],
                    'part_num' => $item['part_num'],
                    'code' => 403], 403);
            }elseif( $product->stock_quantity < $item['quantity']){ 
                return response()->json(['message' => 'Product STOCK not enough. Order not created', 
                    'item' => $item['item'],
                    'part_num' => $item['part_num'],
                    'code' => 403], 403);
            }
            $products++;
        }
        
        if($products==0){
            return response()->json(['message' => 'No products in the order. Order not created', 'code' => 403], 403);
        }

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

        //PVR version final: MailController sendOrderMail
        //app(MailController::class)->sendSkuMail($request, "09314-3208");
        //app(MailController::class)->sendOrderMail($tradeData, $order);
        
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


    public function getTradePeriodOrders(string $trade, string $start, string $end)
    {
        log::info( "trade: " . $trade  ." start: " .$start ." end: ".$end);
        if (!$trade) {
            return response()->json([
                'message' => 'Invalid order data',
                'code' => 500,
            ], 500);
        }
        // $orders_object = DB::table('view_orders')
        // ->where('trade',  "{$trade}")
        // ->where('transaction_date_time', '>=', "{$start}")
        // ->where('transaction_date_time', '<=', "{$end}")    
        // ->get();

        $orders_object = DB::table('view_orders')
        ->where('nit', "{$trade}")
        ->where('transaction_date_time', '>=', DB::raw("CAST('{$start}' AS DATE)"))
        ->where('transaction_date_time', '<', DB::raw("CAST('{$end}' AS DATE) + INTERVAL 1 DAY"))
        ->get();
    
        $orders = app(MaintenanceController::class)->object_to_array($orders_object); 
        log::info("orders = ". count($orders));
        // Check if no orders were found
        if (count($orders) == 0) {
            return response()->json([
                'message' => 'Error: Trade Orders not found',
                'code' => 404,
            ], 404);
        }
    
        // Return the found orders
        return response()->json($orders, 200);
    }
    
    public function getPeriodOrders(string $start, string $end)
    {
        if (!$start || !$end) {
            return response()->json([
                'message' => 'Invalid order range data',
                'code' => 500,
            ], 500);
        }
    
        // Fetch orders based on the conditions
        $orders = Order::with('items')
            ->when(!empty($start), function ($query) use ($start) {
                $query->whereDate('transaction_date_time', '>=', $start); // Filter from the start date
            })
            ->when(!empty($end), function ($query) use ($end) {
                $query->whereDate('transaction_date_time', '<=', $end); // Filter up to the end date
            })
            ->orderBy('order_number', 'DESC')
            ->get();
    
        // Check if no orders were found
        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'Error: Order not found',
                'code' => 404,
            ], 404);
        }
    
        // Return the found orders
        return response()->json($orders, 200);
    }
    
}


