<?php

namespace UltraClarity\Analytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('ultraclarity.dashboard_auth.enabled')) {
            return $next($request);
        }

        $user = $request->getUser();
        $password = $request->getPassword();

        if (
            hash_equals((string) config('ultraclarity.dashboard_auth.email'), (string) $user)
            && hash_equals((string) config('ultraclarity.dashboard_auth.password'), (string) $password)
        ) {
            return $next($request);
        }

        return response('Authentication required.', 401, ['WWW-Authenticate' => 'Basic realm="PrismPath"']);
    }
}

