<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\UserImported;
use App\Models\ProductImported;
use App\Models\CategoryImported;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class SedController extends Controller
{
    /**
     * update SED clasifications
     */
    public function getProductGroups()
    {
        // Clear and Update cache
        app(MaintenanceController::class)->clearCache();
        if (!Cache::has('clasifications')) {
            // 4 SED clasification groups
            $groups = [
                'departments' => 'departamento',
                'categories' => 'categoria',
                'segments' => 'segmento',
                'brands' => 'marca'
            ];

            // $groups = [
            //     'departments' => 'departamento'
            // ];

            foreach ($groups as $key => $group) {
                // execute the API with SED clasification group
                try {
                    $response = Http::connector()

                        ->post('/' . $key . '/', ['item' => '',]);
                    if ($response->successful()) {
                        //Log::info("Exec SED group = " . $group );
                        $jsonResponse = $response->json();
                        $clasifications = $jsonResponse[$key][$key];
                        //only insertion from new clasisifications
                        // $this->CreateGroups($clasifications);
                        $this->UpdateClassifications($clasifications, $group);
                    } else {
                        // Handle non-successful response (e.g., 4xx or 5xx status codes)
                        Log::error("Error during API Import clasifications " . $key . " request");
                        // return response()->json([
                        //     'error' => "Error during API " . $key . " request:",
                        //     'code' => config('services.api.dev') . "/" .$key ."/ " .$response->status(),
                        // ], 403);
                    }

                    // store clasificacion in cache
                    // $groupdata = Category::where('group_name', "{$group}")
                    //     ->orderBy('parent_id', 'asc')
                    //     ->orderBy('id', 'asc')
                    //     ->get();
                    //Cache::put($key, $groupdata, now()->addDays(7));
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ], 403);
                }
            }
            if ($response->successful()) {
                // update the status code is_active in reference to the current stock
                DB::select("CALL sp_insert_categories_from_products()");
                if (app(MaintenanceController::class)->isCache()) Cache::put("clasifications", "ok", now()->addDays(7));
                return response()->json([
                    'message' => "Successfully Product Groups and Categories imported. ",
                    'code' => 200,
                ], 200);
            } else {
                return response()->json([
                    'error' => "Error during API  request. " . json_encode($response->json()),
                    'code' => $response->status(),
                ], 403);
            }
        } else {
            return response()->json([
                'state' => 'Product Groups and Categories in cache',
                'clasifications' => Cache::get('clasifications'),
            ], 200);
        }
    }


    private function UpdateClassifications($groupItems, $maingroup)
    {
        // use temporal table to update classifications
        //$maingroup = $groupItems[0]['group_name'];
        $itemData = [];
        CategoryImported::truncate();

        foreach ($groupItems as $classification) {

            $itemData[] = [
                'id' => $classification["id"],
                'name' => $classification["name"],
                'slug' => $classification["slug"],
                'group_name' => $maingroup,
                'parent_id' => $classification["parent_id"],
                'is_active' => $classification["is_active"],
                'item_like' => $classification["item_like"],
                'is_new' => 0,
            ];
            // Insert in batches of 100
            if (count($itemData) === 100) {
                CategoryImported::insert($itemData);
                //Log::info("Exec SED API Classifications Imported = " . CategoryImported::all()->count() ." data =" .count($itemData) );
                $itemData = [];
            }
        }

        // Insert any remaining records
        if (!empty($itemData)) {
            CategoryImported::insert($itemData);
            //Log::info("Exec SED API Classification Imported = " . CategoryImported::all()->count() . " group ="  . $maingroup);
            //dd($itemData);
        }
        DB::select("CALL sp_categories_update(?)", ["{$maingroup}"]);

        return response()->json([
            'message' => 'SED Classification updated',
            'code' => 200,
        ], 200);
    }

    public function updateNewUsers()
    {
        Log::info("Starting updateNewUsers");
        //Log::stack(['single', 'slack'])->info('Starting Authentication update!');
        app(MaintenanceController::class)->setExecutionTime(7000);
        try {
            //$newUsers = User::where('password', '')->get();
            $newUsers = User::whereRaw('LENGTH(password) = 0')->get();
            Log::info("updateNewUsers processed = ". count($newUsers));
            foreach ($newUsers as $user) {
                $user['password'] = Hash::make($user['user_id']);
                //$user['remember_token'] = Hash::make($user['name']);
                $user->save();
            }
            //Log::info("Ending Authentication update");
            return response()->json([
                'message' => 'SED New Users updated',
                'total_users' =>count($newUsers),
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error during API Staff New Users register: {$e->getMessage()}");
            return response()->json([
                'message' => 'error ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 401);
        }
        finally{
            app(MaintenanceController::class)->setExecutionTime();
        }
    }
    public function updateStaffUsers()
    {
        //Log::info("Starting Authentication update");
        //Log::stack(['single', 'slack'])->info('Starting Authentication update!');
        app(MaintenanceController::class)->setExecutionTime(7000);
        try {
            $staff= User::ALLROLES["Staff"];
            DB::select("CALL sp_import_users({$staff})");

            //Log::info("Ending Authentication update");
            return response()->json([
                'message' => 'SED Staff Users updated',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error during API Staff Users update: {$e->getMessage()}");
            return response()->json([
                'message' => 'error ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 401);
        }
        finally{
            app(MaintenanceController::class)->setExecutionTime();
        }
    }
    public function getStaffUsers()
    {
        try {
            $response = Http::connector()->post('/staff/', ['email' => '',]);
            if ($response->successful()) {
                //return $response->json();
                $jsonResponse = $response->json();
                $staff = $jsonResponse['staff']['staff'];
                //Log::info($customers);

                $itemsKey = "|";
                $staffData = [];
                UserImported::truncate();
                foreach ($staff as $customer) {
                    //Log::info("customer nit.  " . $customer["customer_nit"] . " email " . $customer["contact_email"]);
                    $key = "{$customer['contact_email']}";
                    if (!strpos($itemsKey, "|" . $key . "|")) {
                        $itemsKey = $itemsKey . $key  . "|";
                        $staffData[] = [
                            'name' => $customer["contact_name"],
                            'email' => $key,
                            'role_type' => User::ALLROLES["Staff"],
                        ];
                        // Insert in batches of 100
                        if (count($staffData) === 100) {
                            //Log::info($itemsKey);
                            UserImported::insert($staffData);
                            //Log::info("Exec SED API User Staff Imported = " .UserImported::all()->count() ." data =" .count($staffData) );
                            $staffData = [];
                        }
                    }
                }
                // Insert any remaining records
                if (!empty($staffData)) {
                    //Log::info($itemsKey);
                    UserImported::insert($staffData);
                    Log::info("Exec SED API Staff Users Imported = " . UserImported::all()->count() . " data =" . count($staffData));
                }
                DB::select("CALL sp_import_users(?)", [User::ALLROLES["Staff"]]);
                return response()->json([
                    'message' => 'SED Staff Users updated',
                    'code' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'error ' . json_encode($response->json()),
                    'code' => $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error during API Staff Users request: {$e->getMessage()}");
            return response()->json([
                'message' =>  'error ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 401);
        }
    }

    public function getTradeUsers()
    {
        app(MaintenanceController::class)->setExecutionTime(7000);
        try {
            $response = Http::connector()->post('/authentication/', ['nit' => '',]);
            if ($response->successful()) {
                //return $response->json();
                $jsonResponse = $response->json();
                $tradesusers = $jsonResponse['customers']['customers'];
                //Log::info($customers);

                $itemsKey = "|";
                $tradesData = [];
                UserImported::truncate();
                foreach ($tradesusers as $customer) {
                    //Log::info("customer nit.  " . $customer["customer_nit"] . " email " . $customer["contact_email"]);
                    $key = "{$customer['contact_email']}";
                    if (!strpos($itemsKey, "|" . $key . "|")) {
                        $itemsKey = $itemsKey . $key  . "|";
                        $tradesData[] = [
                            'trade_name' => $customer["customer_name"],
                            'trade_nit' => $customer["customer_nit"],
                            'trade_id' => $customer["customer_number"],
                            'name' => $customer["contact_name"],
                            'email' => $key,
                            'role_type' => User::ALLROLES["Trade"],
                        ];
                        // Insert in batches of 100
                        if (count($tradesData) === 100) {
                            //Log::info($itemsKey);
                            UserImported::insert($tradesData);
                            //Log::info("Exec SED API User Trade Imported = " .UserImported::all()->count() ." data =" .count($tradesData) );
                            $tradesData = [];
                        }
                    }
                }
                // Insert any remaining records
                if (!empty($tradesData)) {
                    //Log::info($itemsKey);
                    UserImported::insert($tradesData);
                    Log::info("Exec SED API Trade Users Imported = " . UserImported::all()->count() . " data =" . count($tradesData));
                }
                DB::select("CALL sp_import_users(?)", [User::ALLROLES["Trade"]]);
                return response()->json([
                    'message' => 'SED Trade Users updated',
                    'code' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'error ' . json_encode($response->json()),
                    'code' =>  $response->status(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error during API  Trade Users request: {$e->getMessage()}");
            return response()->json([
                'message' => 'error ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 401);
        }
        finally{
            app(MaintenanceController::class)->setExecutionTime();
        }
    }

    public function getProviderProducts($idProvider = 1)
    {
        if (!Cache::has('sync_products')) {
            //$Provider = Provider::where('nit', '8300361083')->first(); //SED_PROVIDER
            //$idProvider = ($Provider) ? $Provider->id : 1;
            session(['lastUpdated' =>  date('d/m/Y H:i:s')]);

            try {
                $response = Http::connector()->post('/products/', [
                    'department' => '',
                ]);

                // Check if the response was successful (status code 2xx)
                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    $products = $jsonResponse['products']['products'];
                    $productData = [];
                    $itemsKey = "|";
                    $counter = 0;
                    ProductImported::truncate();
                    foreach ($products as $product) {
                        $key = "{$product['part_num']}";
                        if (!strpos($itemsKey, "|" . $key . "|")) {
                            $itemsKey = $itemsKey . $key  . "|";
                            $productData[] = [
                                'id_provider' => $idProvider,
                                'sku' => $key,
                                'part_num' => $key,
                                'name' => "{$product['name']}",
                                'slug' => "{$product['slug']}",
                                'description' => "{$product['description']}",
                                'short_description' => "{$product['short_description']}",
                                'stock_quantity' => $product['stock_quantity'],
                                'unit' => "{$product['unit']}",
                                'guarantee' => "{$product['guarantee']}",
                                'regular_price' => $product['regular_price'],
                                'sale_price' => $product['regular_price'],
                                'price_tax_status' => "{$product['price_tax_status']}",
                                'currency' => "{$product['currency']}",
                                'department' => "{$product['department']}",
                                'category' => "{$product['category']}",
                                'segment' => "{$product['segment']}",
                                'brand' => "{$product['brand']}",
                                'attributes' => "{$product['attributes']}",
                                'dimension_length' => $product['dimension_length'],
                                'dimension_width' => $product['dimension_width'],
                                'dimension_height' => $product['dimension_height'],
                                'dimension_weight' => $product['dimension_weight'],
                                'image_1' => "{$product['image_1']}",
                                'image_2' => "{$product['image_2']}",
                                'image_3' => "{$product['image_3']}",
                                'image_4' => "{$product['image_4']}",
                                'contact_unit' => "{$product['contact_unit']}",
                                'contact_agent' => "{$product['contact_agent']}",
                                'contact_email' => "{$product['contact_email']}",
                                'is_new' => 0,
                            ];
                            $counter += 1;
                            // Insert in batches of 100
                            if ($counter === 100) { //count($productData)
                                //Log::info($itemsKey);
                                //Log::info($productData);
                                ProductImported::insert($productData);
                                //Log::info("SED API Product Imported = " .ProductImported::all()->count() ." data =" .count($productData) );
                                $productData = [];
                                $counter = 0;
                            }
                        }
                    }

                    // Insert any remaining records
                    if ($counter > 0) { //!empty($productData)
                        //Log::info($itemsKey);
                        //Log::info($productData);
                        ProductImported::insert($productData);
                        //Log::info("SED API Product Imported = " .ProductImported::all()->count() ." data =" .count($productData) );
                    }

                    DB::select("CALL sp_import_products()");
                    if (app(MaintenanceController::class)->isCache()) Cache::put('sync_products', $response->status(), now()->addMinutes(30));

                    return response()->json([
                        'message' => 'Imported Succcessfully',
                        'code' => 200,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'error ' . json_encode($response->json()),
                        'code' =>  $response->status(),
                    ], $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Error during API request: {$e->getMessage()}");
                return response()->json([
                    'message' => 'error ' . $e->getMessage(),
                    'code' => $e->getCode(),
                ], 401);
            }
        } else {
            // return Cache::get('sync_products');
            return response()->json([
                'message' => Cache::get('sync_products'),
                'code' => 200,
            ], 200);
        }
    }
}
