<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class VtexController extends Controller
{
    public function connect(Request $request)
    {
        try {

            $company = $request->header('x-api-company');
            $useremail = $request->header('x-api-user');
            $token = $request->bearerToken();
            $name = $request->query('name');

            // Validate required headers
            if (!$company || !$useremail || !$token) {
                return response()->json([
                    'error' => 'Missing required Authorization Data',
                ], 401);
            }

            // Validate token (consider using Laravel's built-in token authentication)
            if (!$this->isValidToken($token)) { // Replace with your token validation logic
                return response()->json([
                    'error' => 'Invalid token',
                ], 401);
            }

            // Find user using company and email (consider using model relationships for better structure)
            $user = User::where([['trade_id', $company], ['email', $useremail]])->first();

            // Handle user existence and return appropriate response
            if ($user) {
                session(['current_trade' =>  $company]);
                session(['current_user' =>  $user->id]);
                //return redirect('/products');
                return response()->json([
                    'id' => $user->id,
                    'company' => $company,
                    'email' => $useremail,
                    'name' => $name,
                ], 200);
            } else {
                $response = app(SedController::class)->validateCustomerUSer( $company,$useremail);
                if ($response->successful()){
                    $jsonResponse = $response->json();
                    return $jsonResponse;
                }
                else {
                    return response()->json([
                        'error' => 'User not found',
                    ], 404);
                }

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
