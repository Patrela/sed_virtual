<?php

namespace App\Http\Controllers;

use console;
use App\Models\Product;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Models\FailedProduct;
use Illuminate\Support\Facades\Log;
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
        if (!Cache::has('departments')) {
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
                    $response = Http::sedfunc()->post('/' . $key . '/', [
                        'item' => '',
                    ]);
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
                        Log::error("Error during API " . $key . " request");
                        // return response()->json([
                        //     'error' => "Error during API " . $key . " request:",
                        //     'code' => config('services.api.erp') . "/" .$key ."/ " .$response->status(),
                        // ], 403);
                    }

                    // store clasificacion in cache
                    $groupdata = Category::where('group_name', "{$group}")
                        ->orderBy('parent_id', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();
                    Cache::put($key, $groupdata, now()->addDays(7));
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ], 403);
                }
            }
            return response()->json([
                'result' => "Successfully imported. ",
                'code' => 200,
            ], 200);
        } else
            return response()->json([
                'state' => 'in cache',
                'departments' => Cache::get('departments'),
            ], 200);
    }

    //clear cache flag for sync products
    public function clearProductCache()
    {
        Cache::clear('sync_products_last_run');
        return response()->json([
            'success' => true,
            'state' => 'cache cleared',
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
            $response = Http::sedfunc()->post('/products/', [
                'department' => $department,
            ]);
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
     * Read SED Products abd update in the local database. Create some, update stock and price to others
     */
    public function syncProductsAPI()
    {
        // obtain ID_PROVIDER
        if (!Cache::has('sync_products_last_run')) {
            $Provider = Provider::where('nit', '8300361083')->first(); //SED_PROVIDER
            $idProvider = ($Provider) ? $Provider->id : 2;
            session(['lastUpdated' =>  date('d/m/Y H:i:s')]);
            try {
                $response = Http::sedfunc()->post('/products/', [
                    'department' => '',
                    // 'department' => 'Computadores',
                    // 'category' => 'Portátiles',
                    // 'brand' => 'LENOVO',
                    // 'segment' => 'Hogar'
                ]);

                // 'department' => 'Computadores',
                // 'category' => 'Portátiles',
                // 'brand' => 'LENOVO',

                // 'department' => 'Accesorios',
                // 'category' => 'Cables',

                // 'department' => 'Electrodomésticos',
                // 'category' => 'Lavado y Secado',
                // 'brand' => 'LG',
                // 'segment' => 'Hogar'

                // 'department' => 'Servidores',
                // 'category' => 'Discos',
                // 'brand' => 'DELL',
                // 'segment' => 'Oficina'

                // Check if the response was successful (status code 2xx)
                if ($response->successful()) {
                    // reset review status
                    $this->updateSyncState($idProvider);
                    $jsonResponse = $response->json();
                    $products = $jsonResponse['products']['products'];

                    foreach ($products as $product) {
                        $this->createOrUpdateProducts($idProvider, $product);
                    }
                    // identify offline products
                    Product::where('id_provider', $idProvider)
                        ->where('is_reviewed', 0)
                        ->update(['is_discontinued' => 1, 'stock_quantity' => 0]);
                    // reset review status
                    $this->updateSyncState($idProvider);
                    return response()->json([
                        'estado' => 'ok',
                        'message' => 'Imported Succcessfully',
                    ], 200);
                } else {
                    // Handle non-successful response (e.g., 4xx or 5xx status codes)
                    $this->errorPrint('' . $response->status(), json_encode($response->json()));
                }
            } catch (\Exception $e) {
                // Log the error
                Log::error("Error during API request: {$e->getMessage()}");
                // Handle the error as needed (e.g., store failed records)
                return $this->errorPrint('API error', $e->getMessage());
            }

            Cache::put('sync_products_last_run', $response->status(), now()->addMinutes(30));
        } else {
            return Cache::get('sync_products_last_run');
        }
    }

    /**
     * start or end of the syncronization  process. Mark each product as not reviewed
     */
    private function updateSyncState($idProvider)
    {
        Product::where('id_provider', $idProvider)
            ->where('is_reviewed', 1)
            ->update(['is_reviewed' => 0]);
    }

    /**
     * each product from the provider is searched in the database. If exists, updates price and stock, otherwise, creates new product
     * @param int $idProvider: id of the syncronization provider
     * @param Product $product
     */
    private function createOrUpdateProducts($idProvider, $product)
    {
        try {
            $existingProduct = Product::where('part_num', $product['part_num'])
                ->where('id_provider', $idProvider)
                ->first();

            // If the product doesn't exist, create it
            if (!$existingProduct) {
                $product['id_provider'] = $idProvider;
                //$product['stock_quantity'] = $product['stock_quantity'];
                //if (strlen($product['short_description'] )> 200) $product['short_description'] = substr($product['short_description'],0,200);
                $product['is_reviewed'] = 1;
                $product['is_insale'] = 0;
                $product['is_sold'] = 0;
                $product['is_discontinued'] = 0;
                $product['is_reserved'] = 0;
                Product::create($product);
                //$this->errorPrint($product['part_num'], substr($product['name'], 0, 100));
            } else {
                // update states from rules
                $existingProduct->is_reviewed = 1;
                if ($product['regular_price'] < $existingProduct->regular_price) {
                    $existingProduct->is_insale = 1;
                    $existingProduct->sale_price = $product['regular_price'];
                } elseif ($product['regular_price'] > $existingProduct->regular_price) {
                    $existingProduct->is_insale = 0;
                }
                $existingProduct->regular_price = $product['regular_price'];
                $existingProduct->stock_quantity = (int)$product['stock_quantity'];
                $existingProduct->save();
            }
        } catch (\Exception $e) {
            // Store the failed record
            Log::error("Error during product creation request: {$product['part_num']}");
            $this->errorPrint($product['part_num'], $e->getMessage());
        }
    }

    /**
     * Its the blueprint for part_num that could not be imported in the database
     */
    private function errorPrint($partNum, $errorMessage)
    {
        $imporerror = FailedProduct::create([
            'part_num' => $partNum,
            'error_message' => isEmpty($errorMessage) ? "" : $errorMessage
        ]);
        return response()->json([
            'part_num' => $imporerror->part_num,
            'error_message' => $imporerror->error_message,
        ], 403);
    }
}
