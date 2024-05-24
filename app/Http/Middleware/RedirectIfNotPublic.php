<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotPublic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next)
    {  //     return $next($request)
        $path = $request->getPathInfo();

        if ($path === '/' || strpos($path, '/public') !== 0 || strpos($path, '/ppal') !== 0) {
            return redirect('/login');
        }

        return $next($request);
    }
}




