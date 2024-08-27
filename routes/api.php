<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SedController;
use App\Http\Controllers\ConnectController;
use App\Jobs\CreateNewUsers;

// Token creation route
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
})->name('sanctum.token');

Route::prefix('connect')->group(function () {
    Route::post('/{username}', [ConnectController::class, 'connectValidation'])->name('connect.validation');
    Route::get('/node', [ConnectController::class, 'showNodeVersion'])->name('connect.node');
});

Route::post('/documentation/postman', function () {
    return redirect()->away('https://sed-stock.postman.co/collection/32783257-26376ef5-562b-4b9a-b99f-181038bb6fe3?source=rip_html');
})->name('api.documentation');

Route::prefix('sed')->group(function () {
    Route::get('/clasifications', [SedController::class, 'getProductGroups'])->name('sed.getProviderGroups');
    Route::get('/staff', [SedController::class, 'getStaffUsers'])->name('sed.staff');
    Route::post('/customers/auth', [SedController::class, 'validateCustomerUser'])->name('sed.CustomerUser');
    Route::get('/users', function () {
        CreateNewUsers::dispatchAfterResponse();
        return response()->json([
            'message' => 'SED New Users update process initiated in the background',
            'code' => 202,
        ], 202);
    })->name('sed.users');
});
