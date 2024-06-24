<?php

namespace App\Http\Controllers;

use console;
use App\Models\User;
use App\Models\Trade;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Arr;
use App\Models\UserImported;
use Illuminate\Http\Request;

use App\Models\FailedProduct;
use App\Models\ProductImported;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isEmpty;

class SedController extends Controller
{
    /**
     * update SED clasifications
     */
    public function syncProductGroups()
    {
        if (!app(LogController::class)->keyInCache('clasifications')) {
            // 4 SED clasification groups
            $groups = [
                'departments' => 'departamento',
                'categories' => 'categoria',
                'segments' => 'segmento',
                'brands' => 'marca'
            ];

            foreach ($groups as $key => $group) {
                // execute the API with SED clasification group
                try {
                    if( env('APP_ENV') === 'production' ) {
                        $response = Http::sedprod()->post('/' . $key . '/', ['item' => '',]);
                    } else {
                        $response = Http::seddev()->post('/' . $key . '/', ['item' => '',]);
                    }

                    if ($response->successful()) {
                        $jsonResponse = $response->json();
                        // if($key == 'departments')
                        //     $clasifications = $jsonResponse['departaments']['departaments'];
                        // else
                        $clasifications = $jsonResponse[$key][$key];
                        //only insertion from new clasisifications
                        $this->createClasification($clasifications);
                    } else {
                        // Handle non-successful response (e.g., 4xx or 5xx status codes)
                        Log::error("Error during API Imort Products " . $key . " request");
                        // return response()->json([
                        //     'error' => "Error during API " . $key . " request:",
                        //     'code' => config('services.api.dev') . "/" .$key ."/ " .$response->status(),
                        // ], 403);
                    }

                    // store clasificacion in cache
                    $groupdata = Category::where('group_name', "{$group}")
                        ->orderBy('parent_id', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();
                    //Cache::put($key, $groupdata, now()->addDays(7));
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ], 403);
                }
            }
            if (app(LogController::class)->isCache()) Cache::put("clasifications", "ok", now()->addDays(7));

            return response()->json([
                'result' => "Successfully imported. ",
                'code' => 200,
            ], 200);
        } else
            return response()->json([
                'state' => 'in cache',
                'clasifications' => Cache::get('clasifications'),
            ], 200);
    }


    /**
     * Read ordered clasification, if is higger than current, then insert new clasificacion
     */
    private function createClasification($groups)
    {
        $maingroup = $groups[0]['group_name'];
        $parentid =  "X"; // $groups[0]['parent_id'];
        $maxgroup = -1; //
        foreach ($groups as $group) {
            if ($parentid != $group['parent_id']) {
                $parentid = $group['parent_id'];
                //read the newest id for ignoring the rest of ids
                $maxgroup = Category::where([['group_name', "{$maingroup}"], ['parent_id', $parentid]])
                    ->max('id') ?? 0;
                //Log::error("grupo " .$maingroup . " parent " .$parentid ." max " .$maxgroup);
            }
            if ($group['id'] > $maxgroup) {
                //Log::error("cat id " . $group['id'] . "  name " . $group['name'] . "  parent " . $group['parent_id'] . "  group_name " . $group['group_name'] . "  slug " . $group['slug']);
                $group['parent_id'] = $parentid;
                Category::create($group);
            }
        }
    }

    /**
     * @return SED Products from a Department
     */
    public function syncDepartmentProducts(string $department)
    {
        try {
            if( env('APP_ENV') === 'production' ) {
                $response = Http::sedprod()->post('/products/', [ 'department' => $department,]);
            } else {
                $response = Http::seddev()->post('/products/', [ 'department' => $department,]);
            }


            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json([
                    'error' => 'Api SED Department Products',
                    'code' => $response->status(),
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }

    /**
     * validate user profile from customers B2B
     * @param trade Customer B2B
     * @param email Customer contact email
     * @return validated user data
     */
    public function validateCustomerUser(Request $request)
    {
        try {

            $company = $request->header('x-api-company');
            $useremail = $request->header('x-api-user');
            //$token = $request->bearerToken();
            $name = $request->query('name');

            if( env('APP_ENV') === 'production' ) {
                $response = Http::sedprod()->post('/authentication/?name=' .$name, [
                    'nit' => $company,
                    'email' =>  $useremail,
                ]);
            } else {
                $response = Http::seddev()->post('/authentication/?name=' .$name, [
                    'nit' => $company,
                    'email' =>  $useremail,
                ]);
            }

            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json([
                    'error' => 'Api SED Customer User',
                    'code' => $response->status(),
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }


    public function updateNewUsers()
    {
        Log::info("Starting Authentication update");
        try {
            $newUsers = User::where('password','')->get();
            foreach($newUsers as $user) {
                $user['password'] = Hash::make($user['user_id']);
                //$user['remember_token'] = Hash::make($user['name']);
                $user->save();
            }
            Log::info("Ending Authentication update");
            return response()->json([
                'result' => 'SED New Users updated',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error during API Staff New Users register: {$e->getMessage()}");
            return response()->json([
                'result' => 'error ' .$e->getCode(),
                'message' => $e->getMessage(),
            ], 401);
        }

    }

    public function getStaffUsers()
    {
        try {

            if( env('APP_ENV') === 'production' ) {
                $response = Http::sedprod()->post('/staff/', [ 'email' => '',]);
            } else {
                $response = Http::seddev()->post('/staff/', [ 'email' => '',]);
            }
            if ($response->successful()) {
                //return $response->json();
                $jsonResponse = $response->json();
                $staff = $jsonResponse['staff']['staff'];
                //Log::info($customers);

                $itemsKey= "|";
                $staffData = [];
                UserImported::truncate();
                foreach ($staff as $customer) {
                    //Log::info("customer nit.  " . $customer["customer_nit"] . " email " . $customer["contact_email"]);
                    $key = "{$customer['contact_email']}";
                    if(!strpos($itemsKey, "|" .$key ."|" )) {
                        $itemsKey = $itemsKey .$key  ."|";
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
                    Log::info("Exec SED API Staff Users Imported = " .UserImported::all()->count() ." data =" .count($staffData) );
                }
                DB::select("CALL sp_import_users(?)",[User::ALLROLES["Staff"] ]);
                return response()->json([
                    'result' => 'SED Staff Users updated',
                    'code' => 200,
                ], 200);
            } else {
                return response()->json([
                    'result' => 'error ' . $response->status(),
                    'message' => json_encode($response->json()),
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error during API Staff Users request: {$e->getMessage()}");
            return response()->json([
                'result' => 'error ' .$e->getCode(),
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function getTradeUsers()
    {
        try {

            if( env('APP_ENV') === 'production' ) {
                $response = Http::sedprod()->post('/authentication/', [ 'nit' => '',]);
            } else {
                $response = Http::seddev()->post('/authentication/', [ 'nit' => '',]);
            }
            if ($response->successful()) {
                //return $response->json();
                $jsonResponse = $response->json();
                $tradesusers = $jsonResponse['customers']['customers'];
                //Log::info($customers);

                $itemsKey= "|";
                $tradesData = [];
                UserImported::truncate();
                foreach ($tradesusers as $customer) {
                    //Log::info("customer nit.  " . $customer["customer_nit"] . " email " . $customer["contact_email"]);
                    $key = "{$customer['contact_email']}";
                    if(!strpos($itemsKey, "|" .$key ."|" )) {
                        $itemsKey = $itemsKey .$key  ."|";
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
                    Log::info("Exec SED API Trade Users Imported = " .UserImported::all()->count() ." data =" .count($tradesData) );
                }
                DB::select("CALL sp_import_users(?)",[User::ALLROLES["Trade"] ]);
                return response()->json([
                    'result' => 'SED Trade Users updated',
                    'code' => 200,
                ], 200);
            } else {
                return response()->json([
                    'result' => 'error ' . $response->status(),
                    'message' => json_encode($response->json()),
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error("Error during API  Trade Users request: {$e->getMessage()}");
            return response()->json([
                'result' => 'error ' .$e->getCode(),
                'message' => $e->getMessage(),
            ], 401);
        }

    }

    public function getProviderProducts($idProvider = 1)
    {
        if (!app(LogController::class)->keyInCache('sync_products') ){
            //$Provider = Provider::where('nit', '8300361083')->first(); //SED_PROVIDER
            //$idProvider = ($Provider) ? $Provider->id : 1;
            session(['lastUpdated' =>  date('d/m/Y H:i:s')]);

            try {

                if( env('APP_ENV') === 'production' ) {
                    $response = Http::sedprod()->post('/products/', [
                        'department' => '',
                    ]);
                } else {
                    $response = Http::seddev()->post('/products/', [
                        'department' => '',
                        // 'department' => 'Computadores',   // 'category' => 'Portátiles',
                        // 'brand' => 'LENOVO',
                        // 'segment' => 'Hogar'
                    ]);
                }

                // Check if the response was successful (status code 2xx)
                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    $products = $jsonResponse['products']['products'];
                    $productData = [];
                    $itemsKey= "|";
                    ProductImported::truncate();
                    foreach ($products as $product) {
                        $key = "{$product['part_num']}";
                        if(!strpos($itemsKey, "|" .$key ."|" ))
                        {
                            $itemsKey = $itemsKey .$key  ."|";
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
                                'is_new' =>0,
                            ];

                            // Insert in batches of 100
                            if (count($productData) === 100) {
                                //Log::info($itemsKey);
                                //Log::info($productData);
                                ProductImported::insert($productData);
                                //Log::info("SED API Product Imported = " .ProductImported::all()->count() ." data =" .count($productData) );
                                $productData = [];
                            }

                        }
                    }

                    // Insert any remaining records
                    if (!empty($productData)) {
                        //Log::info($itemsKey);
                        //Log::info($productData);
                        ProductImported::insert($productData);
                        Log::info("SED API Product Imported = " .ProductImported::all()->count() ." data =" .count($productData) );
                    }
                    DB::select("CALL sp_import_products()");

                    if (app(LogController::class)->isCache()) Cache::put('sync_products', $response->status(), now()->addMinutes(30));

                    return response()->json([
                        'result' => 'ok',
                        'message' => 'Imported Succcessfully',
                    ], 200);
                } else {
                    return response()->json([
                        'result' => 'error ' . $response->status(),
                        'message' => json_encode($response->json()),
                    ], $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Error during API request: {$e->getMessage()}");
                return response()->json([
                    'result' => 'error ' .$e->getCode(),
                    'message' => $e->getMessage(),
                ], 401);
            }
        } else {
            // return Cache::get('sync_products');
            return response()->json([
                'result' => 'ok',
                'message' => Cache::get('sync_products'),
            ], 200);
        }
    }
}
