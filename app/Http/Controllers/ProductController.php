<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


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
        'image_4'
    ];

    /**
     * Display a listing of the products by department
     * @group string. main group clasification
     */
    public function getDepartmentProducts($group = "Computadores")
    {
        //Log::info("products GET  " . $group);
        $products = Product::where('department', "{$group}")
            ->when($group !== "", function ($query) {
                    $query->where('is_discontinued', 0);
                })
            ->select($this->selectFields)
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();
        return $products;
    }

    public function getBrandProducts($group, $brands)
    {
        // Explode the comma-separated list of brands into an array
        $brandsArray = explode(',', $brands);

        // Build the query for searching by multiple brands
        $query = Product::whereIn('brand', $brandsArray)
            ->when($group !== "", function ($query) use ($group) {
                $query->where('department', "{$group}");
            })
            ->when($group !== "", function ($query) {
                $query->where('is_discontinued', 0);
            });

        $products = $query->select($this->selectFields)
            ->orderBy('brand', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();

        return $products;
    }

    public function getCategoriesProducts($group, $categories)
    {
        // Explode the comma-separated list of brands into an array
        $catsArray = explode(',', $categories);

        // Build the query for searching by multiple brands
        $query = Product::whereIn('category', $catsArray)
            ->when($group !== "", function ($query) use ($group) {
                $query->where('department', "{$group}");
            })
            ->when($group !== "", function ($query) {
                $query->where('is_discontinued', 0);
            });

        $products = $query->select($this->selectFields)
            ->orderBy('category', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();

        return $products;
    }

    public function getSegmentProducts($group)
    {
        // Build the query for searching by multiple brands
        $query = Product::where('segment', $group)
                        ->when($group !== "", function ($query) {
                            $query->where('is_discontinued', 0);
                        });

        $products = $query->select($this->selectFields)
            ->orderBy('department', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->when($group !== "", function ($query) {
                $query->where('is_discontinued', 0);
            })->get();

        return $products;
    }

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
    public function index()
    {
        app(SedController::class)->getProviderProducts();
        $totalproducts = Cache::remember('products', now()->addMinutes(30), function () {
            return Product::where('is_discontinued', 0)->get();
        });
        /*
        $page =  $this->CurrentPage(); */
        $perPage = 12;


        // Mostrar los primeros productos
        $products = $totalproducts->select($this->selectFields)->take($perPage);
        //dd($products->select('part_num','name','stock_quantity','regular_price','image_1','sku'));
        return view('product.index',  ['products' => $products, 'perPage' => $perPage, 'page' => 1, 'total' => count($totalproducts)]);
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
            $product=  $this->searchSpecialSku( $word);
            if ($product) return $product;
        }

        foreach ($words as $word) {
            // Build LIKE query with case-insensitive search using `LOWER` function
            $productQuery = Product::select($this->selectFields)
                ->when($word !== "", function ($query) use ($word) {
                    $query->whereRaw("LOWER(name) LIKE ?", ["%{$word}%"]);
                })
                ->when($words !== "", function ($query) {
                    $query->where('is_discontinued', 0);
                });

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

    /*
    public function toExcel($sku)
    {
        return Excel::download(new ProductsExport($sku), 'product_' . $sku . '.xlsx');
    }
    */

    public function toCsv(Request $request)
    {
        try {
            $sku = ($request->has('sku')) ? $request->input('sku') : '';
            $full_path = ($request->has('filepath')) ? $request->input('filepath') : '';
            //$token = $request->bearerToken();
            Log::error("CSV.  final file = " . $full_path . " sku: " . $sku);
            return response()->json([
                'result' => 'It works!',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }

    public function abilities(Request $request)
    {

        $abilities = $request->user()->tokens()->pluck('abilities'); // Get ability names ;
        return response()->json($abilities);
    }

    public function searchSpecialSku( string $sku = '' )
    {
        if($sku == '')
        {
            return collect(); // null;
        }
        $product = Product::select($this->selectFields)
            ->when($sku !== "", function ($query) use ($sku) {
                $query->where("part_num",  "{$sku}");
            })
            -> get(); //first()

        if (count($product) == 0) {
            $product = Product::select($this->selectFields)
                ->when($sku !== "", function ($query) use ($sku) {
                    $query->whereRaw("LOWER(part_num) LIKE ?", ["{$sku}%"]);
                })
                ->get(); //->first();
        }
        return $product;
    }

}


