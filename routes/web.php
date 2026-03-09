<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admissions\PreRegistrationController;
use App\Http\Controllers\Admissions\PreRegistrationStatusController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\Admissions\EnrollmentController;
use App\Http\Controllers\Utilities\TermController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Students\StudentProfileController;
use App\Http\Controllers\Dean\SectionsOfferingsController;
use App\Http\Controllers\Faculty\FacultyController;


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

Route::get('/enrollment/workspace/{enrollmentId}/form', [EnrollmentController::class, 'showForm'])
    ->name('admission.enrollment.form');

Route::post('/enrollment/workspace/{enrollmentId}/apply-academic', [EnrollmentController::class, 'applyAcademic'])
    ->name('admission.enrollment.applyAcademic');





// Add Subject modal (offerings search + add)
Route::get('/enrollment/workspace/{enrollmentId}/offerings/search', [EnrollmentController::class, 'offeringsSearch'])
    ->name('admission.enrollment.offerings.search');
Route::post('/enrollment/workspace/{enrollmentId}/offerings/add', [EnrollmentController::class, 'offeringsAdd'])
    ->name('admission.enrollment.offerings.add');

// Remove subject from saved enrollment load
Route::post('/enrollment/workspace/{enrollmentId}/subjects/{enrollSubjId}/remove', [EnrollmentController::class, 'subjectRemove'])
    ->name('admission.enrollment.subjects.remove');


Route::middleware(['web'])->group(function () {
    Route::get('/students/{studentNo}', [StudentProfileController::class, 'show'])
        ->name('students.profile');
});

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

    // Utilities Hub
Route::get('/utilities', function () {
    return view('utilities.index');
})->name('utilities.index');

Route::prefix('utilities')->group(function () {
    Route::get('/terms', [TermController::class, 'index'])->name('utilities.terms.index');
    Route::post('/terms', [TermController::class, 'store'])->name('utilities.terms.store');
    Route::patch('/terms/{termId}', [TermController::class, 'update'])->name('utilities.terms.update');
    Route::post('/terms/{termId}/active', [TermController::class, 'setActive'])->name('utilities.terms.active');

    Route::get('/master-data', function () {
    return view('utilities.master-data');
})->name('utilities.master-data');

// Programs
Route::prefix('programs')->name('utilities.programs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Utilities\ProgramsController::class, 'index'])->name('index');
    Route::post('/store', [App\Http\Controllers\Utilities\ProgramsController::class, 'store'])->name('store');
    Route::post('/update/{id}', [App\Http\Controllers\Utilities\ProgramsController::class, 'update'])->name('update');
});






// Curriculum
Route::prefix('curriculum')->name('utilities.curriculum.')->group(function () {
    Route::get('/', [App\Http\Controllers\Utilities\CurriculumController::class, 'index'])->name('index');
    Route::post('/store', [App\Http\Controllers\Utilities\CurriculumController::class, 'store'])->name('store');
    Route::post('/update/{id}', [App\Http\Controllers\Utilities\CurriculumController::class, 'update'])->name('update');
});


// Curriculum Map (assign subjects to curriculum)
Route::get('/curriculum-map', [App\Http\Controllers\Utilities\CurriculumController::class, 'mapIndex'])->name('utilities.curriculum.map.index');
Route::post('/curriculum-map/store', [App\Http\Controllers\Utilities\CurriculumController::class, 'mapStore'])->name('utilities.curriculum.map.store');
Route::post('/curriculum-map/{CurrMapID}/delete', [App\Http\Controllers\Utilities\CurriculumController::class, 'mapDelete'])->name('utilities.curriculum.map.delete');

// Subjects
Route::prefix('subjects')->name('utilities.subjects.')->group(function () {
    Route::get('/', [App\Http\Controllers\Utilities\SubjectsController::class, 'index'])->name('index');
    Route::post('/store', [App\Http\Controllers\Utilities\SubjectsController::class, 'store'])->name('store');
    Route::post('/update/{id}', [App\Http\Controllers\Utilities\SubjectsController::class, 'update'])->name('update');
});
});

// ===============================
// DEAN MODULE
// ===============================

Route::get('/dean', function () {
    return view('dean.DeanIndex');
})->name('dean.index');

// Sections & Offerings (Dean schedule builder)
Route::get('/dean/sections-offerings', [SectionsOfferingsController::class, 'index'])
    ->name('dean.sections-offerings');

Route::post('/dean/sections-offerings/load-subjects', [SectionsOfferingsController::class, 'loadSubjects'])
    ->name('dean.sections-offerings.load-subjects');

Route::post('/dean/sections-offerings/save', [SectionsOfferingsController::class, 'save'])
    ->name('dean.sections-offerings.save');

Route::get('/dean/instructor-search', [SectionsOfferingsController::class, 'searchInstructor'])
    ->name('dean.instructor.search');

// ===============================
// FACULTY MODULE
// ===============================
Route::prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/', [FacultyController::class, 'index'])->name('index');
    Route::get('/create', [FacultyController::class, 'create'])->name('create');
    Route::post('/store', [FacultyController::class, 'store'])->name('store');
    Route::get('/{id}', [FacultyController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [FacultyController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [FacultyController::class, 'update'])->name('update');
    Route::delete('/{id}', [FacultyController::class, 'destroy'])->name('destroy');
});
