<?php

use App\Jobs\CreateNewUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SedController;
use App\Http\Controllers\ConnectController;
use App\Http\Middleware\HandleMemory;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

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

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
})->name('sanctum.token');

Route::middleware(['cors'])->group(function () {
    Route::post('/external_connect/{username}', function (Request $request, string $username) {
        return app(ConnectController::class)->connectValidation($request, $username);
    })->name('api.connect');

    Route::get('/external_login', function (Request $request) {
        return app(ConnectController::class)->connectLogin($request);
    })->middleware(HandleMemory::class)->name('api.login'); //->middleware(HandleMemory::class)->name('api.login');

    // Route::middleware('cors')->get('/external_login', function (Request $request) {
    //     return app(ConnectController::class)->connectLogin($request);
    // })->middleware(HandleMemory::class)->name('api.login'); //->middleware(HandleMemory::class)->name('api.login');
    Route::post('/external_documentation', function () {
        return redirect()->away('https://sed-stock.postman.co/collection/32783257-26376ef5-562b-4b9a-b99f-181038bb6fe3?source=rip_html');
        //https://www.postman.com/sed-stock/workspace/stock/collection/32783257-162e661d-7d69-42c2-a7d4-2cf3f6fcecec
    })->name('api.documentation');
});

//Route::middleware(['auth'])->group(function () {
    Route::get('/sed/clasifications', [SedController::class, 'getProductGroups'])->name('sed.getProviderGroups');  // syncProductsClasification
    Route::get('/sed/users', function () {
        CreateNewUsers::dispatchAfterResponse();
        //CreateNewUsers::dispatch();
        return response()->json([
            'message' => 'SED New Users update process initiated in the background',
            'code' => 202,
        ], 202);
    })->name('sed.users');
    Route::post('/sed/customers/auth', [SedController::class, 'validateCustomerUser'])->name('sed.CustomerUser');
    Route::get('/sed/staff',[SedController::class, 'getStaffUsers'])->name('sed.staff');
//});




