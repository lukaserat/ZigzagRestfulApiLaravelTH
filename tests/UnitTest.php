<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class UnitTest extends TestCase
{
    use Helper;

    /** @test */
    public function root_is_an_admin() {
        $user = App\User::root();
        $role = App\Role::fromName('admin');
        $this->assertTrue($user->hasRole($role->uid));
    }

    /** @test */
    public function assign_role() {
        // create a non admin user
        $user = $this->createNonAdmin();
        $role = App\Role::fromName('admin');
        $this->assertFalse($user->hasRole($role->uid));

        // assign role
        $user->assignRole($role->uid);
        $this->assertTrue($user->hasRole($role->uid));
    }

    /** @test */
    public function revoke_role()
    {
        // create an admin user
        $user = $this->createAdmin();
        $role = App\Role::fromName('admin');
        $this->assertTrue($user->hasRole($role->uid));

        // revoke role
        $user->revokeRole($role->uid);
        $this->assertFalse($user->hasRole($role->uid));
    }

    /** @test */
    public function role_admin_only_middleware_should_work() {
        // a route that an admin is only allowed
        $responseData = ['a' => 1];
        $this->app->router->group([
            'middleware' => 'role:admin',
        ], function () use($responseData) {
            Route::post('admin-only', function () use($responseData) {
                return response()->json($responseData);
            });
        });

        // create an admin user and make it the actor of the request
        $user = $this->createAdmin();
        $this->actingAs($user);

        $this->post('admin-only', []);
        $this->assertResponseOk();
        $this->seeJson($responseData);

        // create a non-admin user and make it the actor of the request
        $user = $this->createNonAdmin();
        $this->actingAs($user);
        $this->post('admin-only', []);
        $this->assertResponseStatus(401);
    }

    /** @test */
    public function api_version_middleware_should_work() {
        $responseData = ['a' => 1];
        $this->app->router->group([
            'middleware' => 'apiversion',
        ], function () use($responseData) {
            Route::post('api-version', function () use($responseData) {
                return response()->json($responseData);
            });
        });

        $this->post('api-version', [], ['API-VERSION' => 'v1']);
        $this->assertResponseOk();
    }

    /** @test */
    public function ensurejson_middleware_should_work() {
        $responseData = ['a' => 1];
        $this->app->router->group([
            'middleware' => 'ensurejson',
        ], function () use($responseData) {
            Route::post('content-type-json-only', function () use($responseData) {
                return response()->json($responseData);
            });
        });

        $this->post('content-type-json-only', [], [
            'Content-Type' => 'application/json'
        ]);
        $this->assertResponseOk();
    }

//    /** @test */
//    public function duplicate_phone_numbers_should_not_work()
//    {
//        $this->assertTrue(true);
//    }
//
//    /** @test */
//    public function invalid_phone_number_format_should_not_work()
//    {
//        $this->assertTrue(true);
//    }
}
