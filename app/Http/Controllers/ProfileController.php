<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $roltype = $request->user()->role_type;
        $email = $request->user()->email;
        $request->user()->save();
        $users = User::where('email', "{$email}")->get();

        if ($users) {
            $user = $users->first();
            Log::error("new user.  " . $user->email);
            // abilities
            switch ($roltype) {
                case 1: //Administrator
                    $user->createToken('profile', ['user-list', 'user-create', 'user-edit', 'user-show', 'user-delete']);
                    $user->createToken('operation', ['product-list', 'product-show', 'product-create', 'product-edit', 'product-delete']);
                    $user->createToken('api', ['product-import', 'app-validation']);
                    $user->createToken('post', ['post-create']);
                    break;
                case 2: //Staff
                    $user->createToken('operation', ['product-list', 'product-show', 'product-create', 'product-edit', 'product-delete']);
                    $user->createToken('api', ['product-import', 'app-validation']);
                    break;
                case 3: //Trade
                    $user->createToken('operation', ['product-list', 'product-show', 'product-create', 'product-edit', 'product-delete']);
                    $user->createToken('api', ['product-import', 'app-validation']);
                    break;
                case 4: //Reseller
                    $user->createToken('operation', ['product-list', 'product-show', 'product-create', 'product-edit', 'product-delete']);
                    $user->createToken('api', ['product-import', 'app-validation']);
                    break;
                case 5: //Support
                case 6: //Developer
                    $user->createToken('operation', ['product-list', 'product-show', 'product-create', 'product-edit', 'product-delete']);
                    $user->createToken('api', ['product-import', 'app-validation']);
                    break;
            }
        } else {
            Log::error("new user email not found " . $email);
        }
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();
        //Log::info("ProfileController.destroy Activated " );
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('login'); // '/' 'home'
    }
    /*PVR sanctum read user sanctum abilities */
    public function userAbilities(string $username)
    {
        $user = User::where('name',"{$username}")->first();
        if(!$user){
            return response()->json([
                'error' => 'user not found',
                'code' => 404,
            ], 404);
        }
        $abilities = $user->tokens()->pluck('abilities'); // Get ability names ; abilities
        return response()->json($abilities, 200); // Return abilities as JSON
    }
}
