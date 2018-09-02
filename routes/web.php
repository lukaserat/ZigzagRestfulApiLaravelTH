<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'prefix' => '',
    'middleware' => ['nocache', 'hideserver', 'security', 'csp', 'cors', 'ensurejson', 'apiversion']
], function () use($router) {

    // define api routes
    $router->group([
        'prefix' => 'api'
    ], function () use($router) {

        $router->group(['middleware' => 'throttle:10,1'], function () use ($router) {
            $router->post('/login', 'AuthController@postLogin');
            $router->post('/logout', 'AuthController@logout');
        });

        // resource for user
        $router->group([
            'prefix' => 'users', // /api/users
        ], function () use($router) {
            $router->get('/', 'UserController@index');
            $router->get('/{user}', 'UserController@show');
            $router->post('/', 'UserController@store');
            $router->put('/{user}', 'UserController@update');
            $router->delete('/{user}', 'UserController@destroy');
        });

        // resource for client
        $router->group([
            'prefix' => 'clients' // /api/clients
        ], function () use($router) {
            $router->get('/', 'ClientController@index');
            $router->get('/{client}', 'ClientController@show');
            $router->post('/', 'ClientController@store');
            $router->put('/{client}', 'ClientController@update');
            $router->delete('/{client}', 'ClientController@destroy');
            $router->put('/{client}/authorize', 'ClientController@authorizeClient');
            $router->put('/{client}/de-authorize', 'ClientController@deAuthorizeClient');
        });

        // resource for phone
        $router->group([
            'prefix' => 'phones' // /api/phones
        ], function () use($router) {
            $router->get('/{owner}/', 'PhoneController@index');
            $router->get('/{owner}/{phone}', 'PhoneController@show');
            $router->post('/{owner}/', 'PhoneController@store');
            $router->put('/{owner}/{phone}', 'PhoneController@update');
            $router->delete('/{owner}/{phone}', 'PhoneController@destroy');
        });
    });

});


