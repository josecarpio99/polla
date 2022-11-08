<?php

/* test response */
$router->get('/ping', [ 'as' => 'ping', function () use ($router) {
    return 'pong';
}]);

/* lumen version */
$router->get('/version', [ 'as' => 'version', function () use ($router) {
    dd(config('settings'));
    return $router->app->version();
}]);

/* test role admin */
$router->get('/role/admin', ['middleware' => ['auth', 'role:pos'],function () use ($router) {
    return response('llego');
}]);
