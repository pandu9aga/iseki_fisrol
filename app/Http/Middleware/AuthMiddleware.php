<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('login_id')) {
            return redirect()->route('login')->withErrors(['accessDenied' => 'You must login first']);
        }

        if(session('login_type') != 2) {
            session()->forget('login_id');
            session()->forget('login_name');
            session()->forget('login_type');
            return redirect()->route('login')->withErrors(['accessDenied' => 'You must login with user account']);
        }

        return $next($request);
    }
}

