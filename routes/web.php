<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SwaggerController;
use App\Http\Controllers\AffinityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\RoleProfileController;
use App\Jobs\ImportProducts;


Route::get('/', function () {
    if (session()->has('current_user')) {
        return redirect()->route('product.index');
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
    Route::get('/profile/abilities/{username}', [ProfileController::class, 'userAbilities'])->name('profile.abilities');

});

Route::middleware('auth:sanctum')->group(function () { //middleware('auth:sanctum')
    Route::get('/api/documentation', [SwaggerController::class, 'show'])->name('documentation.show')->middleware('ability:document-read');
    Route::get('/api/documentation/json/swagger.json', [SwaggerController::class, 'getSwaggerJson'])->name('documentation.json')->middleware('ability:document-read');
});

Route::prefix('rolesprofile')->middleware('auth')->group(function () {
    Route::get('/',[RoleProfileController::class,'index'])->name('roleprofile.index');
    Route::get('/{email}',[RoleProfileController::class,'searchProfileEmail'])->name('roleprofile.mail');
    Route::put('/{email}/{role_type}',[RoleProfileController::class,'updateRoleProfile'])->name('roleprofile.update');
});


//sanctum abilities and privileges
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    Route::get('/post/create', function (Request $request) { //sanctum with ability
        return response()->json([
            'id' => 1,
            'title' => $request->title,
            'content' => $request->content,
        ]);
    })->name('sanctum.post')->middleware('ability:post-create');
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

/*
Route::prefix('products')->middleware(['auth'])->group(function () { //Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
    Route::get('/stock', function () {
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        if (!Cache::has('sync_products') ) ImportProducts::dispatchAfterResponse();
        return view('product.index', $data);
    })->name('product.index');

    Route::get('/refresh', function () {
        Cache::clear('sync_products');
        ImportProducts::dispatchSync();
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        return view('product.index', $data);
    })->name('product.refresh');

    // Route::get('/products/segment/{group}', function ($group) {
    //     $products = app(ProductController::class)->getSegmentProducts($group);
    //     $data = app(CategoryController::class)->loadPageData($products,$group);
    //     if (!Cache::has('sync_products') )  ImportProducts::dispatchAfterResponse();
    //     return view('product.index', $data);
    // })->name('product.segment');

    Route::get('/{group}', function ($group) {
        $products = app(ProductController::class)->getDepartmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!Cache::has('sync_products') )  ImportProducts::dispatchAfterResponse();
        return view('product.index', $data);
    })->name('product.index');

    Route::get('/search/{searchText}', function ($searchText) {
        $products = app(ProductController::class)->getSearchProducts($searchText);
        $data = app(CategoryController::class)->loadPageData($products, "", $searchText);
        return view('product.index', $data);
    })->name('product.search');

    Route::get('/mail/{sku}',  [MailController::class, 'sendMail'])->name('product.email');
});

*/

Route::prefix('products')->middleware(['auth'])->group(function () {

    // Shared logic to get products and data
    $getProductsAndData = function ($group = null, $searchText = null) {
        //$maingroupName = "";
        if ($searchText) {
            $products = app(ProductController::class)->getSearchProducts($searchText);
        } else {
            $group = ($group)? $group : "Computadores";
            $products = app(ProductController::class)->getDepartmentProducts($group);
        }

        $data = app(CategoryController::class)->loadPageData($products, $group, $searchText);

        if (!Cache::has('sync_products')) ImportProducts::dispatchAfterResponse();

        return $data;
    };

    Route::get('/', function () use ($getProductsAndData) {
        $data = $getProductsAndData();
        return view('product.index', $data);
    })->name('product.index');

    // Import Products
    Route::get('/refresh', function () use ($getProductsAndData) {
        Cache::forget('sync_products');
        ImportProducts::dispatchSync();
        $data = $getProductsAndData();
        return view('product.index', $data);
    })->name('product.refresh');

    // Products by Department
    Route::get('/{group}', function ($group) use ($getProductsAndData) {
        $data = $getProductsAndData($group);
        return view('product.index', $data);
    })->name('product.department');

    // Products by Pattern
    Route::get('/search/{searchText}', function ($searchText) use ($getProductsAndData) {
        $data = $getProductsAndData(null, $searchText);
        return view('product.index', $data);
    })->name('product.search');

    // send product email by sku
    Route::get('/mail/{sku}', [MailController::class, 'sendMail'])->name('product.email');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/affinities',  [AffinityController::class, 'index'])->name('affinity.index');
    Route::get('/affinities/{brand}',  [AffinityController::class, 'getAffinities'])->name('affinity.show');
    Route::post('/affinities/{brand}',[AffinityController::class,'createOrUpdateAffinity'])->name('affinity.save');
});

Route::middleware('auth')->group(function () {
    Route::get('/categories}', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/categories/departments', [CategoryController::class, 'departments'])->name('category.departments');
    Route::get('/categories/parent/{groupname}/{parentid}', [CategoryController::class, 'childGroups'])->name('category.parent');
});

Route::middleware('auth')->group(function () {
    Route::get('/files/vtex-imagesnames', [FileController::class, 'saveVtexImagesFileName'])->name('file.vtex-imagesnames');
    Route::post('/files/export-csv/{name}', [FileController::class, 'exportCsv'])->name('file.csv-export');
});

Route::get('/local/login', [ConnectController::class, 'connectLogin'])->name('local.login');

require __DIR__.'/auth.php';
