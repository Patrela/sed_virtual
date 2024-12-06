<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Trade;
use App\Http\Controllers\UserVisitLogController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\PersonalAccessToken;


class ConnectController extends Controller
{


    public function connectValidation(Request $request)
    { {
            try {
                $corsHeaders = [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization, X-Token-Auth, X-CSRF-TOKEN, x-api-key',
                ];


                $email = $request->header('X-Token-Auth');
                $token = $request->bearerToken();
                // Validate required headers
                if (!$email || !$token) {
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
                $user = $this->userOfflineAuthentication($email, "", $request->ip());
                if (!$user) {
                    return response()->json([
                        'message' => __('Auth.failure'),
                        'code' => 422,
                    ], 422);
                }
                $user = User::where('email', "{$email}")->first();
                app(UserVisitLogController::class)->userVisitRegistry($user->getId(), $user->trade_id);

                $token = $user->createToken('external-token', ['*'], now()->addMinutes(240))->plainTextToken;
                //Log::info("Login ok createToken ");

                return response()->json([
                    'name' =>  $user->name,
                    'redirect_url' => route('local.login') . "?email={$email}&token={$token}",
                    'message' => 'Login successful',
                    'code' => 200,
                ], 200)->withHeaders($corsHeaders);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error ' . $e->getMessage(),
                    'code' => $e->getCode(),
                ], 403);
            }
        }
    }

    private function isValidToken($token): bool
    {
        $generalToken = (app()->isProduction()) ? config('services.api.token_prod') : config('services.api.token_dev');
        //Log::info("generalToken " .$generalToken);
        return $token === $generalToken;
    }

    public function userOfflineAuthentication($email, $token, $rateLimiterKey)
    {
        $user = false;
        if ($token !== "") {
            $tokenData = PersonalAccessToken::findToken($token);
            //Log::info("tokenData");
            //Log::info($tokenData->tokenable);
            if ($tokenData && $tokenData->tokenable->email == $email) {
                $user = $tokenData->tokenable;
                Auth::login($user);
                //revoke the token
                $user = User::where('email', "{$email}")->first();
                $user->tokens()->where('token', "{$token}")->delete();
                //(Auth::check())? Log::info("Auth check  successful"): Log::info("Auth check  failed");
            }
        } else {
            $password  = substr($email, 0, strpos($email, "@"));
            $inputs = array('email' => "{$email}", 'password' => "{$password}");
            $rules = array('email' => 'required|email', 'password' => 'required');
            $user = User::where('email', "{$email}")->first();
            if ($user) {
                $validator = Validator::make($inputs, $rules);
                if (!$validator->fails()) {
                    if (Auth::attempt($inputs)) {
                        $user = Auth::user();
                    }
                }
            }
        }
        if (!$user) {
            RateLimiter::hit(strtolower($email) . '|' . $rateLimiterKey);
        } else {
            RateLimiter::clear(strtolower($email) . '|' . $rateLimiterKey);
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

        $user = $this->userOfflineAuthentication($email, $token, $request->ip());
        if (!$user) {
            return response()->json([
                'message' => 'Invalid token or email',
                'code' => 422
            ], 422);
        }

        //session()->regenerate();
        session(['current_user' => $email]);
        session(['SESSION_SECRET' => config('services.api.token_connect')]);

        return redirect()->route('products.index');
    }

    //bearer token in header X-Token-Auth and Standard basic auth authentication
    public function validateTradeBasicAuthentication(Request $request)
    {

        $token = $request->header('x-api-key');
        //Log::info('token ' . $token);
        //Validate required headers
        if (!$token || !$request->hasHeader('Authorization')) {
            //Log::info(' has auth ' . $request->hasHeader('Authorization'));
            return false;
        }
        $auth_standard_key = config('services.api.standard_key');

        $credentials = base64_decode(substr($request->header('Authorization'), 6));
        list($auth_email, $password) = explode(':', $credentials);

        if ($password !== $auth_standard_key) {
            //Log::info('auth_standard_key ' . $auth_standard_key);
            //Log::info('password ' . $password . 'email ' . $auth_email);
            return false;
        }
        $trade = Trade::where('email', "{$auth_email}")
            ->when(app()->isProduction(), function ($query) use ($token) {
                $query->where('token_production', "{$token}");
            })
            ->when(!app()->isProduction(), function ($query) use ($token) {
                $query->where('token_stage', "{$token}");
            })
            ->first();
        return $trade;
    }

}
