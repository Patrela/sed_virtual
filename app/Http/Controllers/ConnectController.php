<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ConnectController extends Controller
{
    public function connectValidation(Request $request, string $username){
        {
            try {
                $corsHeaders = [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization, X-Token-Auth, X-CSRF-TOKEN'
                ];
                $email = $request->header('X-Token-Auth');
                $token = $request->bearerToken();
                // Validate required headers
                if (!$email || !$token || !$username ) {
                    return response()->json([
                        'message' => 'Error Missing required Authorization Data',
                        'code' => 401
                    ], 401);
                }
                if (!$this->isValidToken($token)) {
                    return response()->json([
                        'message' => 'Error Invalid token',
                        'code' => 402,
                    ], 402);
                }
                $user = $this->userOfflineAuthentication($email, "", $request ->ip());
                if (!$user) {
                    return response()->json([
                        'message' => __('Auth.failure'),
                        'code' => 422,
                    ], 422);
                }
                $user = User::where( 'email', "{$email}")->first();
                $token = $user->createToken('external-token', ['*'], now()->addMinutes(240))->plainTextToken;
                //Log::info("Login ok createToken ");

                return response()->json([
                    'name' =>  $username,
                    'redirect_url' => route('local.login') . "?email={$email}&token={$token}",
                    'message' => 'Login successful',
                    'code' => 200,
                ], 200)->withHeaders($corsHeaders);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error ' .$e->getMessage(),
                    'code' => $e->getCode(),
                ], 403);
            }
        }

    }

    private function isValidToken($token): bool
    {
        $generalToken = ( app()->isProduction() )? config('services.api.token_prod') : config('services.api.token_dev');
        //Log::info("generalToken " .$generalToken);
        return $token === $generalToken;
    }

    public function userOfflineAuthentication($email, $token , $rateLimiterKey )
    {
        $user= false;
        if($token !== "") {
            $tokenData = PersonalAccessToken::findToken($token);
            //Log::info("tokenData");
            //Log::info($tokenData->tokenable);
            if( $tokenData && $tokenData->tokenable->email == $email)
            {
                $user= $tokenData->tokenable;
                Auth::login($user);
                //(Auth::check())? Log::info("Auth check  successful"): Log::info("Auth check  failed");
            }
        } else{
            $password  = substr($email,0,strpos($email,"@"));
            $inputs = array('email' =>"{$email}", 'password' => "{$password}");
            $rules = array( 'email' => 'required|email', 'password' => 'required');
            $user = User::where( 'email', "{$email}")->first();
            if ($user) {
                $validator = Validator::make($inputs, $rules);
                if (!$validator->fails()){
                    if(Auth::attempt($inputs)){
                        $user = Auth::user();
                    }
                }
            }
        }
        if(!$user)
        {
            RateLimiter::hit(strtolower($email) .'|'.$rateLimiterKey);
        }
        else{
            RateLimiter::clear(strtolower($email) .'|'.$rateLimiterKey);
        }
        return $user;
    }


    public function connectLogin(Request $request)
    {

        $email = $request->query('email');
        $token = $request->query('token');

        if (!$email || !$token) {
            return response()->json([
                'message' => 'Email and token are required',
                'code' => 400
            ], 400);
        }

        $user = $this->userOfflineAuthentication($email, $token, $request ->ip());
        if (!$user) {
            return response()->json([
                'message' => 'Invalid token or email',
                'code' => 422
            ], 422);
        }

        //session()->regenerate();
        session(['current_user' => $email]);
        return redirect()->route('stock', ['email' => $email]);
    }
    public function connectTest(Request $request) {
        $flags = [
            'app_prod' => 2,
            'env' =>env('APP_ENV'),
            'prod' => config('services.api.prod'),
            'dev' => config('services.api.dev'),
            'prod_token' => config('services.api.token_prod'),
            'dev_token' => config('services.api.token_dev')
        ];
        Log::info($flags);
        //echo $flags;
        try {
            $flags['app_prod'] =app()->isProduction();
            return  var_dump($flags); //response()->json( $flags);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error ' .$e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }

}
