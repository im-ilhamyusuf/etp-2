<?php

use App\Http\Controllers\API\UjianController;
use App\Http\Controllers\SuratKeteranganController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// ujian
Route::get('/ujian/sertifikat', [UjianController::class, 'sertifikat'])->name('ujian-sertifikat');

Route::get('/sk/download', [SuratKeteranganController::class, 'download'])
    ->name('sk.download')
    ->middleware('auth');

Route::get('/sk/download-lampiran', [SuratKeteranganController::class, 'downloadLampiran'])
    ->name('sk.download.lampiran')
    ->middleware('auth');
