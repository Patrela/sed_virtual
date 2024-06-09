<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class VtexController extends Controller
{
    public function connect(Request $request, string $username)
    {
        try {
            $useremail = $request->header('x-api-user');
            $token = $request->bearerToken();
            //Log::info("intro to  Vtex  " . $username);
            // Validate required headers
            if (!$useremail || !$token) {
                return response()->json([
                    'error' => 'Missing required Authorization Data',
                    'code' => 401
                ], 401);
            }

            // Validate token (consider using Laravel's built-in token authentication)
            if (!$this->isValidToken($token)) { // Replace with your token validation logic
                return response()->json([
                    'error' => 'Invalid token',
                    'code' => 402,
                ], 402);
            }

            // Find user using company and email (consider using model relationships for better structure)
            $user = app(LogController::class)->loginAPI($useremail);
            if($user){
                return response()->json([
                    'id' => $user->id,
                    'trade_id' => $user->trade_id,
                    'name' =>  $username,
                    'code' => 200,
                ], 200);
            /*
            //init the password
            //$password = "Test123456";
            $password = "Test" . sprintf("%05d", rand(147841,999999)); //+ rand(547841,999999);
            $user->password = Hash::make($password);
            $user->remember_token =$password;
            $user->save();

            // Handle user existence and return appropriate response
            if ($user) {
                //$user->password = $password;
                Auth::login($user);
                session(['current_trade' => $user->trade_id]);
                session(['current_user' =>  $user->email]);
                //Log::info("user Vtex  " . $user->email);

                //return redirect('/products');

                return response()->json([
                    'id' => $user->id,
                    'name' =>  $username,
                ], 200);

                //return app(LogController::class)->authenticateAPI($request, $useremail);
                */
            } else {
                    return response()->json([
                        'error' => "User {$username} not found",
                        'code' => 404,
                    ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }

    /***
     * constant token for Epicor
     * @param
     * @return SED Epicor Token
     */
    private function isValidToken($token): bool
    {
        $generalToken = "HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y";
        // Replace with your actual token validation logic (e.g., using a JWT library)
        return $token == $generalToken; // Change this to return true/false based on token validity
    }

}
