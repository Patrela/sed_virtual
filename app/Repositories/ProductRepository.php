<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    protected $selectFields = [
        'part_num', 'name', 'stock_quantity', 'regular_price', 'price_tax_status',
        'currency', 'regular_price_cop', 'image_1', 'sku', 'unit', 'description',
        'department', 'category', 'segment', 'brand', 'attributes', 'guarantee',
        'contact_agent', 'contact_unit', 'dimension_length', 'dimension_width',
        'dimension_height', 'dimension_weight', 'image_2', 'image_3', 'image_4',
        'is_permanent_stock'
    ];

    protected $selectJoinFields = [
        'part_num', 'name', 'stock_quantity', 'regular_price', 'price_tax_status',
        'currency', 'regular_price_cop', 'image_1', 'sku', 'unit', 'description',
        'department', 'category', 'segment', 'brand', 'attributes', 'guarantee',
        'contact_agent', 'contact_unit', 'dimension_length', 'dimension_width',
        'dimension_height', 'dimension_weight', 'image_2', 'image_3', 'image_4',
        'program_url', 'program_image', 'is_program_active', 'is_permanent_stock'
    ];

    /**
     * Obtener productos por departamento
     */
    public function productsByDepartment($group = "Computadores")
    {
        return Product::leftJoin('affinities', function ($join) {
                $join->on('products.brand', '=', 'affinities.brand_name')
                     ->where('affinities.is_program_active', '=', '1');
            })
            ->where('products.department', $group)
            ->where('products.is_active', 1)
            ->select($this->selectJoinFields)
            ->orderBy('products.name', 'ASC')
            ->get();
    }

    /**
     * Buscar productos por palabras clave
     */
    public function searchProducts(array $words)
    {
        return Product::leftJoin('affinities', function ($join) {
                $join->on('products.brand', '=', 'affinities.brand_name')
                     ->where('affinities.is_program_active', '=', '1');
            })
            ->where(function ($query) use ($words) {
                foreach ($words as $word) {
                    $query->orWhereRaw("LOWER(name) REGEXP ?", ["\\b{$word}\\b"]);
                }
            })
            ->where('products.is_active', 1)
            ->select($this->selectJoinFields)
            ->orderBy('products.name', 'ASC')
            ->get()
            ->unique()
            ->values();

    }

    /**
     * Buscar producto por SKU
     */

    public function searchProductBySku(string $sku = '')
    {
        //log::info("1- Searching product by SKU: {$sku}");   
        if ($sku == '') {
            return collect(); // null;
        }

            $product = Product::leftJoin('affinities', function ($join) {
                $join->on('products.brand', '=', 'affinities.brand_name')
                    ->where('affinities.is_program_active', '=', '1');
                })
                ->select($this->selectJoinFields)
                ->when($sku !== "", function ($query) use ($sku) {
                    $query->where("part_num",  "{$sku}");
                })
                ->get()
                ; //->first();
        //log::info("2- Searching product by SKU-Found: " . count($product));                
        if (count($product) == 0) {

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


    public function availableStock(array $fields = null)
    {
        return Product::select($fields && count($fields) > 0 ? $fields : '*')
            ->where('stock_quantity', '>', 0)
            ->get();
    }
      
    public function activeStock(array $fields = null)
    {
        return Product::select($fields && count($fields) > 0 ? $fields : '*')
            ->where('is_active', '=', 1)
            // ->where('category', 'Pequeños Electrodomésticos')
            // ->where('sku', '43HT3WJ-B.AWC') //WT13DPBK.ASFECOL
            // ->orderBy('products.name', 'ASC')            
            ->get();
    }    

    public function updateProductImage(string $partNum, string $imageUrl)
    {
        return Product::where('part_num', $partNum)->update(['image_1' => $imageUrl]);
    }    
}
