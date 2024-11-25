<?php

use App\Jobs\ImportProducts;
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
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Auth\RoleProfileController;


Route::get('/', function () {
    if (session()->has('current_user')) {
        return redirect()->route('products.index');
    } else {
        // session()->invalidate();
        // session()->regenerateToken();
        return redirect()->route('login');
    }
    //return view('welcome');
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::prefix('/profile')->controller(ProfileController::class)->group(function () {
    Route::get('/', 'edit')->name('profile.edit');
    Route::patch('/', 'update')->name('profile.update');
    Route::delete('/', 'destroy')->name('profile.destroy');
    Route::get('/abilities/{username}', 'userAbilities')->name('profile.abilities');
})->middleware('auth');

Route::prefix('/documentation')->controller(SwaggerController::class)->group(function () {
    Route::get('/', 'show')->name('documentation.show');
    Route::get('/json/swagger.json', 'getSwaggerJson')->name('documentation.json');
    Route::post('/postman', function () {
        return redirect()->away('https://sed-stock.postman.co/collection/32783257-26376ef5-562b-4b9a-b99f-181038bb6fe3?source=rip_html');
    })->name('api.documentation');
})->middleware(['auth:sanctum','ability:document-read']);

Route::prefix('rolesprofile')->controller(RoleProfileController::class)->group(function () {
    Route::get('/','index')->name('rolesprofile.index');
    Route::get('/{email}','searchProfileEmail')->name('rolesprofile.mail');
    Route::put('/{email}/{role_type}','updateRoleProfile')->name('rolesprofile.update');
})->middleware('auth');


Route::prefix('products')->middleware(['auth'])->group(function () {

    // Shared logic to get products and data
    $getProductsAndData = function ($group = null, $searchText = null) {
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
    })->name('products.index');

    // Import Products
    Route::get('/refresh', function () use ($getProductsAndData) {
        Cache::forget('sync_products');
        ImportProducts::dispatchSync();
        $data = $getProductsAndData();
        return view('product.index', $data);
    })->name('products.refresh');

    // Products by Department
    Route::get('/{group}', function ($group) use ($getProductsAndData) {
        $data = $getProductsAndData($group);
        return view('product.index', $data);
    })->name('products.department');

    // Products by Pattern
    Route::get('/search/{searchText}', function ($searchText) use ($getProductsAndData) {
        $data = $getProductsAndData(null, $searchText);
        return view('product.index', $data);
    })->name('products.search');

    // send product email by sku
    Route::get('/mail/{sku}', [MailController::class, 'sendMail'])->name('products.email');
});

Route::prefix('/affinities')->controller(AffinityController::class)->group(function () {
    Route::get('/','index')->name('affinity.index');
    Route::get('/{brand}', 'getAffinities')->name('affinity.show');
    Route::post('/{brand}', 'createOrUpdateAffinity')->name('affinity.save');
})->middleware(['auth']);

Route::prefix('/categories')->controller(CategoryController::class)->group(function () {
    Route::get('/}', 'index')->name('category.index');
    Route::get('/departments', 'departments')->name('category.departments');
    Route::get('/parent/{groupname}/{parentid}', 'childGroups')->name('category.parent');
})->middleware('auth');

Route::prefix('/files')->controller(FileController::class)->group(function () {
    Route::get('/vtex-imagesnames', 'saveVtexImagesFileName')->name('file.vtex-imagesnames');
    Route::post('/export-csv/{name}', 'exportCsv')->name('file.csv-export');
    Route::get('/export-wrong-product-url-images', [ProductController::class, 'getWrongUrlImageProducts'])->name('file.getWrongUrlImageProducts');
})->middleware('auth');

//To Check session -cache - Auth Data
Route::prefix('maintenance')->middleware(['auth'])->group(function () {
    Route::get('/memory-data', function () {
        $cacheKeys = ['clasifications', 'sync_products','query', 'email','token'];
        $cacheData = [];

        foreach ($cacheKeys as $key) {
            $cacheData[$key] = Cache::has($key)? Cache::get($key) : "";
        }
        return view('memory-data', ['session' => Session::all(), 'user' => Auth::user(), 'cacheData' => $cacheData])->name('maintenance.memory-data');
    })->name('data');

    Route::get('/clear-cache', [MaintenanceController::class, 'clearCache'])->name('maintenance.clear-cache');
});

Route::get('/local/login', [ConnectController::class, 'connectLogin'])->name('local.login');

require __DIR__.'/auth.php';
