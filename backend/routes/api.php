<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VeterinarianController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});


// Rotas protegidas por autenticação
Route::group(['middleware' => 'auth:api'], function () {

    Route::middleware('auth:api')->get('appointments/my', [AppointmentController::class, 'myAppointments']);

    // Rotas de marcações
    Route::apiResource('appointments', AppointmentController::class);

    // Rotas de veterinários
    Route::apiResource('veterinarians', VeterinarianController::class);
    // Rota para médicos verem suas próprias marcações
    Route::get('veterinarian/appointments', [AppointmentController::class, 'getAppointmentsByVeterinarian']);
});
