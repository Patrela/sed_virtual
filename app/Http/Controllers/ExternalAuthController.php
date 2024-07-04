<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class ExternalAuthController extends Controller
{
    public function tokenLogin(Request $request)
    {
        // Validar que la url origen esté registrada en CORS.php
        $origin = $request->header('Origin');
        $allowedOrigins = config('cors.allowed_origins');

        if (!in_array($origin, $allowedOrigins)) {
            return response()->json(['message' => 'Error - Origin not allowed.' , 'code' => 403], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Error - User not found.' , 'code' => 404], 404);
        }

        // Generar el token usando Sanctum
        $token = $user->createToken('api_token')->plainTextToken;

        // Guardar correo de usuario y token en la sesión
        Session::put('api_sed_session', [
            'email' => $user->email,
            'token' => $token,
        ]);

        return response()->json(['email' => $user->email,'token' => $token, 'message'=> 'User Verified', 'code' => 200], 200);
    }
}
