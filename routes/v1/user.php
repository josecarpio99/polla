<?php

/* registration */

// $router->post('/register', [ 'as' => 'register', 'uses' => 'AuthController@register']);

/* login */
$router->post('/login', [ 'as' => 'login', 'uses' => 'AuthController@login']);

/* restrict route */
$router->group(['middleware' => 'auth'], function () use ($router) {

    /* get user profile */
    // $router->get('/profile', [ 'as' => 'profile', 'uses' => 'AuthController@profile']);

    /* logout user */
    $router->post('/logout', [ 'as' => 'logout', 'uses' => 'AuthController@logout']);

    /* refresh token */
    // $router->get('/refresh-token', [ 'as' => 'refreshToken', 'uses' => 'AuthController@refresh']);

    $router->group(['middleware' => 'role:superadmin'], function () use ($router) {

        $router->group(['prefix' => 'user', 'as' => 'user'], function () use ($router) {
            $router->get('/', ['as' => 'index', 'uses' => 'UserController@index']);
            $router->get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
            $router->post('/', ['as' => 'store', 'uses' => 'UserController@store']);
            $router->put('/{id}', ['as' => 'update', 'uses' => 'UserController@update']);
            $router->delete('/{id}', ['as' => 'destroy', 'uses' => 'UserController@destroy']);

            $router->post('add-pos', ['uses' => 'AssignPosToUserController']);
        });

        $router->get('/setting', ['uses' => 'SettingController@index']);
        $router->put('/setting', ['uses' => 'SettingController@update']);
    });



});
