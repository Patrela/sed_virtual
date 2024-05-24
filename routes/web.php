<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    if (session()->has('current_user') || Cache::has('sync_products')) {
        return redirect()->route('ppal');
    } else {
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
    //return view('welcome');
})->name('home');
// load products page
Route::get('/ppal', function () {
    $products = app(ProductController::class)->getDepartmentProducts();
    $data = app(CategoryController::class)->loadPageData($products);
    return view('ppal', $data);
})->name('ppal');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/abilities', [ProfileController::class, 'abilities'])->name('profile.abilities');
});
//Route::get('/logout', [LogController::class, 'destroy'])->name('logout');




//products routes
Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/products/brands/{group}/{brands}', [ProductController::class, 'getBrandProducts'])->name('product.brand');
    Route::get('/products/categories/{group}/{categories}', [ProductController::class, 'getCategoriesProducts'])->name('product.categories');

    //Route::get('/products/refresh', [ProductController::class, 'getDepartmentClearCache'])->name('refresh');
    Route::get('/refresh', function () {
        $products = app(ProductController::class)->getDepartmentClearCache();
        $data = app(CategoryController::class)->loadPageData($products);
        return view('ppal', $data);
    })->name('refresh');

    //Route::get('/products/segment/{group}', [ProductController::class, 'getSegmentProducts'])->name('product.segment');
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
});

//categories routes
Route::middleware('auth')->group(function () {
    Route::get('/categories}', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/categories/departments', [CategoryController::class, 'departments'])->name('department.index');
    Route::get('/categories/parent/{groupname}/{parentid}', [CategoryController::class, 'childGroups'])->name('category.parent');
});

Route::get('/products/export/{sku}', [ProductController::class, 'toExcel'])->name('product.excel');
Route::get('/products/tocsv', [ProductController::class, 'exportCsv'])->name('product.tocsv');
Route::post('/products/csv', [ProductController::class, 'toCsv'])->name('product.csv');
//read Vtex Images Directory
Route::get('/guardar-carpeta', [FileController::class, 'guardarCarpeta']);

Route::post('/external', function () {
    return redirect()->away('https://www.postman.com/sed-stock/workspace/stock/collection/32783257-162e661d-7d69-42c2-a7d4-2cf3f6fcecec');
})->name('postman.stock');
//emails
Route::get('/emails/detail',  [ProductController::class, 'mailProducts'])->name('product.mail');
require __DIR__.'/auth.php';
