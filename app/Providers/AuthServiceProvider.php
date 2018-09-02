<?php

namespace App\Providers;

use App\User;
use App\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        // admin can do all
        Gate::before(function ($user) {
            if ($user && $user->hasRole(Role::fromName('admin')->uid)) {
                return true;
            }
            return null;
        });
        // policy for clients
        Gate::define('update-client', function ($user, $instance) {
            // only the owner of the info and the admin
            return $user->id === $instance->id;
        });

        Gate::define('view-client', function ($user, $instance) {
            // only the owner of the info and the admin
            return $user->id === $instance->id;
        });

        Gate::define('list-client', function ($user) {
            // only the owner of the info and the admin
            return Gate::allows('do-all');
        });

        Gate::define('delete-client', function ($user) {
            // only the admin
            return false;
        });

        Gate::define('deauthorize-client', function ($user) {
            // only the admin
            return false;
        });

        Gate::define('authorize-client', function ($user) {
            // only the admin
            return false;
        });

        // user gates
        Gate::define('create-user', function ($user) {
            // creating user is allowed to all authorized client and admin
            return ($user->isAuthorized() && $user->isClient());
        });

        Gate::define('update-user', function ($user, $instance) {
            // only the owner of the info and the admin
            return $user->id === $instance->id;
        });

        Gate::define('view-user', function ($user, $instance) {
            // only the owner of the info and the admin
            return $user->id === $instance->id;
        });

        Gate::define('list-user', function ($user) {
            // only the owner of the info and the admin
            return false;
        });

        Gate::define('delete-user', function ($user) {
            // only the admin
            return false;
        });

        // phone gates
        Gate::define('create-phone', function ($user, $owner) {
            // user can add phone numbers as much as he/she wants
            return !$user->isClient();
        });

        Gate::define('update-phone', function ($user, $owner, $instance) {
            // only the owner of the info and the admin
            return ($user->id == $owner->id && $owner->id == $instance->user_id);
        });

        Gate::define('view-phone', function ($user, $owner, $instance) {
            // only the owner of the info and the admin
            return ($user->id == $owner->id && $owner->id == $instance->user_id);
        });

        Gate::define('list-phone', function ($user, $owner) {
            // only the owner of the info and the admin
            return $user->id == $owner->id;
        });

        Gate::define('delete-phone', function ($user, $owner, $instance) {
            // only the admin
            return ($user->id == $owner->id && $owner->id == $instance->user_id);
        });
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
