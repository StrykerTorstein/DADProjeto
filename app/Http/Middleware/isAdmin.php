<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class isAdmin
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
        if($request->user() != null && $request->user()->isAdministrator()){
            return $next($request);
        }
        throw new AccessDeniedHttpException("Unauthorized. User is not an adminstrator");
    }
}
