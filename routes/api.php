<?php

use App\Jobs\CreateNewUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EpicorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ConnectController;

// Token creation route
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
})->name('sanctum.token');

Route::prefix('connect')->controller(ConnectController::class)->group(function () {
    Route::post('/', 'connectValidation')->name('connect.validation');
    Route::post('/{username}', 'connectValidation')->name('connect.validation');
});

Route::post('/order/{username}', [OrderController::class, 'createOrUpdateOrder'])->name('connect.createOrUpdateOrder');
//Route::post('/order/period/{start}/{end}/{order}/{trade}', [OrderController::class, 'getPeriodOrders'])->name('order.period');

Route::prefix('epicor')->controller(EpicorController::class)->group(function () {
    Route::get('/clasifications', 'getProductGroups')->name('epicor.getProviderGroups');
    Route::get('/staff', 'getStaffUsers')->name('epicor.staff');
    Route::get('/staff/new-users', 'updateNewUsers')->name('epicor.newUser');
    Route::get('/staff/put-users', 'importUsers')->name('epicor.users');

    Route::get('/users', function () {
        CreateNewUsers::dispatchAfterResponse();
        return response()->json([
            'message' => 'SED New Users update process initiated in the background',
            'code' => 202,
        ], 202);
    })->name('epicor.putUsers');
});
