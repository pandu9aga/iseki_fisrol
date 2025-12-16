<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('login_id')) {
            return redirect()->route('login')->withErrors(['accessDenied' => 'You must login first']);
        }

        if(session('login_type') != 1) {
            session()->forget('login_id');
            session()->forget('login_name');
            session()->forget('login_type');
            return redirect()->route('login')->withErrors(['accessDenied' => 'You must login with admin account']);
        }

        return $next($request);
    }
}

