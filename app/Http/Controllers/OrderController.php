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
        return view('order.list', ['orders' => $orders, 'profile_list' => array_flip(User::ALLROLES), 'rolevalue' => User::ALLROLES["OrderManager"]]);

        //return $orders;        
        //return response()->json($orders->toArray(), 200);
    }

    public function show(string $order)
    {
        if (!$order) {
            return response()->json([
                'message' => 'Datos no se pueden procesar',
                'code' => 500,
            ], 500);
        }
        $order = Order::with('items')->where('order_number', $order)->first();

        //if (count($order) == 0) {
        if ($order == null) {
            return response()->json([
                'message' => "Error: Orden no encontrada",
                'code' => 404,
            ], 404);
        }
        //log::info("orders = " ,$order->toArray());
        return response()->json($order, 200);
    }

    public function createOrUpdateOrder(Request $request, string $username = "standard")
    {
        $failedOrders = [];
        $error_code = 0;
        $products = 0;

        $trade = app(ConnectController::class)->connectValidation($request, $username);
        $tradeData = json_decode($trade->getContent(), true);
        // Log::info("data: ",$tradeData);
        $data = $request->json()->all();
        // Validate if the request body is empty
        if (empty($data)) {
            return response()->json([
                'message' => 'Request body no tiene datos',
                'code' => 400,
            ], 400);
        }

        if (isset($tradeData['nit'])) {
            // Log::info("Paso nit");
            if ($tradeData['nit'] !== $request->input('trade_nit')) {
                $failedOrders[] = [
                    'error' => 'Trade NIT mismatch',
                    'nit' => $tradeData['nit'] . ' - ' . $request->input('trade_nit'),
                    'name' => $tradeData['name'],
                    'email' => $tradeData['email'],       
                    'trade_request_code' => $data['trade_request_code'] ?? null,
                    'buyer_name' => $data['buyer_name']?? null,
                    'transaction_date_time' => $data['transaction_date_time']?? null,
                    'transaction_cus' => $data['transaction_cus']?? null,
                ];  
                $error_code = 401;              
                //return response()->json( $failedOrders[], 401);                               
            }

        } else {
            // Log::info("Paso nit else");
            //return $trade;
            $failedOrders[] = [
                'error' => 'Trade NIT no existe',
                'nit' => $request->input('trade_nit'),
                'name' =>  $request->input('trade_nit'),
                'email' => $request->header('X-Token-Auth'),
                'trade_request_code' => $data['trade_request_code'] ?? null,
                'buyer_name' => $data['buyer_name']?? null,
                'transaction_date_time' => $data['transaction_date_time']?? null,
                'transaction_cus' => $data['transaction_cus']?? null,
            ];   
            $error_code = 401;            
            //return response()->json( $failedOrders[], 401);             
        }


        // Validate the JSON body
       // $data = $request->json()->all();
       if($error_code == 0){
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
                $failedOrders[] = [
                    'error' => 'Error de validacion',
                    'nit' => $tradeData['nit'] ,
                    'name' => $tradeData['name'],
                    'email' => $tradeData['email'],
                    'trade_request_code' => $data['trade_request_code'] ?? null,
                    'buyer_name' => $data['buyer_name'],
                    'transaction_date_time' => $data['transaction_date_time'],
                    'transaction_cus' => $data['transaction_cus'],
                    'errors' => $validator->errors(),
                ];
                $error_code = 422;   
                //return response()->json($failedOrders, 422);       
            }
        }
        Log::info("error_code value ". $error_code);
        // Generate the order attribute
        if($error_code == 0){
            $order = Order::where('trade_nit', $data['trade_nit'])
                ->where('trade_request_code', $data['trade_request_code'])
                ->first();
            // Check if the product exists and has enough stock
            foreach ($data['items'] as $item) {
                //Log::info("item ",$item);

                $product = Product::where('part_num', $item['part_num'])->first();
                
                if (!$product) {
                    $failedOrders[] = [
                        'error' => 'Producto no encontrado',
                        'item' => $item['item'],
                        'part_num' => $item['part_num'],
                        'nit' => $tradeData['nit'] ,
                        'name' => $tradeData['name'],
                        'email' => $tradeData['email'],
                        'trade_request_code' => $data['trade_request_code'] ?? null,
                        'buyer_name' => $data['buyer_name'],
                        'transaction_date_time' => $data['transaction_date_time'],
                        'transaction_cus' => $data['transaction_cus'],
                    ];
                    $error_code = 402; 
                    continue;
                } elseif ($product->is_active == 0) {
                    $failedOrders[] = [
                        'error' => 'Product no activo',
                        'item' => $item['item'],
                        'part_num' => $item['part_num'],
                        'nit' => $tradeData['nit'] ,
                        'name' => $tradeData['name'],
                        'email' => $tradeData['email'],
                        'trade_request_code' => $data['trade_request_code'] ?? null,
                        'buyer_name' => $data['buyer_name'],
                        'transaction_date_time' => $data['transaction_date_time'],
                        'transaction_cus' => $data['transaction_cus'],
                    ];
                    $error_code = 402; 
                    continue;
                } elseif ($product->stock_quantity < $item['quantity']) {
                    $failedOrders[] = [
                        'error' => 'Stock insuficiente',
                        'item' => $item['item'],
                        'part_num' => $item['part_num'],
                        'nit' => $tradeData['nit'] ,
                        'name' => $tradeData['name'],
                        'email' => $tradeData['email'],
                        'trade_request_code' => $data['trade_request_code'] ?? null,
                        'buyer_name' => $data['buyer_name'],
                        'transaction_date_time' => $data['transaction_date_time'],
                        'transaction_cus' => $data['transaction_cus'],
                    ];
                    $error_code = 402; 
                    continue;
                } 
                // Log::info($product->part_num);
                $products++;
                
                
            }
        }
        if ($products == 0 && $error_code == 0) {
            $failedOrders[] = [
                'error' => 'No existen productos. No se crea la orden',
                'nit' => $tradeData['nit'] ,
                'name' => $tradeData['name'],
                'email' => $tradeData['email'],
                'trade_request_code' => $data['trade_request_code'] ?? null,
                'buyer_name' => $data['buyer_name'],
                'transaction_date_time' => $data['transaction_date_time'],
                'transaction_cus' => $data['transaction_cus'],  
            ];  
            $error_code = 403;           
        }
        // Send email for unprocessed orders
        if (!empty($failedOrders)) {
            app(MailController::class)->sendFailedOrdersMail( $failedOrders);
            return response()->json( $failedOrders, $error_code);
        } 

        if (!$order) {

            $code = 201;
            $message = "creado";
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
            $message = "actualizada";
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
       
        app(MailController::class)->sendOrderMail($tradeData, $order);

        return response()->json([
            'message' => "Orden {$message} procesada exitosamente",
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
        //log::info( "trade: " . $trade  ." start: " .$start ." end: ".$end);
        if (!$trade) {
            return response()->json([
                'message' => 'Datos de entrada no se pueden procesar',
                'code' => 500,
            ], 500);
        }

        $orders_object = DB::table('view_orders')
            ->where('nit', "{$trade}")
            ->where('transaction_date_time', '>=', DB::raw("CAST('{$start}' AS DATE)"))
            ->where('transaction_date_time', '<', DB::raw("CAST('{$end}' AS DATE) + INTERVAL 1 DAY"))
            ->get();

        $orders = app(MaintenanceController::class)->object_to_array($orders_object);
        // log::info("orders = ". count($orders));
        // Check if no orders were found
        if (count($orders) == 0) {
            return response()->json([
                'message' => 'Error: no hay ordenes asociadas al nit en el rango de fechas',
                'code' => 404,
            ], 404);
        }

        // Return the found orders
        return response()->json($orders, 200);
    }

    public function getPeriodOrdersFile(string $start, string $end, string $order, string $trade)
    {
        // log::info("parameters = " . $start . " - " . $end . " Order " . $order . " Trade " . $trade);
        if (!$start || !$end) {
            return response()->json([
                'message' => 'Rango de fechas no se pudo procesar',
                'code' => 500,
            ], 500);
        }

        $orders_object = DB::table('view_orders')
            ->when($order !== '0', function ($query) use ($order) {
                $query->where('n_order', "{$order}");
            })
            ->when($trade !== '0', function ($query) use ($trade) {
                $query->where('nit', "{$trade}");
            })
            ->where('transaction_date_time', '>=', DB::raw("CAST('{$start}' AS DATE)"))
            ->where('transaction_date_time', '<', DB::raw("CAST('{$end}' AS DATE) + INTERVAL 1 DAY"))
            ->get();

        if (count($orders_object) == 0) {
            return [
                'message' => "Error: no hay ordenes en el rango de fechas",
                'code' => 404,
            ];
        }

        $array_orders = app(MaintenanceController::class)->object_to_array($orders_object);

        $attributes = array_keys((array) $orders_object->first());
        // log::info("count = " . count($orders_object), $attributes);
        // Control default process time restored
        app(MaintenanceController::class)->setExecutionTime();

        //return $attributes;
        //return array_merge($attributes, $invalidUrlRecords);
        $output = app(FileController::class)->saveArrayToCSV($attributes, $array_orders, 'order' . $start . '_' . $end . '.csv');
        // log::info('output = ' . count($orders_object), $output);
        return $output;

    }

    public function getPeriodOrdersList(string $start, string $end, string $order, string $trade)
    {
        // log::info("parameters = " . $start . " - " . $end . " Order " . $order . " Trade " . $trade);
        if (!$start || !$end) {
            return response()->json([
                'message' => 'Rango de fechas no se pudo procesar',
                'code' => 500,
            ], 500);
        }
        $order = (!$order) ? '0' : $order;
        $trade = (!$trade) ? '0' : $trade;

        $orders_object = DB::table('view_orders')
            ->when($order !== '0', function ($query) use ($order) {
                $query->where('n_order', "{$order}");
            })
            ->when($trade !== '0', function ($query) use ($trade) {
                $query->where('nit', "{$trade}");
            })
            ->where('transaction_date_time', '>=', DB::raw("CAST('{$start}' AS DATE)"))
            ->where('transaction_date_time', '<', DB::raw("CAST('{$end}' AS DATE) + INTERVAL 1 DAY"))
            ->get();

            $orders = app(MaintenanceController::class)->object_to_array($orders_object);
            // log::info("orders = ". count($orders));
            // Check if no orders were found
            if (count($orders) == 0) {
                return response()->json([
                    'message' => 'Error: no hay ordenes en el rango de fechas',
                    'code' => 404,
                ], 404);
            }
    
            // Return the found orders
            return response()->json($orders, 200);

    }

}


