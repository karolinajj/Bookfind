<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class Checkout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $currentRoute = $request->route()->getName();

        if ($currentRoute === 'payment' && !Session::has('shipping_address')) {
            return redirect()->route('address')->withErrors('Please fill in the shipping address before proceeding.');
        }

        return $next($request);
    }
}
