<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class UpdateLastActivity
{ 
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        try {

            if ($user = JWTAuth::parseToken()->authenticate()) {
                DB::table('project_user')
                    ->where('user_id', $user->id)
                    ->update([
                        'last_activity' => Carbon::now(),
                    ]);
            }
        } catch (JWTException $e) {
        }

        return $next($request);
    }
}
