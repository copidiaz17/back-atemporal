<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class VerifyToken
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->cookie('atemporal_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

                
        
        // Busca el token en la base de datos usando el ID
        $userToken = PersonalAccessToken::findToken($authorizationHeader);

        if (!$userToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // Autentica al usuario asociado
        $user = User::find($userToken->tokenable_id);
        // Agrega el usuario autenticado al request
        $request->merge(['user' => $user]);

        return $next($request);
    }
}