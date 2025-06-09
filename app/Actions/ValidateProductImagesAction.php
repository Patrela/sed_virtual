<?php

namespace App\Actions;

use App\Http\Controllers\MaintenanceController;
use App\Repositories\ImageValidationRepository;
use App\Repositories\ProductRepository;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Log;

class ValidateProductImagesAction
{
    protected $productRepository;
    protected $fileController;
    protected $imageValidationRepository;
    protected $maintenanceController;

    public function __construct(ProductRepository $productRepository, ImageValidationRepository $imageValidationRepository, FileController $fileController, MaintenanceController $maintenanceController)
    {
        $this->productRepository = $productRepository;
        $this->imageValidationRepository = $imageValidationRepository;
        $this->fileController = $fileController;
        $this->maintenanceController = $maintenanceController;
    }

    public function execute(): array
    {
        $fields = ['part_num', 'name', 'department', 'category', 'stock_quantity', 'sale_price', 'currency'];
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];
        $products = $this->productRepository->availableStock(array_merge($fields, $imageFields));

        if ($products->isEmpty()) {
            return ['message' => "No products found with stock quantity greater than 0.", 'code' => 204];
        }

        $this->maintenanceController->setExecutionTime(7000);
        $invalidUrls = [];
        $records = 0;
        foreach ($products as $product) {
            if (++$records % 20 == 0) {
                log::info("{$records} - sku {$product->part_num}");
            }
            $validatedImage = '';
            foreach ($imageFields as $field) {

                if ($this->imageValidationRepository->validateImageUrl($product->{$field})) {
                    $validatedImage = $product->{$field}; // Guarda la primera URL válida
                    break; // Detiene el ciclo en la primera coincidencia válida
                }

            }

            if ($validatedImage == '') {
                $productData = collect($product)->only($fields)->toArray(); // Solo los campos relevantes
                $productData['image_1'] = $validatedImage; // Reemplaza con la URL válida
                $productData['valid_image_1'] = 0;
                $invalidUrls[] = $productData;
            } elseif ($validatedImage != $product['image_1']) {
                $productData = collect($product)->only($fields)->toArray(); // Solo los campos relevantes
                $productData['image_1'] = $validatedImage; // Reemplaza con la URL válida
                $productData['valid_image_1'] = 1; // Indica que la imagen es válida
                $invalidUrls[] = $productData;
            }
        }
        $fields[] = 'valid_image_1'; // Agrega el campo de validación de imagen al encabezado del CSV

        Log::info("Productos con imágenes inválidas: " . count($invalidUrls). " de " . $records);
        $this->maintenanceController->setExecutionTime();

        if (count($invalidUrls) == 0) {
            return [
                'message' => "No invalid URLs found.",
                'code' => 200,
            ];
        }

        return $this->fileController->saveArrayToCSV($fields, $invalidUrls, 'products_wrong_images.csv');
    }
}

