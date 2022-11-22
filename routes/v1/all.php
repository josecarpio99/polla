<?php

/* registration */

// $router->post('/register', [ 'as' => 'register', 'uses' => 'AuthController@register']);

/* login */
$router->post('/login', [ 'as' => 'login', 'uses' => 'AuthController@login']);

$router->get('/plays', ['as' => 'index', 'uses' => 'PlayController@index']);
$router->get('/plays/{id}', ['as' => 'show', 'uses' => 'PlayController@show']);
$router->get('/plays/{id}/ranking', ['as' => 'show', 'uses' => 'RankingTicketsController']);
/* restrict route */
$router->group(['middleware' => 'auth'], function () use ($router) {

    /* get user profile */
    $router->get('/profile', [ 'as' => 'profile', 'uses' => 'AuthController@profile']);

    /* logout user */
    $router->post('/logout', [ 'as' => 'logout', 'uses' => 'AuthController@logout']);


    $router->group(['prefix' => 'users', 'as' => 'users'], function () use ($router) {
        $router->get('/', ['as' => 'index', 'uses' => 'UserController@index']);
        $router->get('/pos', ['as' => 'pos', 'uses' => 'UserController@pos']);
        $router->get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);

        $router->group(['middleware' => 'role:superadmin'], function () use ($router) {
            $router->post('/', ['as' => 'store', 'uses' => 'UserController@store']);
            $router->put('/{id}', ['as' => 'update', 'uses' => 'UserController@update']);
            $router->delete('/{id}', ['as' => 'destroy', 'uses' => 'UserController@destroy']);

            $router->post('add-pos', ['uses' => 'AssignPosToUserController']);
        });

    });

    $router->group(['prefix' => 'plays', 'as' => 'plays'], function () use ($router) {

        $router->get('/{playId}/user/{userId}/sales', ['uses' => 'SalesAdjustmentController']);

        $router->group(['middleware' => 'role:superadmin'], function () use ($router) {
            $router->post('/', ['as' => 'store', 'uses' => 'PlayController@store']);
            $router->put('/{id}', ['as' => 'update', 'uses' => 'PlayController@update']);
            $router->delete('/{id}', ['as' => 'destroy', 'uses' => 'PlayController@destroy']);
            $router->post('/{id}/toggle', ['as' => 'store', 'uses' => 'TogglePlayStatusController']);
            $router->put('/{id}/update-points', ['as' => 'update', 'uses' => 'UpdateRacesPointsController']);
        });

        $router->group(['prefix' => '{playId}/tickets', 'as' => 'tickets'], function () use ($router) {
            $router->get('/', ['as' => 'store', 'uses' => 'TicketController@index']);
            $router->get('/{id}', ['as' => 'store', 'uses' => 'TicketController@show']);
            $router->post('/', ['as' => 'store', 'uses' => 'TicketController@store']);

            $router->group(['middleware' => 'role:superadmin'], function () use ($router) {
                $router->put('/{id}', ['as' => 'store', 'uses' => 'TicketController@update']);
                $router->delete('/{id}', ['as' => 'store', 'uses' => 'TicketController@destroy']);
            });
        });

    });

    $router->group(['middleware' => 'role:superadmin'], function () use ($router) {
        $router->get('/settings', ['uses' => 'SettingController@index']);
        $router->put('/settings', ['uses' => 'SettingController@update']);
    });


    $router->get('/racetracks', ['uses' => 'RaceTrackController@index']);


    // TICKETS
    // $router->group(['prefix' => 'plays', 'as' => 'plays'], function () use ($router) {
    //     $router->group(['prefix' => '{playId}/tickets', 'as' => 'tickets'], function () use ($router) {
    //         $router->get('/', ['as' => 'store', 'uses' => 'TicketController@index']);
    //         $router->get('/{id}', ['as' => 'store', 'uses' => 'TicketController@show']);
    //         $router->post('/', ['as' => 'store', 'uses' => 'TicketController@store']);
    //         $router->put('/{id}', ['as' => 'store', 'uses' => 'TicketController@update']);
    //         $router->delete('/{id}', ['as' => 'store', 'uses' => 'TicketController@destroy']);
    //     });
    // });




});

