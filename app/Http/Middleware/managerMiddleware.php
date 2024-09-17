<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Project;
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

        $projectId = $request->route('project_id') ?? $request->input('project_id');

        if (!$projectId) {
            return response()->json(['error' => 'Project ID is missing'], 400);
        }

        $isProjectManager = $user->projects()
            ->where('projects.id', $projectId)
            ->wherePivot('role', 'manager')
            ->exists();

        if (!$isProjectManager) {
            return response()->json(['error' => 'Unauthorized: You must be the manager of this project to perform this action'], 403);
        }

        return $next($request);
    }
}
