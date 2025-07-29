<?php
// app/Http/Middleware/ForcePasswordChange.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->force_password_change) {
            if (!$request->routeIs('profile.*') && !$request->routeIs('logout')) {
                return redirect()->route('profile.index')
                    ->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}
