<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DokterController;
use App\Http\Controllers\API\PasienController;
use App\Http\Controllers\API\PoliController;
use App\Http\Controllers\API\ObatController;

Route::get('/', function () {
    return response()->json(["message" => "API is working", "status" => "ok"]);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('dokter', DokterController::class);
    Route::apiResource('pasien', PasienController::class);
    Route::apiResource('poli', PoliController::class);
    Route::apiResource('obat', ObatController::class);
});
