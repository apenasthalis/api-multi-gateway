<?php

use App\Http\Middleware;
use App\Http\Route;

Route::get('/', 'HomeController@index');
Route::post('/login', 'LoginController@login');

$route = new Route;
$middleware = new Middleware();
$route->middleware(['jwt'])->group(function() {
    Route::get('/client/{id}', 'ClientController@show'); 
    Route::get('/client', 'CientController@index');
    Route::post('/client', 'CientController@index');
    Route::put('/client', 'CientController@index');
    Route::delete('/client', 'CientController@index');
});