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

Route::prefix('connect')->controller(ConnectController::class)->group(function () {
    Route::post('/{username}', 'connectValidation')->name('connect.validation');
    Route::get('/node', 'showNodeVersion')->name('connect.node');
});

Route::prefix('sed')->controller(SedController::class)->group(function () {
    Route::get('/clasifications', 'getProductGroups')->name('sed.getProviderGroups');
    Route::get('/staff', 'getStaffUsers')->name('sed.staff');
    Route::post('/customers/auth', 'validateCustomerUser')->name('sed.CustomerUser');
    Route::get('/users', function () {
        CreateNewUsers::dispatchAfterResponse();
        return response()->json([
            'message' => 'SED New Users update process initiated in the background',
            'code' => 202,
        ], 202);
    })->name('sed.users');
});
