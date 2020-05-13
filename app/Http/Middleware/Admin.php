<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class Admin - middleware for admin checking.
 */
class Admin
{
    public function handle($request, Closure $next)
    {
        if ( Auth::check() ) {
            $user = Auth::user();
            if (in_array($user->access, [0, 1, 2, 3, 4, 5, 6])) {
                return $next($request);
            }
        }

        return redirect(url('/login'));
    }
}
