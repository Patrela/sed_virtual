<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\FileController;
use App\Services\ProductService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class ProductController extends Controller
{

    /**
     * Display a listing of the products by department
     * @group string. main group clasification
     */

     protected $productService; // Define the property

     public function __construct(ProductService $productService)
     {
         $this->productService = $productService; // Initialize the property
     }
 
     public function productsByDepartment($group = "Computadores")
     {
         return $this->productService->productsByDepartment($group);
     }
 
     public function searchProductsByText($searchText)
     {
         return $this->productService->searchProductsByText($searchText);
     }

     public function validateProductImageUrls()
     {
        return $this->productService->validateProductImageUrls();

     }

    public function searchProductBySku(string $sku = '')
    {
        return $this->productService->searchProductBySku($sku);
    }

    public function validateProductImagesUrls()
    {   return $this->productService->validateProductImagesUrls();
    }

}
