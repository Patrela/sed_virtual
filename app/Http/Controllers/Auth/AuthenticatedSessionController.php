<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        session(['current_user' =>  Auth::user()->email]);
        session(['SESSION_SECRET' =>config('services.api.token_connect')]);
        //Log::info("session user email.  " .session('current_user'));
        return redirect()->intended(route('product.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        //Log::info("AuthenticatedSessionController.destroy Activated " );
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
