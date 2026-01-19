<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/admission', function () {
    return view('admission.index');
})->name('admission.index');

Route::get('/admission/pre-registration', function () {
    return view('admission.pre_registration.index');
})->name('admission.pre_registration.index');

Route::get('/admission/enrollment', function () {
    return view('admission.enrollment.index');
})->name('admission.enrollment.index');

use App\Http\Controllers\Admissions\PreRegistrationController;

Route::prefix('admission')->group(function () {
    Route::get('/pre-registration/manual', [PreRegistrationController::class, 'create'])
        ->name('admission.prereg.manual');

    Route::post('/pre-registration/manual', [PreRegistrationController::class, 'store'])
        ->name('admission.prereg.store');
});
