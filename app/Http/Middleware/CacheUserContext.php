<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CacheUserContext
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            
            $userContext = Cache::remember("user_context_{$userId}", 60 * 24, function () use ($userId) {
                $user = Auth::user();
                $profile = DB::table('user_profiles')->where('user_id', $userId)->first();
                
                return (object) [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'avatar_url' => $profile ? $profile->avatar_url : null,
                ];
            });

            View::share('userContext', $userContext);
        }

        return $next($request);
    }
}
