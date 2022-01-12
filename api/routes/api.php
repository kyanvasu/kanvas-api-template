<?php

use Baka\Router\Route;
use Baka\Router\RouteGroup;

$publicRoutes = [
    Route::get('/')->controller('IndexController'),
    Route::get('/status')->controller('IndexController')->action('status'),
    Route::get('/lounges')->controller('LoungesController')->action('index'),
    Route::get('/lounges/{id}')->controller('LoungesController')->action('getById'),
    Route::get('/lounges/{id}/followers')->controller('LoungesController')->action('followers'),
    Route::get('/lounges/{id}/members')->controller('LoungesController')->action('members'),
    Route::get('/topics')->controller('TopicsController')->action('index'),

    Route::get('/search')->controller('SearchController')->action('index'),
    Route::get('/search/{query}/suggestion')->controller('SearchController')->action('suggestion'),
];

$privateRoutes = [
    Route::get('/users')->controller('UsersController')->action('index'),
    Route::get('/users/{id}')->controller('UsersController')->action('getById'),
    Route::post('users/{id}/activate')->controller('UsersController')->action('activate'),
    Route::put('/users/{id}')->controller('UsersController')->action('edit'),

    Route::post('/lounges')->controller('LoungesController')->action('create'),
    Route::put('/lounges/{id}')->controller('LoungesController')->action('edit'),
    Route::put('/lounges/{loungeId}/users/{userId}')->controller('LoungesController')->action('updateUser'),
    Route::post('/lounges/{loungeId}/users')->controller('LoungesController')->action('addUser'),
    Route::get('/lounges/{loungeId}/users')->controller('Lounges\UsersController')->action('index'),
    Route::get('/books')->controller('BooksController')->action('index'),

    Route::get('/lounges/{loungeId}/rooms')->controller('Rooms\RoomsController')->action('index'),
    Route::post('/lounges/{loungeId}/rooms')->controller('Rooms\RoomsController')->action('create'),
    Route::get('/lounges/{loungeId}/rooms/{id}')->controller('Rooms\RoomsController')->action('getById'),
    Route::put('/lounges/{loungeId}/rooms/{id}')->controller('Rooms\RoomsController')->action('edit'),
    Route::post('/lounges/{loungeId}/rooms/{id}/users')->controller('Rooms\UsersController')->action('addUser'),
    Route::get('/lounges/{loungeId}/rooms/{id}/users')->controller('Rooms\UsersController')->action('index'),
    Route::delete('/lounges/{loungeId}/rooms/{id}/users/{userId}')->controller('Rooms\UsersController')->action('removeUser'),

];

$routeGroup = RouteGroup::from($publicRoutes)
                ->defaultNamespace('Gewaer\Api\Controllers')
                ->addMiddlewares('auth.anonymous@before')
                ->defaultPrefix('/v1');

$privateRoutesGroup = RouteGroup::from($privateRoutes)
                ->defaultNamespace('Gewaer\Api\Controllers')
                ->addMiddlewares('auth.jwt@before', 'auth.acl@before')
                ->defaultPrefix('/v1');

/**
 * @todo look for a better way to handle this
 */
return array_merge(
    $routeGroup->toCollections(),
    $privateRoutesGroup->toCollections()
);
