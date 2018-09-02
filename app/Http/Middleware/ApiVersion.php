<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ApiException;

class ApiVersion
{
    /**
     * Destroy all caches
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws ApiException
     */
    public function handle($request, Closure $next)
    {
        $version = $request->input('version', $request->headers->get('API-VERSION', null));
        if (!$version) {
            throw new ApiException('Unknown api version.', 400);
        }
        return $next($request);
    }
}