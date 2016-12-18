<?php

namespace App\Http\Middleware;

use Session;

use Closure;

class ShopifyAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // application is not authenticated with shopify
        if(!Session::has('shop')) {
            return redirect('auth');
        }

        return $next($request);
    }
}
