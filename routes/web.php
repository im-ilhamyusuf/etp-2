<?php

use App\Http\Controllers\API\UjianController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // if (auth()->check()) {
    //     $user = auth()->user();

    //     return redirect()->to(match ($user->role) {
    //         'admin' => '/admin',
    //         'user'  => '/peserta',
    //         default => '/',
    //     });
    // }

    // return redirect('/peserta/login');

    return view('home');
});

// ujian
Route::get('/ujian/sertifikat', [UjianController::class, 'sertifikat'])->name('ujian-sertifikat');
