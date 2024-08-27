<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\ProfileController;
use Illuminate\Validation\ValidationException;

class RoleProfileController extends Controller
{
    public function index(){
        $user = new User(); // Assuming User is your model
        $user->name = "";
        $user->email = "";
        $user->trade_id= 0;
        $user->role_type= 0;
        return view('profile.roles', ['user' => $user]);
    }

    public function searchProfileEmail(string $email){
        if (app(ProfileController::class)->hasAbility(Auth::user()->email, 'user-edit')) {
            $user = User::where( 'email', "{$email}")->first();
            return  $user;
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);
    }

    public function updateRoleProfile(Request $request, string $email, string $role_type){
         $userLogged =  $request->input('sender_email'); // Auth::user()->email;
         //Log::info("user logged", ['userLogged' => $userLogged]);

        if (app(ProfileController::class)->hasAbility($userLogged, 'user-edit')) {
            //Log::info(['email' => $email, 'role_type' => $role_type]);
            $user = User::where( 'email', "{$email}")->first();
            if(!$user) {
                return response()->json([
                    'message' => 'User not found.',
                    'code' => 404,
                ], 404);
            }
            if($user['role_type'] !== $role_type) {
                switch ($role_type) {
                    case User::ALLROLES["Administrator"]:
                            $user->createToken('profile', ['user-list','user-create','user-edit', 'user-show', 'user-delete']);
                        case User::ALLROLES["Developer"]:
                            $user->createToken('api', ['product-import', 'app-validation','document-read']);
                            break;
                }
                $user['role_type'] = $role_type;
                $user->save();
            }
            return response()->json([
                'message' => 'User Profile updated',
                'code' => 200,
            ], 200);
        }
        return response()->json([
            'message' => 'Unauthorized - Access is denied due to invalid credentials.',
            'code' => 401,
        ], 401);


    }
}
