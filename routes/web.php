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
| Admissions (URL prefix: /admission)
|--------------------------------------------------------------------------
*/

Route::prefix('admission')->name('admission.')->group(function () {

    // Admission Hub
    Route::view('/', 'admission.index')->name('index');

    // Pre-registration Hub
    Route::view('/pre-registration', 'admission.pre_registration.index')
        ->name('pre_registration.index');

    // Enrollment Hub
    Route::view('/enrollment', 'admission.enrollment.index')
        ->name('enrollment.index');

    // Manual Pre-registration (Walk-in)
    Route::get('/pre-registration/manual', [PreRegistrationController::class, 'create'])
        ->name('prereg.manual');

    Route::post('/pre-registration/manual', [PreRegistrationController::class, 'store'])
        ->name('prereg.store');
});
