<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;

class LogController extends Controller
{
    /*
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('home'); //('/')
     }
*/
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return Redirect::route('home');
            //return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    //clear cache key
    public function clearCacheKey($keycache) //'sync_products'
    {

        if($this->keyInCache($keycache)) // Cache::store()->getStore() !== null //config('cache.enabled')
        {
            Cache::clear($keycache);
            return response()->json([
                'success' => true,
                'state' => 'cache cleared',
            ], 200);
        }
        return response()->json([
            'success' => true,
            'state' => 'cache is disabled',
        ], 200);

    }
    //validate cache key
    public function keyInCache($keycache)
    {   $keyInCache = false;
        $cachestore= config('cache.default');
        if($cachestore){
            $cachestore=config("cache.stores.{$cachestore}");
        }
        if($cachestore) // Cache::store()->getStore() !== null //config('cache.enabled')
        {
            //Cache::put('sync_products', 'test data', now()->addMinutes(30));
            if(Cache::has($keycache)) $keyInCache = true;
        }
        return  $keyInCache;// $cachestore; //  $keyInCache; // Cache::get('sync_products');

    }


}
