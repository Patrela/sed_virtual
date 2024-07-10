<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Jobs\ImportProducts;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;



Route::get('/', function () {
    if (session()->has('current_user')) {
        //Log::info("path stock");
        return redirect()->route('stock');
    } else {
        // session()->invalidate();
        // session()->regenerateToken();

        return redirect()->route('login');
    }
    //return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/stock', function () {
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        if (!Cache::has('sync_products') )  ImportProducts::dispatchAfterResponse();
        //(Auth::check())? Log::info("Auth check  successful 4"): Log::info("Auth check  failed 4");
        return view('stock', $data);
    })->name('stock');

    Route::get('/refresh', function () {
        //Log::info("path REFRESH get in");
        Cache::clear('sync_products');
        ImportProducts::dispatchSync();
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        return view('stock', $data);
    })->name('refresh');
});

//To Check session -cache - Auth Data
Route::get('/memory-data', function () {
    $cacheKeys = ['clasifications', 'sync_products','query', 'email','token'];
    $cacheData = [];

    foreach ($cacheKeys as $key) {
        $cacheData[$key] = Cache::has($key)? Cache::get($key) : "";
    }
    return view('memory-data', ['session' => Session::all(), 'user' => Auth::user(), 'cacheData' => $cacheData]);
})->name('data');


Route::get('/profile/abilities/{username}', [ProfileController::class, 'userAbilities'])->name('profile.abilities');

//products routes
//Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
Route::middleware(['auth'])->group(function () {

    /*
    Route::get('/products/segment/{group}', function ($group) {
        $products = app(ProductController::class)->getSegmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!Cache::has('sync_products') )  ImportProducts::dispatchAfterResponse();
        return view('stock', $data);
    })->name('product.segment');
    */

    Route::get('/products/{group}', function ($group) {
        //Log::info("path PRODUCTS get in");

        $products = app(ProductController::class)->getDepartmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!Cache::has('sync_products') )  ImportProducts::dispatchAfterResponse();
        return view('stock', $data);
    })->name('product.index');

    Route::get('/products/search/{searchText}', function ($searchText) {
        //Log::info("path SEARCH get in");
        $products = app(ProductController::class)->getSearchProducts($searchText);
        $data = app(CategoryController::class)->loadPageData($products, "", $searchText);
        return view('stock', $data);
    })->name('search');

});

Route::get('/products/mail/{sku}',  [MailController::class, 'sendMail'])->name('product.email');
Route::get('send-mail/{email}', [MailController::class, 'sendMail']);


//categories routes
Route::middleware('auth')->group(function () {
    Route::get('/categories}', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/categories/departments', [CategoryController::class, 'departments'])->name('department.index');
    Route::get('/categories/parent/{groupname}/{parentid}', [CategoryController::class, 'childGroups'])->name('category.parent');
});
/*
Route::get('/products/export/{sku}', [ProductController::class, 'toExcel'])->name('product.excel');
Route::post('/products/csv', [ProductController::class, 'toCsv'])->name('product.csv');
*/
//read Vtex Images Directory
Route::get('/guardar-carpeta', [FileController::class, 'guardarCarpeta'])->name('files.scan');


require __DIR__.'/auth.php';
