<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $selectFields = [
        'part_num',
        'name',
        'stock_quantity',
        'regular_price',
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
     * Display a listing of the resource.
     */
    public function main(string $group = "Computadores",  string $orderField = "name", string $ordered = "asc", string $category = "", string $brand = "", string $segment = "")
    {
        //$page= $this->CurrentPage();
        $products = $this->loadPageProducts($group,  $orderField,  $ordered, $category, $brand, $segment);
        //return view('product.index',  ['products' => $products, 'perPage' => $this->PerPage(), 'page' => $this->CurrentPage() , 'total'=> count($products)]);
    }

    public function loadPageProducts($group = "Computadores", string $orderField = 'name', string $ordered = 'asc', string $category = "", string $brand = "", string $segment = "")
    {
        if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache group  " . $group);
        } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache clear group  " . $group);
        }

        $products = Product::where('department', "{$group}")
            ->when($category !== "", function ($query) use ($category) {
                $query->where('category', "{$category}");
            })
            ->when($brand !== "", function ($query) use ($brand) {
                $query->where('brand', "{$brand}");
            })
            ->when($segment !== "", function ($query) use ($segment) {
                $query->where('segment', "{$segment}");
            })
            ->select($this->selectFields)
            ->orderBy($orderField, $ordered)
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();
        return $products;
    }

    public function getDepartmentProducts($group = "Computadores")
    {
        if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache group  " . $group);
        } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache clear group  " . $group);
        }

        $products = Product::where('department', "{$group}")
            ->select($this->selectFields)
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();
        return $products;
    }

    public function getBrandProducts($group)
    {
        if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache group  " . $group);
        } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache clear group  " . $group);
        }

        // Explode the comma-separated list of brands into an array
        $brands = explode(',', $group);

        // Build the query for searching by multiple brands
        $query = Product::whereIn('brand', $brands);

        $products = $query->select($this->selectFields)
            ->orderBy('brand', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();

        return $products;
    }


    public function getSegmentProducts($group)
    {
        if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache group  " . $group);
        } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
            Cache::put('group', $group);
            Cache::clear('products');
            // Log::error("cache clear group  " . $group);
        }


        // Build the query for searching by multiple brands
        $query = Product::where('segment', $group);

        $products = $query->select($this->selectFields)
            ->orderBy('department', 'ASC')
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();

        return $products;
    }

    public function getOrderProducts(string $group, string $order = "")
    {
        if ($order !== "") {
            if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
                $sedController = new SedController();
                $response = $sedController->syncProductsAPI();
                Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
                Cache::put('group', $group);
                Cache::clear('products');
                //Log::error("cache group  " . $group);
            } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
                Cache::put('group', $group);
                Cache::clear('products');
                // Log::error("cache clear group  " . $group);
            }


            // Build the query for searching by multiple brands
            $query = Product::where('department', $group)
                ->when($order == "price-plus", function ($query) {
                    $query->orderBy('regular_price', "DESC");
                })
                ->when($order == "price-less", function ($query) {
                    $query->orderBy('regular_price', "ASC");
                })
                ->when($order == "brands", function ($query) {
                    $query->orderBy('brand', "ASC");
                });

            $products = $query->select($this->selectFields)->get();
            Log::error("products Order No.  " .count($products) . " grupo = " . $group ." order = " .$order);
            return $products;
        }
        else{
            return [];
        }
    }
    public function getCategoryProducts($group, $category)
    {
        if (config('cache.enabled') && !Cache::has('sync_products_last_run') || !Cache::has('group')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
            Cache::put('group', $group);
            Cache::clear('products');
            //Log::error("cache group  " . $group);
        } elseif (config('cache.enabled') && Cache::get('group') !== $group) {
            Cache::put('group', $group);
            Cache::clear('products');
            // Log::error("cache clear group  " . $group);
        }


        // Build the query for searching by multiple brands
        $query = Product::where('department', $group)
            ->when($category !== "", function ($query) use ($category) {
                $query->where('category', "{$category}");
            });

        $products = $query->select($this->selectFields)
            ->orderBy('name', 'ASC')
            //->cacheTags(['products'])
            //->skip(($this->CurrentPage()-1) * $this->PerPage())->take($this->PerPage())
            ->get();
        //Log::error("products No.  " .count($products));
        return $products;
    }

    public function index()
    {
        // Obtener los productos desde la caché o la base de datos si no están en caché
        if (!Cache::has('sync_products_last_run')) {
            $sedController = new SedController();
            $response = $sedController->syncProductsAPI();
            Cache::put('sync_products_last_run', $response, now()->addMinutes(30));
        }
        $totalproducts = Cache::remember('products', now()->addMinutes(30), function () {
            return Product::all();
        });
        $page =  $this->CurrentPage();
        $perPage = $this->PerPage();


        // Mostrar los primeros productos
        $products = $totalproducts->select($this->selectFields)->take($perPage);
        //dd($products->select('part_num','name','stock_quantity','regular_price','image_1','sku'));
        return view('product.index',  ['products' => $products, 'perPage' => $perPage, 'page' => $page, 'total' => count($totalproducts)]);
    }

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
    public function loadPage(string $group = "Computadores",  string $orderField = "name", string $ordered = "asc", string $category = "", string $brand = "", string $segment = "")
    {
        $page = $this->AddPage();
        $products = $this->loadPageProducts($group,  $orderField,  $ordered, $category, $brand, $segment);
        // Return View Data
        return view('product.index',  ['products' => $products, 'perPage' => $this->PerPage(), 'page' => $this->CurrentPage(), 'total' => count($products)]);

        // return view('product.more', ['products' => $products, 'perPage' => $perPage, 'page' => $page]);
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

        foreach ($words as $word) {
            // Build LIKE query with case-insensitive search using `LOWER` function
            $productQuery = Product::select($this->selectFields)
                                 ->whereRaw("LOWER(name) LIKE ?", ["%{$word}%"]);

            // Optional: Include search in description as well
            // $productQuery->orWhereRaw("LOWER(description) LIKE ?", ["%{$word}%"]);

            // Execute the query and merge results
            $wordResults = $productQuery->get();
            $results = $results->merge($wordResults);
        }

        // Remove duplicates and maintain order
        $products = $results->unique('id');

        return $products;
    }


    // Método para verificar si una palabra es común
    private function isCommonWord($word)
    {
        $commonWords = ['el', 'la', 'los', 'las'];
        return in_array(Str::lower($word), $commonWords);
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

}
