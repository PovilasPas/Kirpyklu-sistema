<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HairSalonController;
use App\Http\Controllers\HairstyleController;
use App\Http\Controllers\HairdresserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('V1')->group(function()
{
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{city}', [CityController::class, 'show']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store'])->middleware('valid.json');
    Route::patch('/users/{user}', [UserController::class, 'update'])->middleware('valid.json');
    Route::delete('/users/{user}', [UserController::class, 'delete']);

    Route::get('/hair-salons', [HairSalonController::class, 'index']);
    Route::get('/hair-salons/{salon}', [HairSalonController::class, 'show']);
    Route::post('/hair-salons', [HairSalonController::class, 'store'])->middleware('valid.json');
    Route::put('/hair-salons/{salon}', [HairSalonController::class, 'update'])->middleware('valid.json');
    Route::delete('/hair-salons/{salon}', [HairSalonController::class, 'delete']);
    Route::prefix('/hair-salons/{salon}')->group(function()
    {
        Route::get('/hairdressers', [HairdresserController::class, 'index']);
        Route::get('/hairdressers/{hairdresser}', [HairdresserController::class, 'show']);
        Route::post('/hairdressers', [HairdresserController::class, 'store'])->middleware('valid.json');
        Route::put('/hairdressers/{hairdresser}', [HairdresserController::class, 'update'])->middleware('valid.json');
        Route::delete('/hairdressers/{hairdresser}', [HairdresserController::class, 'delete']);
        Route::prefix('/hairdressers/{hairdresser}')->group(function() 
        {
            Route::get('/hairstyles', [HairstyleController::class, 'index']);
            Route::get('/hairstyles/{hairstyle}', [HairstyleController::class, 'show']);
            Route::post('/hairstyles', [HairstyleController::class, 'store'])->middleware('valid.json');
            Route::patch('/hairstyles/{hairstyle}', [HairstyleController::class, 'update'])->middleware('valid.json');
            Route::delete('/hairstyles/{hairstyle}', [HairstyleController::class, 'delete']);
        });
    });
});
