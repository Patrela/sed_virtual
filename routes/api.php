<?php

use App\Jobs\CreateNewUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SedController;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\Auth\RoleProfileController;


Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
})->name('sanctum.token');

Route::post('/connect/{username}', [ConnectController::class, 'connectValidation'])->name('api.connect');
Route::get('/connect/node', [ConnectController::class, 'showNodeVersion'])->name('api.node');


Route::post('/documentation/postman', function () {
    return redirect()->away('https://sed-stock.postman.co/collection/32783257-26376ef5-562b-4b9a-b99f-181038bb6fe3?source=rip_html');
    //https://www.postman.com/sed-stock/workspace/stock/collection/32783257-162e661d-7d69-42c2-a7d4-2cf3f6fcecec
})->name('api.documentation');

Route::put('/testing/{email}/{role_type}', function (Request $request, string $email, string $role_type) {
    return response()->json(array('email' => $email, 'role_type' => $role_type, 'status' =>200), 200);
})->name("api.puttest");

// Route::put('/roleprofile/{email}/{role_type}', function (Request $request, string $email, string $role_type) {
//     return app(RoleProfileController::class)->updateRoleProfile($request, $email, $role_type);
// })->name("roleprofile.update");

Route::put('/roleprofile/{email}/{role_type}',[RoleProfileController::class,'updateRoleProfile'])->name('roleprofile.update');

Route::get('/sed/clasifications', [SedController::class, 'getProductGroups'])->name('sed.getProviderGroups');
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




