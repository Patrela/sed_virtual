<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class ProductController extends Controller
{
    /**
     * list of public fields for product
     */
    protected $selectFields = [
        'part_num',
        'name',
        'stock_quantity',
        'regular_price',
        'price_tax_status',
        'currency',
        'regular_price_cop',
        'image_1',
        'sku',
        'unit',
        'description',
        'department',
        'category',
        'segment',
        'brand',
        'attributes',
        'guarantee',
        'contact_agent',
        'contact_unit',
        'dimension_length',
        'dimension_width',
        'dimension_height',
        'dimension_weight',
        'image_2',
        'image_3',
        'image_4',
        'is_permanent_stock'
    ];

    protected $selectJoinFields = [
        'part_num',
        'name',
        'stock_quantity',
        'regular_price',
        'price_tax_status',
        'currency',
        'regular_price_cop',
        'image_1',
        'sku',
        'unit',
        'description',
        'department',
        'category',
        'segment',
        'brand',
        'attributes',
        'guarantee',
        'contact_agent',
        'contact_unit',
        'dimension_length',
        'dimension_width',
        'dimension_height',
        'dimension_weight',
        'image_2',
        'image_3',
        'image_4',
        'program_url',
        'program_image',
        'is_program_active',
        'is_permanent_stock'
    ];

    /**
     * Display a listing of the products by department
     * @group string. main group clasification
     */

    public function getDepartmentProducts($group = "Computadores")
    {
        //Log::info("products GET  " . $group);

        $productsAffinities = Product::leftJoin('affinities', function ($join) {
            $join->on('products.brand', '=', 'affinities.brand_name')
                ->where('affinities.is_program_active', '=', '1');
        })
            ->where('products.department', "{$group}")
            ->where('products.is_active', 1)
            ->select($this->selectJoinFields)
            ->orderBy('products.name', 'ASC')
            ->get();

        return $productsAffinities;
    }


    public function getWrongUrlImageProducts()
    {

        // Control default process time
        app(MaintenanceController::class)->setExecutionTime(7000);


        $productsAvailable = Product::select('sku', 'department', 'category', 'brand', 'name', 'image_1', 'image_2', 'image_3', 'image_4')
            ->where('products.is_active', 1)
            //->where('sku', '43HT3WJ-B.AWC') //WT13DPBK.ASFECOL
           // ->where('brand', 'LG')
            //->orderBy('products.name', 'ASC')
            ->get();

        $invalidUrlRecords = [];

        $attributes = array_keys($productsAvailable->first()->getAttributes());
        $attributes[]= 'correct_images';

        $headers = ['sku', 'department', 'category', 'brand', 'name'];
        $images = ['image_1', 'image_2', 'image_3', 'image_4'];

        foreach ($productsAvailable as $product) {
            $invalidImages = [];
            $imagesOk= 0;
            Log::info($product->sku); //Log::info("sku {$product->sku}");
            foreach ($headers as $field) {
                $invalidImages[$field] = "{$product->{$field}}";
            }

            foreach ($images as $field) {

                $url = $product->{$field};

                // Skip if the field is null or empty
                if (empty($url)) {
                    $invalidImages[$field] = 'null';
                    continue;
                }

                // Validate URL by checking response status
                try {
                    $response = Http::timeout(1)->head($url);
                    $invalidImages[$field] = $response->ok() ? '' : $url;
                } catch (\Exception $e) {
                    $invalidImages[$field] = $url; // URL is invalid or not reachable
                }

                if ($invalidImages[$field] === ''){
                    $imagesOk++;
                }
            }

            if (str_contains($product->name, "\""))
            {
                $invalidImages['name']=  str_replace("\"", '\'', $product->name);
            }

            $invalidImages['correct_images'] = $imagesOk;
            $invalidUrlRecords[] = $invalidImages;
            //Log::info($product->sku,$invalidImages);
        }

        // Control default process time restored
        app(MaintenanceController::class)->setExecutionTime();

        //return $attributes;
        //return array_merge($attributes, $invalidUrlRecords);

        return app(FileController::class)->saveArrayToCSV($attributes, $invalidUrlRecords, 'products_image_url.csv');


    }

    // All products searching
    public function getSearchProducts(string $searchText)
    {

        // Convert query to lowercase for case-insensitive search
        $lowercaseQuery = strtolower($searchText);

        // Split the query into words, filtering out common words
        $words = array_filter(preg_split('/\s+/', $lowercaseQuery), function ($word) {
            return !$this->isCommonWord($word);
        });

        $results = collect();
        //read PART_NUM
        if (count($words) == 1 && strlen($words[0]) > 4) {
            $word = $words[0];
            $product =  $this->searchProductBySku($word);
            if ($product) return $product;
        }

        foreach ($words as $word) {
            // Build LIKE query with case-insensitive search using `LOWER` function
            /*
            $productQuery = Product::select($this->selectFields)
                ->when($word !== "", function ($query) use ($word) {
                    $query->whereRaw("LOWER(name) LIKE ?", ["%{$word}%"]);
                })
                ->when($words !== "", function ($query) {
                    $query->where('is_active', 1);
                });
            */

                $productQuery = Product::leftJoin('affinities', function ($join) {
                    $join->on('products.brand', '=', 'affinities.brand_name')
                        ->where('affinities.is_program_active', '=', '1');
                })
                ->when($word !== "", function ($query) use ($word) {
                    $query->whereRaw("LOWER(name) LIKE ?", ["%{$word}%"]);
                })
                ->when($words !== "", function ($query) {
                    $query->where('is_active', 1);
                })
                    ->select($this->selectJoinFields)
                    ->orderBy('products.name', 'ASC');
                    //->get();


            // Execute the query and merge results
            $wordResults = $productQuery->get();
            $results = $results->merge($wordResults);
            //Log::error("search  " . $word . " Cant. " . count($wordResults) . " Total. " . count($results) );
        }

        // Remove duplicates , maintain order, and extract the index key. Return only values array
        $products =  $results->unique(); // $products =  $results;
        $products =  $products->values();

        // Log::error(" Total unique " . count($results) );
        return $products;
    }


    // Método para verificar si una palabra es común
    private function isCommonWord($word)
    {
        $commonWords = ['el', 'la', 'los', 'las'];
        return in_array(Str::lower($word), $commonWords);
    }

    public function searchProductBySku(string $sku = '')
    {
        if ($sku == '') {
            return collect(); // null;
        }
        /*
        $product = Product::select($this->selectFields)
            ->when($sku !== "", function ($query) use ($sku) {
                $query->where("part_num",  "{$sku}");
            })
            ->get(); //first()
        */
            $product = Product::leftJoin('affinities', function ($join) {
                $join->on('products.brand', '=', 'affinities.brand_name')
                    ->where('affinities.is_program_active', '=', '1');
                })
                ->select($this->selectJoinFields)
                ->when($sku !== "", function ($query) use ($sku) {
                    $query->where("part_num",  "{$sku}");
                })
                ->get(); //->first();
        if (count($product) == 0) {
            /*
            $product = Product::select($this->selectFields)
                ->when($sku !== "", function ($query) use ($sku) {
                    $query->whereRaw("LOWER(part_num) LIKE ?", ["{$sku}%"]);
                })
                ->get(); //->first();
            */
            $product = Product::leftJoin('affinities', function ($join) {
                $join->on('products.brand', '=', 'affinities.brand_name')
                    ->where('affinities.is_program_active', '=', '1');
                })
                ->select($this->selectJoinFields)
                ->when($sku !== "", function ($query) use ($sku) {
                    $query->whereRaw("LOWER(part_num) LIKE ?", ["{$sku}%"]);
                })
                ->get(); //->first();

        }
        return $product;
    }

    /*
    public function getSegmentProducts($group)
    {
        // Build the query for searching by multiple brands
        $query = Product::where('segment', $group)
                        ->when($group !== "", function ($query) {
                            $query->where('is_active', 1);
                        });

        $products = $query->select($this->selectFields)
            ->orderBy('department', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->when($group !== "", function ($query) {
                $query->where('is_active', 1);
            })->get();

        return $products;
    }
    */
    /*
    public function getOrderProducts(string $group, string $order = "")
    {
        if ($order !== "") {
            // Build the query for searching by multiple brands
            $query = Product::where("department", "{$group}")
                ->when($order == "price-plus", function ($query) {
                    $query->orderBy('regular_price', "DESC");
                })
                ->when($order == "price-less", function ($query) {
                    $query->orderBy('regular_price', "ASC");
                })
                ->when($order == "stock-plus", function ($query) {
                    $query->orderBy('stock_quantity', "DESC");
                })
                ->when($order == "stock-less", function ($query) {
                    $query->orderBy('stock_quantity', "ASC");
                })
                ->when($order == "brands", function ($query) {
                    $query->orderBy('brand', "ASC");
                });

            $products = $query->select($this->selectFields)->get();
            Log::error("products Order No.  " . count($products) . " grupo = " . $group . " order = " . $order);
            return $products;
        } else {
            return [];
        }
    }
    */
    /*
    public function loadMore()
    {
        $page = $this->AddPage();
        $perPage = $this->PerPage();

        $totalproducts = Cache::remember('products', now()->addMinutes(30), function () {
            return Product::all();
        });

        // Calcular el rango de productos para mostrar
        $startIndex = ($page - 1) * $perPage;
        $perPage = ($startIndex + $perPage > count($totalproducts)) ? count($totalproducts) - $startIndex : $perPage;
        $products = $totalproducts->slice($startIndex, $perPage);

        return view('product.more', ['products' => $products, 'perPage' => $perPage, 'page' => $page]);
    }

    public function CurrentPage()
    {
        $page = Cache::remember('current_page', now()->addMinutes(30), function () {
            return 1;
        });
        return $page;
    }

    public function AddPage(): int
    {
        $page = $this->CurrentPage();
        Cache::increment('current_page');
        return $page + 1;
    }
    public function PerPage(): int
    {
        return 30;
    }
    */


}
