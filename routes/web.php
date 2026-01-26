<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\PreRegistrationController;
use App\Http\Controllers\GeoController;

/*
|--------------------------------------------------------------------------
| GEO DROPDOWN ROUTES (Address Cascading)
|--------------------------------------------------------------------------
*/
Route::get('/geo/provinces/{region_psgc}', [PreRegistrationController::class, 'provinces']);
Route::get('/geo/cities/{province_psgc}', [PreRegistrationController::class, 'cities']);
Route::get('/geo/barangays/{citymun_psgc}', [PreRegistrationController::class, 'barangays']);

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

    // Pre-registration SUCCESS page (after submission)
    Route::get('/pre-registration/{studID}/success',
        [PreRegistrationController::class, 'success']
    )->name('prereg.success');

    // Pre-registration PDF (view/download)
    Route::get('/pre-registration/{studID}/pdf',
        [PreRegistrationController::class, 'pdf']
    )->name('prereg.pdf');


    // Enrollment Hub (placeholder)
    Route::view('/enrollment', 'admission.enrollment.index')
        ->name('enrollment.index');



// Existing routes above…



// Existing routes below…



});
