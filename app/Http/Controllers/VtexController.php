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
                    'result' => ' Error Missing required Authorization Data',
                    'code' => 401
                ], 401);
            }

            // Validate token (consider using Laravel's built-in token authentication)
            if (!$this->isValidToken($token)) { // Replace with your token validation logic
                return response()->json([
                    'result' => 'Error Invalid token',
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
                        'result' => 'ok',
                        'code' => 200,
                    ], 200);
            } else {
                    return response()->json([
                        'result' => "Error User {$username} not found",
                        'code' => 404,
                    ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'result' => 'Error ' .$e->getMessage(),
                'code' => $e->getCode(),
            ], 403);
        }
    }

    /***
     * constant token for Epicor
     * @param
     * @return SED Epicor Token validation
     */
    private function isValidToken($token): bool
    {
        if( env('APP_ENV') === 'production' ) {
            $generalToken = "TfBS4ZNFr9JxUqKQjiGmTanp29Ocix8TJORDCnTo4wg8q";
        } else {
            $generalToken = "HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y";
        }
        return $token === $generalToken; // Change this to return true/false based on token validity
    }

}
