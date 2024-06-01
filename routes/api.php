<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SedController;
use App\Http\Controllers\VtexController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//sanctum abilities and privileges
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');
});

Route::get('/post/create', function (Request $request) { //sanctum with ability
    return response()->json([
        'id' => 1,
        'title' => $request->title,
        'content' => $request->content,
    ]);
})->name('sanctum.post')->middleware(['auth:sanctum', 'ability:post-create']);

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
})->name('sanctum.token');

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->input('email'))->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
    return response()->json([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
        'token' => $user->createToken('api')->plainTextToken,
    ], 200);
})->name('login');

Route::post('/login/header', function (Request $request) {
    $user = User::where('email', $request->input('email'))->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
    return response()->json([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
        'token' => $user->createToken('api')->plainTextToken,
    ], 200);
});

Route::get('/sed/products', [SedController::class, 'getProviderProducts'])->name('sed.getProviderProducts');
Route::get('/sed/products/{department}', [SedController::class, 'syncDepartmentProducts'])->name('sed.DeparmentProducts');
Route::get('/sed/clasifications', [SedController::class, 'syncProductGroups'])->name('sed.syncGroupsAPI');  // syncProductsClasification
Route::post('/sed/clearcache/{keycache}', [LogController::class, 'clearCacheKey'])->name('sed.clearCache');
Route::get('/sed/customers', [SedController::class, 'CustomersB2B'])->name('sed.Customers');
Route::post('/sed/customers/auth', [SedController::class, 'validateCustomerUser'])->name('sed.CustomerUser');

Route::get('/vtex', [VtexController::class, 'connect'])->name('vtex.conection');
