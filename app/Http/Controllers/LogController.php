<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
    {
        if($this->isCache())
        {
            if(Cache::has($keycache)) return true;
        }
        return  false;
    }

    //validate cache active status
    public function isCache()
    {
        $cachestore = config('cache.default');
        if($cachestore){
            $cachestore = config("cache.stores.{$cachestore}");
        }
        if($cachestore)
        {
                return true;
        }
        return  false;
    }

    public function loginAPI(string $useremail)
    {
        $error= "";
        //$this->ensureIsNotRateLimited();
        $password = "Test" . sprintf("%05d", rand(147841,999999));
        $inputs = array('email' =>"{$useremail}", 'password' => "{$password}");
        $rules = array( 'email' => 'required|email', 'password' => 'required');

        $validator = Validator::make($inputs, $rules); //Input::all() , $rules);
        try {
            if (!$validator->fails()) {
                $user = User::where( 'email', "{$useremail}")->first();
                if($user)
                {
                    $user->password = Hash::make($password);
                    $user->remember_token =$password;
                    $user->save();
                    //Auth::login($user);
                    if (Auth::attempt( $inputs)){
                        session()->regenerate();
                        session(['current_trade' => $user->trade_id]);
                        session(['current_user' =>  $user->email]);
                        return Auth::user();
                    } else {
                        $error= "Invalid {$useremail} credentials";
                    }
                } else{
                    $error= "User {$useremail} not found";
                }
            } else {
                $error= "Validation error";
            }
        } catch (\Exception $e) {
            $error="Exception error " . $e->getMessage();
        }
        Log::error( "loginAPI. " .$error);
        session()->regenerate();
        return false;
    }


    public function authenticateAPI(string $useremail)
    {

        //$this->ensureIsNotRateLimited();
        $password = "Test" . sprintf("%05d", rand(147841,999999));
        $inputs = array('email' =>"{$useremail}", 'password' => "{$password}");
        $rules = array( 'email' => 'required|email', 'password' => 'required');

        $validator = Validator::make($inputs, $rules); //Input::all() , $rules);
        if ($validator->fails()) {
            return response()->json([
                'result' => "Doesn't pass validation",
                'code' => 402,
            ], 402);
        }

        $user = User::where( 'email', "{$useremail}")->first();
        $user->password = Hash::make($password);
        $user->remember_token =$password;
        $user->save();

        //Auth::login($user);

        try {
            if (!Auth::attempt( $inputs))
            {
                return response()->json([
                    'result' => "User {$useremail} not found",
                    'code' => 404,
                ], 404);
            }
            return response()->json([
                'email' => Auth::user()->email,
                'trade_id' => Auth::user()->trade_id,
                'result' => Auth::user()->trade_id, //'API',
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }

        //RateLimiter::clear($this->throttleKey());

        // $request->session()->regenerate();
        // session(['current_trade' => Auth::user()->trade_id]);
        // session(['current_user' =>  Auth::user()->email]);
        // Log::info("session API email.  " .session('current_user'));

        // return response()->json([
        //     'email' => Auth::user()->email,
        //     'trade_id' => Auth::user()->trade_id,
        //     'source' => 'API',
        //     'code' => 200
        // ], 200);
    }
}
