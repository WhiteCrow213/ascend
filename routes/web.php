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
