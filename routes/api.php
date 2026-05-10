<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\ResidentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Houses
    Route::apiResource('houses', HouseController::class);
    Route::post('houses/{house}/assign-resident', [HouseController::class, 'assignResident']);
    Route::post('houses/{house}/unassign-resident', [HouseController::class, 'unassignResident']);

    // Residents
    Route::apiResource('residents', ResidentController::class);
});
