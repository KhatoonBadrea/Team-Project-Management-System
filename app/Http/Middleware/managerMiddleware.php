<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Project; // Assuming projects model
use Tymon\JWTAuth\Exceptions\JWTException;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next)  
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        $userRole = $user->projects()->wherePivot('role', 'manager')->exists();

        if (!$userRole) {
            return response()->json(['error' => 'Unauthorized: You must be a manager to perform this action'], 403);
        }
 
        return $next($request);
    }
}
