<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserLevel {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next) {
        $userLevel = session('user_level');

        if ($userLevel !== -1) {
            // You can customize this response as per your requirement.
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
