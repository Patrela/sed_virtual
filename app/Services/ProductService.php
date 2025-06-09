<?php

namespace App\Services;
use App\Models\Product;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FileController;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class ProductService.
 */
class ProductService
{
    /**
     * Campos seleccionados para consultas
     */
    protected $selectFields = [
        'part_num', 'name', 'stock_quantity', 'regular_price', 'price_tax_status',
        'currency', 'regular_price_cop', 'image_1', 'sku', 'unit', 'description',
        'department', 'category', 'segment', 'brand', 'attributes', 'guarantee',
        'contact_agent', 'contact_unit', 'dimension_length', 'dimension_width',
        'dimension_height', 'dimension_weight', 'image_2', 'image_3', 'image_4',
        'is_permanent_stock'
    ];

    /**
     * Campos seleccionados en consultas con JOIN
     */
    protected $selectJoinFields = [
        'part_num', 'name', 'stock_quantity', 'regular_price', 'price_tax_status',
        'currency', 'regular_price_cop', 'image_1', 'sku', 'unit', 'description',
        'department', 'category', 'segment', 'brand', 'attributes', 'guarantee',
        'contact_agent', 'contact_unit', 'dimension_length', 'dimension_width',
        'dimension_height', 'dimension_weight', 'image_2', 'image_3', 'image_4',
        'program_url', 'program_image', 'is_program_active', 'is_permanent_stock'
    ];

    /**
     * Lista estática de palabras comunes para mejorar filtrado en búsquedas
     */
    private static $commonWords = [
        'el', 'la', 'los', 'las', 'de', 'y', 'en', 'a', 'con', 'por',
        'para', 'un', 'una', 'del', 'al', 'es', 'se', 'que', 'lo'
    ];

    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * Obtener productos por departamento
     */
    public function productsByDepartment($group)
    {
        return $this->productRepository->productsByDepartment($group);
    }

  // All products searching
    // All products searching
    /**
     * Buscar productos por texto
     */
    public function searchProductsByText(string $searchText)
    {

        // Convertir consulta a minúsculas
        $lowercaseQuery = strtolower($searchText);

        // Separar las palabras y filtrar las más comunes
        $words = array_filter(preg_split('/\s+/', $lowercaseQuery), function ($word) {
            return !$this->isCommonWord($word);
        });
        //log::info("0- Searching product  Words= ". count($words)); 
        // Si es un SKU, buscar directamente
        if (count($words) === 1 && strlen($words[0]) > 4) {
            //return collect([$this->productRepository->searchProductBySku($words[0])]);
            return $this->productRepository->searchProductBySku($words[0]);
        }

        return $this->productRepository->searchProducts($words);
    }
    

    /**
     * Check if a word is common and should be excluded from search
     */    
    private function isCommonWord(string $word): bool
    {
        return in_array($word, self::$commonWords);
    }    


    public function searchProductBySku(string $sku = '')
    {
        return $this->productRepository->searchProductBySku($sku);
    }

    public function validateProductImagesUrls()
    {
        $invalidUrls = [];
        $corrections = [];
        $fields = ['part_num', 'name', 'department', 'category', 'stock_quantity','sale_price', 'currency', 'image_1', 'image_2', 'image_3', 'image_4'];

        // Control default process time
        app(MaintenanceController::class)->setExecutionTime(7000);

        // Fetch products with stock_quantity > 0
        $products = $this->productRepository->availableStock($fields);
        if (count($products) == 0) {
            return [
                'message' => "No products found with stock quantity greater than 0.",
                'code' => 204,
            ];
        }
        $records = 0;
        $fields = ['part_num', 'name', 'department', 'category', 'stock_quantity', 'sale_price', 'currency'];
        foreach ($products as $product) {
            $images = ['image_1', 'image_2', 'image_3', 'image_4'];
            $isValid = false;
            $records++;
            if ($records % 20 == 0) {
                log::info("{$records} - {$product->part_num}");
            }

            foreach ($images as $imageField) {
                $url = $product[$imageField];

                // Skip validation if the URL is empty or null
                if (empty($url)) {
                    continue;
                }

                // Validate URL
                try {
                    $response = Http::withoutVerifying()->timeout(1)->head($url);

                    if ($response->ok()) {
                        // If the URL is valid and it's not image_1, prepare correction
                        if ($product->image_1 !== $url) {
                            // version 1
                            $correction = [];
                            foreach ($fields as $field){
                                $correction[$field] = $product->{$field};
                            }
                            $correction['image_1']= $url;
                            $correction['is_updated'] = 1;

                            array_push($corrections, $correction);

                        }
                        $isValid = true;
                        //break; // No need to check further URLs for this product
                    }
                } catch (\Exception $e) {
                    // log::error("Error validating URL: {$url} - " . $e->getMessage());
                    continue;
                }
                if ($isValid) {
                    break; // No need to check further URLs for this product
                }
            }

            // If no valid URL is found, add the product to the invalidUrls array
            if (!$isValid) {
                $correction = [];
                foreach ($fields as $field){
                    $correction[$field] = $product->{$field};
                }
                $correction['image_1']= $product->image_1 ?? '';
                $correction['is_updated'] = 0;

                array_push($invalidUrls, $correction);                

            }
        }

        log::info("Total processed: {$records}  Corrections: " . count($corrections) . " Invalid URLs: " . count($invalidUrls));

        // Apply corrections to the database
        foreach ($corrections as $correction) {
            $this->productRepository->updateProductImage($correction['part_num'], $correction['image_1']);
        }

        // Merge corrections and invalid URLs
        $invalidUrls = array_merge($invalidUrls, $corrections);
        array_push($fields, 'image_1');
        array_push($fields, 'is_updated');

        // Control default process time restored
        app(MaintenanceController::class)->setExecutionTime();

        if (count($invalidUrls) == 0) {
            return [
                'message' => "No invalid URLs found.",
                'code' => 200,
            ];
        }

        return app(FileController::class)->saveArrayToCSV($fields, $invalidUrls, 'products_wrong_images.csv');
    }

    public function validateProductImageUrls()
    {

        // Control default process time
        app(MaintenanceController::class)->setExecutionTime(7000);

        $headers = ['sku', 'department', 'category', 'brand', 'name'];
        $images = ['image_1', 'image_2', 'image_3', 'image_4'];
        // Fetch products with active stock
        $productsAvailable = $this->productRepository->activeStock(array_merge( $headers, $images));
        $invalidUrlRecords = [];

        $attributes = array_keys($productsAvailable->first()->getAttributes());
        $attributes[]= 'correct_images';

        $headers = ['sku', 'department', 'category', 'brand', 'name'];
        $images = ['image_1', 'image_2', 'image_3', 'image_4'];

        foreach ($productsAvailable as $product) {
            $invalidImages = [];
            $imagesOk= 0;
            //Log::info("sku {$product->sku}");
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

    // public function getProductsByCategory(string $category = 'Computadores')
    // {
    //     return Product::leftJoin('affinities', function ($join) {
    //             $join->on('products.brand', '=', 'affinities.brand_name')
    //                  ->where('affinities.is_program_active', '=', '1');
    //         })
    //         ->where('products.category', "{$category}")
    //         ->where('products.is_active', 1)
    //         ->select($this->selectJoinFields)
    //         ->orderBy('products.name', 'ASC')
    //         ->get();
    // }

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
