<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Trade;
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
        $user = new User();
        $user->name = "";
        $user->email = "";
        $user->trade_id= 0;
        $user->role_type= 0;
        $users = User::where( 'role_type', User::ALLROLES["Developer"])->get()->toArray();
        return view('profile.roles', ['user' => $user, 'users' => $users]);
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
                        $user->createToken('api', ['product-import', 'app-validation','document-read']);
                        break;
                    case User::ALLROLES["Developer"]:
                        $user->createToken('api', ['product-import', 'app-validation','document-read']);
                        // update user email for trade developer profile
                        if($user->trade_id >1){
                            $trade= Trade::where('trade_id', "{$user->trade_id}")->first();
                            if($trade !== false){
                                $trade->email = $user->email;
                                $trade->save();
                            }
                        }
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
