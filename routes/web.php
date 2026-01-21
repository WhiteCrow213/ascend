<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\PreRegistrationController;

/*
|--------------------------------------------------------------------------
| Core Routes
|--------------------------------------------------------------------------
*/

Route::view('/login', 'login')->name('login');
Route::view('/dashboard', 'dashboard')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admissions Module (URL prefix: /admission)
|--------------------------------------------------------------------------
*/

Route::prefix('admission')->name('admission.')->group(function () {

    // Admissions Hub
    Route::view('/', 'admission.index')->name('index');

    // Pre-registration GRID (INBOX)
    Route::get('/pre-registration',
        [PreRegistrationController::class, 'index']
    )->name('prereg.grid');

    // Manual Pre-registration (Walk-in)
    Route::get('/pre-registration/manual',
        [PreRegistrationController::class, 'create']
    )->name('prereg.manual');

    Route::post('/pre-registration/manual',
        [PreRegistrationController::class, 'store']
    )->name('prereg.store');

    // Enrollment Hub (placeholder)
    Route::view('/enrollment', 'admission.enrollment.index')
        ->name('enrollment.index');

});
