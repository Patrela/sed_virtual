<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Jobs\CreateNewUsers;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SedController;
use App\Http\Controllers\VtexController;
use App\Http\Controllers\ExternalAuthController;
use App\Http\Middleware\HandleCorsHeaders;

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

/*
Route::post('/user/login', function (Request $request) {
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
})->name('user.login');
*/
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
Route::get('/sed/clasifications', [SedController::class, 'getProductGroups'])->name('sed.getProviderGroups');  // syncProductsClasification
Route::post('/sed/clearcache/{keycache}', [LogController::class, 'clearCacheKey'])->name('sed.clearCache');
Route::get('/sed/customers', [SedController::class, 'CustomersB2B'])->name('sed.Customers');
Route::post('/sed/customers/auth', [SedController::class, 'validateCustomerUser'])->name('sed.CustomerUser');
Route::get('/sed/staff',[SedController::class, 'getStaffUsers'])->name('sed.staff');
Route::get('/sed/users', function () {
    CreateNewUsers::dispatchAfterResponse();
    //CreateNewUsers::dispatch();
    return response()->json([
        'result' => 'SED New Users update process initiated in the background',
        'code' => 202,
    ], 202);
})->name('sed.users');

//Route::get('/vtex/login/{username}', [VtexController::class, 'connect'])->name('vtex.conection');
Route::get('/vtex/login/mail/{useremail}', [LogController::class, 'authenticateAPI'])->name('vtex.login');

/* Route::get('/vtex/login/{username}', function ($username) {
    app(VtexController::class)->connect($username);
})->name('vtex.conection');
*/


//Route::post('token_login', [ExternalAuthController::class, 'tokenLogin'])->middleware('cors')->name('api.login');
//Route::post('/token_login', 'App\Http\Controllers\Auth\ExternalAuthControllerr@tokenLogin')->name('login.api');

Route::post('token_login', [ExternalAuthController::class, 'tokenLogin'])->middleware('cors')->name('api.login');
