<?php

use App\Http\Controllers\UserWeatherController;
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


Route::get('/api/user/dashboard', [UserWeatherController::class, 'dashboard']);
Route::get('/api/user/{id}/forecast', [UserWeatherController::class, 'userForecast'])->where('id', '[0-9]+');
Route::get('/api/user/{id}/forecast/refresh', [UserWeatherController::class, 'refresh'])->where('id', '[0-9]+');
