<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'all systems are a go',
        'users' => \App\Models\User::all(),
    ]);
});


Route::get('/api/user/dashboard',[\App\Http\Controllers\UserWeatherController::class, 'dashboard']);
Route::get('/api/user/{id}/forecast',[\App\Http\Controllers\UserWeatherController::class, 'userForecast']);
Route::get('/api/user/{id}/forecast/refresh',[\App\Http\Controllers\UserWeatherController::class, 'refresh']);
