<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        // ❌ Tidak pakai Auth::check()
        if (!session('login')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
