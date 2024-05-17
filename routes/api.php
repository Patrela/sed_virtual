<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SedController;
use App\Http\Controllers\VtexController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');
});

Route::get('/post/create', function (Request $request) { //sanctum with ability
    return [
        'id' => 1,
        'title' => $request->title,
        'content' => $request->content,
    ];
})->middleware(['auth:sanctum', 'ability:post-create']);

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::post('/login', function (Request $request) {
    $user = User::where('email',$request->input('email'))->first();
    if(!$user || !Hash::check($request->password,$user->password)){
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
    return response()->json([
        'user' => [
                'name' => $user->name,
                'email' => $user->email,
        ],
        'token' => $user->createToken( 'api')->plainTextToken,
    ], 200);
});
Route::post('/login/header', function (Request $request) {
    $user = User::where('email',$request->input('email'))->first();
    if(!$user || !Hash::check($request->password,$user->password)){
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
    return response()->json([
        'user' => [
                'name' => $user->name,
                'email' => $user->email,
        ],
        'token' => $user->createToken( 'api')->plainTextToken,
    ], 200);
});

Route::get('/sed/products', [SedController::class, 'syncProductsAPI'])->name('sed.syncProductsAPI');
Route::get('/sed/products/{department}', [SedController::class, 'syncDepartmentProducts'])->name('sed.DeparmentProducts');
Route::get('/sed/clasifications', [SedController::class, 'syncProductGroups'])->name('sed.syncGroupsAPI');  // syncProductsClasification
Route::post('/sed/cleared', [SedController::class, 'clearProductCache'])->name('sed.clearCache');

Route::get('/vtex', [VtexController::class, 'connect'])->name('vtex.conection');
