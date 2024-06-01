<?php



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ProcessExternalProducts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;


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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // load products page
    Route::get('/ppal', function () {
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
        return view('ppal', $data);
    })->name('ppal');

    Route::get('/refresh', function () {
        app(LogController::class)->clearCacheKey('sync_products');
        ProcessExternalProducts::dispatchSync();
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        return view('ppal', $data);
    })->name('refresh');
});

Route::get('/profile/abilities/{username}', [ProfileController::class, 'userAbilities'])->name('profile.abilities');


//products routes
//Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
Route::middleware(['auth:sanctum', 'abilities:product-list,product-show'])->group(function () {
    Route::get('/products', function () {
        $products = app(ProductController::class)->getDepartmentProducts();
        $data = app(CategoryController::class)->loadPageData($products);
        //if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
        return view('ppal', $data);
    })->name('product.index');
    Route::get('/products/brands/{group}/{brands}', function ($group, $brands) {
        app(ProductController::class)->getBrandProducts($group, $brands);
        if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
    })->name('product.brand');

    Route::get('/products/categories/{group}/{categories}', function ($group, $categories) {
        $products = app(ProductController::class)->getCategoriesProducts($group,$categories);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
        return view('ppal', $data);
    })->name('product.categories');
    /*
    Route::get('/products/segment/{group}', function ($group) {
        $products = app(ProductController::class)->getSegmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
        return view('ppal', $data);
    })->name('product.segment');    */
    Route::get('/products/{group}', function ($group) {
        $products = app(ProductController::class)->getDepartmentProducts($group);
        $data = app(CategoryController::class)->loadPageData($products,$group);
        if (!app(LogController::class)->keyInCache('sync_products') )  ProcessExternalProducts::dispatchAfterResponse();
        return view('ppal', $data);
    })->name('product.show');
    Route::get('/products/search/{searchText}', function ($searchText) {
        $products = app(ProductController::class)->getSearchProducts($searchText);
        $data = app(CategoryController::class)->loadPageData($products, "", $searchText);
        return view('ppal', $data);
       // return $products;
    })->name('search');
    Route::get('/products/mail/{sku}',  [ProductController::class, 'mailProducts'])->name('product.email');

    /*
    Route::get('/products/order/{group}/{order}', function ($group, $order) {
        $products = app(ProductController::class)->getOrderProducts($group, $order);
        $data = app(CategoryController::class)->loadPageData($products, $group);
        return view('ppal', $data);
    })->name('order');
    */
});

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

Route::post('/external', function () {
    return redirect()->away('https://www.postman.com/sed-stock/workspace/stock/collection/32783257-162e661d-7d69-42c2-a7d4-2cf3f6fcecec');
})->name('postman.stock');

/*
Route::get('/mail/test/{contact}', function ($contact) {
    //use Illuminate\Support\Facades\Mail;
    try {
        Mail::to('patorela@gmail.com')->cc('patrela@hotmail.com')->send(new TestMailable($contact));
        return response()->json([
            'result' => "correo enviado",
            'code' => 200,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
        ], 403);
    }

})->name('mail.test');;
*/

require __DIR__.'/auth.php';
