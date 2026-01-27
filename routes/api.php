<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\PesertaController;
use App\Http\Controllers\API\UjianController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // auth
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // peserta
    Route::prefix('peserta')->group(function () {
        Route::get('/profil', [PesertaController::class, 'profil']);
        Route::get('/jadwal-aktif', [PesertaController::class, 'jadwalAktif']);
    });

    // ujian
    Route::prefix('ujian')->group(function () {
        Route::post('mulai', [UjianController::class, 'mulai']);
        Route::get('soal', [UjianController::class, 'soal']);
    });
});
