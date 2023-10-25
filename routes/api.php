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

Route::prefix('V1')->group(function()
{
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/register', [UserController::class, 'store'])->middleware('valid.json');
    Route::post('/users/login', [UserController::class, 'authenticate'])->middleware('valid.json');
    Route::post('/users/logout', [UserController::class, 'logout'])->middleware('auth:api');
    Route::post('/users/refresh', [UserController::class, 'refresh'])->middleware('auth:api');
    Route::patch('/users/{user}', [UserController::class, 'update'])->middleware('auth:api', 'valid.json');
    Route::delete('/users/{user}', [UserController::class, 'delete'])->middleware('auth:api');

    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{city}', [CityController::class, 'show']);

    Route::prefix('/cities/{city}')->group(function ()
    {
        Route::get('/hair-salons', [HairSalonController::class, 'index']);
        Route::get('/hair-salons/{salon}', [HairSalonController::class, 'show']);
        Route::post('/hair-salons', [HairSalonController::class, 'store'])->middleware('auth:api', 'can:create,App\Models\HairSalon' , 'valid.json');
        Route::put('/hair-salons/{salon}', [HairSalonController::class, 'update'])->middleware('auth:api', 'can:update,salon' ,'valid.json');
        Route::delete('/hair-salons/{salon}', [HairSalonController::class, 'delete'])->middleware('auth:api', 'can:delete,salon');
        Route::prefix('/hair-salons/{salon}')->group(function()
        {
            Route::get('/hairdressers', [HairdresserController::class, 'index']);
            Route::get('/hairdressers/{hairdresser}', [HairdresserController::class, 'show']);
            Route::post('/hairdressers', [HairdresserController::class, 'store'])->middleware('auth:api', 'can:create,App\Models\Hairdresser', 'valid.json');
            Route::put('/hairdressers/{hairdresser}', [HairdresserController::class, 'update'])->middleware('auth:api', 'can:update,hairdresser', 'valid.json');
            Route::delete('/hairdressers/{hairdresser}', [HairdresserController::class, 'delete'])->middleware('auth:api', 'can:delete,hairdresser');
            Route::prefix('/hairdressers/{hairdresser}')->group(function() 
            {
                Route::get('/hairstyles', [HairstyleController::class, 'index']);
                Route::get('/hairstyles/{hairstyle}', [HairstyleController::class, 'show']);
                Route::post('/hairstyles', [HairstyleController::class, 'store'])->middleware('auth:api', 'can:create,App\Models\Hairstyle' , 'valid.json');
                Route::patch('/hairstyles/{hairstyle}', [HairstyleController::class, 'update'])->middleware('auth:api', 'can:update,hairstyle' ,'valid.json');
                Route::delete('/hairstyles/{hairstyle}', [HairstyleController::class, 'delete'])->middleware('auth:api', 'can:delete,hairstyle');
            });
        });
    });
});
