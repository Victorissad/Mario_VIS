<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('toad_user')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
