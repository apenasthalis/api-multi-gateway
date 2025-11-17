<?php

use App\Http\Middleware;
use App\Http\Route;

Route::get('/', 'HomeController@index');
Route::post('/login', 'LoginController@login');
Route::post('/client', 'ClientController@store');

$route = new Route;
$middleware = new Middleware();
$route->middleware(['jwt'])->group(function() {
    Route::get('/client/{id}', 'ClientController@show'); 
    Route::get('/client', 'ClientController@index');
    Route::put('/client', 'ClientController@update');
    Route::delete('/client', 'ClientController@index');
    Route::post('/mercadopago', 'MercadoPagoController@store');
    Route::get('/mercadopago', 'MercadoPagoController@show');
    Route::patch('/mercadopago/{id}', 'MercadoPagoController@verify');

    Route::post('/simuladorpago', 'SimuladorPagoController@store');
    Route::get('/simuladorpago', 'SimuladorPagoController@show');
    Route::patch('/simuladorpago/{id}', 'SimuladorPagoController@verify');
});
