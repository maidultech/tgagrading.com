<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class ActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status != 1) {
            Auth::logout(); // Log out the inactive user
            Toastr::error('Your account is disabled. Please contact support.', 'Error');

            return redirect()->route('login');
        }

        return $next($request);
    }
}
