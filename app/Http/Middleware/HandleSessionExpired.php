<?php
// app/Http/Middleware/HandleSessionExpired.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HandleSessionExpired
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
