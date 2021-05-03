<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLtiLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('lti_session_exists')) {
            return response("<html><body><h3>Sorry, your session has expired.  Please relaunch this tool through your LMS.</h3></body></html>");
        }

        return $next($request);
    }
}
