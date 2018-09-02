<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ApiException;

class EnsureJson
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
        // (pre) must have application/json as content type
        if (!$request->isJson()) {
            throw new ApiException('Invalid content type.', 400);
        }

        $response = $next($request);

        // (post) set response content type to json
        $response->header('Content-Type', 'application/json');
        return $response;
    }
}