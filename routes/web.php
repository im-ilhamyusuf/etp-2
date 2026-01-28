<?php

use App\Http\Controllers\API\UjianController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('after.login');
    }

    return redirect()->route('login');
});

// ujian
Route::get('/ujian/sertifikat', [UjianController::class, 'sertifikat'])->name('ujian-sertifikat');

http: //127.0.0.1:8000/ujian/sertifikat?peserta_jadwal_id=18

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class);
});

Route::get('/after-login', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin'   => redirect('/admin'),
        'user' => redirect('/peserta'),
        default   => abort(403),
    };
})->name('after.login')->middleware('auth');
