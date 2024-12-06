<?php

namespace App\Http\Controllers\Auth;


use Illuminate\View\View;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\UserVisitLogController;

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
        //Log::info(Auth::user()->getId());
        app(UserVisitLogController::class)->userVisitRegistry(Auth::user()->getId(), Auth::user()->trade_id);
        session(['SESSION_SECRET' =>config('services.api.token_connect')]);
        //Log::info("session user email.  " .session('current_user'));
        return redirect()->intended(route('products.index', absolute: false));
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
