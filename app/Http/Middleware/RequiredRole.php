<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class RequiredRole
{
    /**
     * Destroy all caches
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $requiredRole
     * @return mixed
     * @throws ApiException
     */
    public function handle($request, Closure $next, $requiredRole)
    {
        // Validate role
        /** @var \App\Role $model */
        $role = \App\Role::where('name', $requiredRole)->first();

        /** @var \App\User $user */
        $user = Auth::user();

        if (!empty($user) && !$user->hasRole($role->uid)) {
            throw new ApiException('Not authorized to perform the action.', 401);
        }

        $response = $next($request);
        return $response;
    }
}