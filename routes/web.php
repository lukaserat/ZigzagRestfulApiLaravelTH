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
});
