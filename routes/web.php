<?php

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SedController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    //return view('principal'); //'principal' 'welcome'
    if (session()->has('current_user')) {
        return redirect()->route('ppal');
    } else {
        return redirect()->route('login');
    }
})->name('home');

// Route::get('/products/search/{searchText}', [ProductController::class, 'getSearchProducts'])->name('product.search');

Route::get('/welcome', function () {
    return view('welcome'); //'principal' 'welcome'
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // list of santum profile abilities
    Route::get('/profile/abilities', [ProfileController::class, 'abilities'])->name('profile.abilities');
});

Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    //Query for button of department
    //Route::get('/products/{group}', [ProductController::class, 'getDepartmentProducts'])->name('product.show');
    Route::get('/products/brands/{group}/{brands}', [ProductController::class, 'getBrandProducts'])->name('product.brand');
    Route::get('/products/{group}', function ($group) {
        $products = app(ProductController::class)->getDepartmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        return view('ppal', $data);
    })->name('product.show');
    Route::get('/products/search/{searchText}', function ($searchText) {
        $products = app(ProductController::class)->getSearchProducts($searchText);
        $data = app(CategoryController::class)->loadPageData($products, "", $searchText);
        return view('product.search', $data);
       // return $products;
    })->name('search');
    Route::get('/products/order/{group}/{order}', function ($group, $order) {
        $products = app(ProductController::class)->getOrderProducts($group, $order);
        $data = app(CategoryController::class)->loadPageData($products, $group);
        return view('ppal', $data);
    })->name('order');

    Route::get('/products/categories/{group}/{categories}', [ProductController::class, 'getCategoriesProducts'])->name('product.categories');
   // Route::get('/products/brands/{group}', [ProductController::class, 'getBrandProducts'])->name('product.brand');
    //Route::get('/products/segment/{group}', [ProductController::class, 'getSegmentProducts'])->name('product.segment');
    // Route::get('/products/search/{searchText}', [ProductController::class, 'getSearchProducts'])->name('product.search');
});


// Route::get('/products/order/{group}/{order}', [ProductController::class, 'getOrderProducts'])->name('product.order');

Route::get('/departments', [CategoryController::class, 'departments'])->name('department.index');
Route::get('/categories}', [CategoryController::class, 'index'])->name('category.index');
Route::get('/categories/parent/{groupname}/{parentid}', [CategoryController::class, 'childGroups'])->name('category.parent');
//read Vtex Images Directory
Route::get('/guardar-carpeta', [FileController::class, 'guardarCarpeta']);
require __DIR__ . '/auth.php';

Route::post('/external', function () {
    return redirect()->away('https://www.postman.com/sed-stock/workspace/stock/collection/32783257-162e661d-7d69-42c2-a7d4-2cf3f6fcecec');
})->name('postman.stock');


Route::get('/ppal', function () {
    // Check cache for products using ProductController
    $products = app(ProductController::class)->getDepartmentProducts();
    $data = app(CategoryController::class)->loadPageData($products);
    return view('ppal', $data);
})->name('ppal');

Route::post('/refresh', function () {
    cache::clear('sync_products_last_run');
    // Check cache for products using ProductController
    $products = app(ProductController::class)->getDepartmentProducts();
    $data = app(CategoryController::class)->loadPageData($products);
    return view('ppal', $data);
})->name('refresh');




