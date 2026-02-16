<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admissions\PreRegistrationController;
use App\Http\Controllers\Admissions\PreRegistrationStatusController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\Admissions\EnrollmentController;
use App\Http\Controllers\Utilities\TermController;
use App\Http\Controllers\AuthController;



/*
|--------------------------------------------------------------------------
| Web Routes (ASCEND)
|--------------------------------------------------------------------------
| This restores the named routes your layout expects (dashboard, admission.index,
| admission.prereg.grid) and keeps the Pre-Registration workflow working.
|--------------------------------------------------------------------------
*/

// ===============================
// BASIC PAGES (restores broken links)
// ===============================

// Login page (if you already have resources/views/login.blade.php)
Route::get('/login', function () {
    if (session()->has('ascend_user_id')) {
        return redirect()->route('dashboard');
    }
    return view('login');
})->name('login');

// Handle login (POST)
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Handle logout (POST)
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');


// Dashboard (your layout/sidebar calls route('dashboard'))
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    if (!session()->has('ascend_user_id')) {
        session(['url.intended' => $request->fullUrl()]);
        return redirect()->route('login');
    }
    return view('dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| GEO (AJAX) ROUTES
|--------------------------------------------------------------------------
| Used by Manual Pre-Registration address dropdowns.
*/
Route::get('/geo/provinces/{region}', [GeoController::class, 'provinces'])->name('geo.provinces');
Route::get('/geo/cities/{province}', [GeoController::class, 'cities'])->name('geo.cities');
Route::get('/geo/barangays/{city}', [GeoController::class, 'barangays'])->name('geo.barangays');


// Optional landing: send to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ===============================
// ADMISSIONS MODULE
// ===============================
Route::prefix('admission')->group(function () {

// Admissions home (some menus use route('admission.index'))
Route::get('/', function () {
    return view('admission.index'); // loads resources/views/admission/index.blade.php
})->name('admission.index');

// Enrollment index
Route::get('/enrollment', function () {
    return view('admission.enrollment.index');
})->name('admission.enrollment.index');

// Enrollment candidates grid
Route::get('/enrollment', [EnrollmentController::class, 'index'])
    ->name('admission.enrollment.index');

// Start enrollment (creates draft)
Route::post('/enrollment/{studID}/start', [EnrollmentController::class, 'start'])
    ->name('admission.enrollment.start');

Route::get('/enrollment/workspace/{enrollmentId}', [EnrollmentController::class, 'show'])
    ->name('admission.enrollment.show');





    /*
    |--------------------------------------------------------------------------
    | PRE-REGISTRATION (INBOX + VIEWER + PDF + STATUS)
    |--------------------------------------------------------------------------
    */

    // Inbox grid (your UI expects route('admission.prereg.grid'))
    Route::get('/pre-registration', [PreRegistrationController::class, 'index'])
        ->name('admission.prereg.grid');

    // Backward-compatible alias if any older code uses admission.prereg.index
    Route::get('/pre-registration/index', function () {
        return redirect()->route('admission.prereg.grid');
    })->name('admission.prereg.index');

    // Manual (Walk-in) multi-step (create shows Step 1, store handles saving)
    Route::get('/pre-registration/manual', [PreRegistrationController::class, 'create'])
        ->name('admission.prereg.manual');

    Route::post('/pre-registration/manual', [PreRegistrationController::class, 'store'])
        ->name('admission.prereg.manual.store');

        // Success page after manual prereg save
Route::get('/pre-registration/success/{studID}', [PreRegistrationController::class, 'success'])
    ->name('admission.prereg.success');


    // Viewer (iframe modal "View" button)
    Route::get('/prereg/{studID}/viewer', [PreRegistrationController::class, 'viewer'])
        ->name('admission.prereg.viewer');

    // Download PDF (modal "Download PDF" button)
    Route::get('/prereg/{studID}/pdf', [PreRegistrationController::class, 'pdf'])
        ->name('admission.prereg.pdf');

    // Approve / Reject (updates application_status)
    Route::put('/prereg/{studID}/status', [PreRegistrationStatusController::class, 'updateStatus'])
        ->name('admission.prereg.status');

});


// ===============================
// UTILITIES MODULE
// ===============================
Route::prefix('utilities')->group(function () {
    Route::get('/terms', [TermController::class, 'index'])->name('utilities.terms.index');
    Route::post('/terms', [TermController::class, 'store'])->name('utilities.terms.store');
    Route::patch('/terms/{termId}', [TermController::class, 'update'])->name('utilities.terms.update');
    Route::post('/terms/{termId}/active', [TermController::class, 'setActive'])->name('utilities.terms.active');
});

