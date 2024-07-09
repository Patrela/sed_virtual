<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ConnectController;
use App\Providers\AppServiceProvider;

class HandleMemory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $email = "";
        if($request->has('email')){
            $email = $request->query('email');
        }
        /*
        elseif($response->has('email')){
            $email = $response->query('email');
        }
            */
        if($email != ""){
            $token = $request->query('token');
            if($email !=""){
                $user = app(ConnectController::class)->userOfflineAuthentication( $email, "", $request->ip());
                $email = $user->email;
            }
            Cache::set('query', $request->fullUrl());
            Cache::set('email', $email);
            Cache::set('token', $token);
        }

        return $response;
    }

}
