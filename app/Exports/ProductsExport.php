<?php
namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements  FromCollection
{
    protected $sku;

    public function __construct($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::where('part_num',"{$this->sku}" )->get();
    }
}
