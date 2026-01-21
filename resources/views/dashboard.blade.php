@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dash">

    {{-- PAGE HEADER --}}
    <div class="page-head">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Choose a module to get started.</p>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div>
            <h2 class="hero__title">Welcome back 👋</h2>
            <p class="hero__text">
                This is your control room. Everything important lives a click away.
            </p>
        </div>
        <button class="primary-btn" type="button">
            Create Quick Action
        </button>
    </div>

    {{-- MODULE CARDS --}}
    <div class="cards">

        {{-- ADMISSIONS --}}
        <a class="card" href="{{ route('admission.index') }}">
            <div class="card__title">Admission</div>
            <div class="card__desc">Applicants, requirements, screening</div>
        </a>

        {{-- REGISTRAR --}}
        <a class="card" href="#">
            <div class="card__title">Registrar</div>
            <div class="card__desc">Records, enrollment, sections</div>
        </a>

        {{-- ACCOUNTING --}}
        <a class="card" href="#">
            <div class="card__title">Accounting</div>
            <div class="card__desc">Payments, reports, ledgers</div>
        </a>

        {{-- BILLING --}}
        <a class="card" href="#">
            <div class="card__title">Billing</div>
            <div class="card__desc">Fees, assessment, balances</div>
        </a>

        {{-- DEAN --}}
        <a class="card" href="#">
            <div class="card__title">Dean</div>
            <div class="card__desc">Approvals, oversight, monitoring</div>
        </a>

        {{-- FACULTY --}}
        <a class="card" href="#">
            <div class="card__title">Faculty</div>
            <div class="card__desc">Loads, grading, schedules</div>
        </a>

        {{-- STUDENTS --}}
        <a class="card" href="#">
            <div class="card__title">Students</div>
            <div class="card__desc">Profiles, status, history</div>
        </a>

        {{-- UTILITIES --}}
        <a class="card" href="#">
            <div class="card__title">Utilities</div>
            <div class="card__desc">Import/export, tools, backups</div>
        </a>

        {{-- SETTINGS --}}
        <a class="card" href="#">
            <div class="card__title">Settings</div>
            <div class="card__desc">Users, roles, preferences</div>
        </a>

    </div>

</div>
@endsection
