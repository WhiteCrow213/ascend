@extends('layouts.app')

@section('title', 'Student Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student-profile.css') }}">
@endpush

@section('content')
<div class="sp-wrap">

    {{-- Top Bar --}}
    <div class="sp-topbar">
        <div class="sp-topbar-left">
            <div class="sp-page-title">Overview</div>
        </div>

        <div class="sp-topbar-right">
            <div class="sp-meta">School Year | Date | Time</div>
        </div>
    </div>

    {{-- Hero --}}
    <div class="sp-hero">
        <div class="sp-hero-left">
            <div class="sp-photo-card">
                <img class="sp-photo" src="{{ $student['photo_url'] }}" alt="Student Photo">
                <div class="sp-photo-tag">{{ $student['student_no'] }}</div>
            </div>
        </div>

        <div class="sp-hero-mid">
            <div class="sp-name">{{ $student['full_name'] }}</div>

            <div class="sp-subgrid">
                <div class="sp-subleft">
                    <div class="sp-subline">{{ $student['college'] }}</div>
                    <div class="sp-subline">{{ $student['program'] }}</div>
                    <div class="sp-subline">{{ $student['year_level'] }}</div>
                </div>

                <div class="sp-subright">
                    <div class="sp-kv">
                        <div class="sp-k">Active Term:</div>
                        <div class="sp-v">{{ $student['active_term_id'] }}</div>
                    </div>
                    <div class="sp-kv">
                        <div class="sp-k">Account Balance:</div>
                        <div class="sp-v">₱ {{ number_format($student['account_balance'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sp-hero-right">
            <div class="sp-status-card">
                <div class="sp-status-row">
                    <div class="sp-status-dot"></div>
                    <div class="sp-status-title">{{ $student['status_label'] }}</div>
                    <div class="sp-status-pill">{{ $student['status_badge'] }}</div>
                </div>

                <div class="sp-status-grid">
                    <div class="sp-status-block">
                        <div class="sp-status-label">Active Term:</div>
                        <div class="sp-status-big">{{ $student['active_term_id'] }}</div>
                    </div>

                    <div class="sp-status-block">
                        <div class="sp-status-label">{{ $student['active_term'] }}</div>
                        <div class="sp-balance-pill">₱ {{ number_format($student['account_balance'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="sp-tabs">
        <a class="sp-tab {{ $tab==='overview' ? 'active' : '' }}"
           href="{{ route('students.profile', ['studentNo' => $student['student_no'], 'tab' => 'overview']) }}">
            Overview
        </a>

        <a class="sp-tab {{ $tab==='admission' ? 'active' : '' }}"
           href="{{ route('students.profile', ['studentNo' => $student['student_no'], 'tab' => 'admission']) }}">
            Admission History
        </a>

        <a class="sp-tab {{ $tab==='enrollment' ? 'active' : '' }}"
           href="{{ route('students.profile', ['studentNo' => $student['student_no'], 'tab' => 'enrollment']) }}">
            Enrollment History
        </a>

        <a class="sp-tab {{ $tab==='grades' ? 'active' : '' }}"
           href="{{ route('students.profile', ['studentNo' => $student['student_no'], 'tab' => 'grades']) }}">
            Grades
        </a>

        <a class="sp-tab {{ $tab==='billing' ? 'active' : '' }}"
           href="{{ route('students.profile', ['studentNo' => $student['student_no'], 'tab' => 'billing']) }}">
            Billing & Ledger
        </a>
    </div>

    {{-- Body --}}
    <div class="sp-body">
        <div class="sp-leftcol">

            <div class="sp-card">
                <div class="sp-card-title">STUDENT ACTIONS</div>

                <a href="#" class="sp-action">
                    <span class="sp-action-ico">👤</span>
                    <span class="sp-action-text">View Information</span>
                </a>

                <a href="#" class="sp-action">
                    <span class="sp-action-ico">🧾</span>
                    <span class="sp-action-text">View Grades</span>
                </a>

                <a href="#" class="sp-action">
                    <span class="sp-action-ico">📚</span>
                    <span class="sp-action-text">View Load</span>
                </a>

                <a href="#" class="sp-action sp-action-primary">
                    <span class="sp-action-ico">➕</span>
                    <span class="sp-action-text">Enroll Student</span>
                </a>
            </div>

            <div class="sp-card">
                <div class="sp-card-title">QUICK DETAILS</div>

                <div class="sp-qd">
                    <div class="sp-qd-label">Admission Date:</div>
                    <div class="sp-qd-value">{{ $student['admission_date'] }}</div>
                </div>

                <div class="sp-divider"></div>

                <div class="sp-qd">
                    <div class="sp-qd-label">Last Status Update:</div>
                    <div class="sp-qd-value">{{ $student['last_status_update'] }}</div>
                </div>
            </div>

        </div>

        <div class="sp-maincol">
            <div class="sp-panel">
                @if($tab === 'overview')
                    <div class="sp-empty">
                        <div class="sp-empty-ico">📋</div>
                        <div class="sp-empty-title">Welcome to the student overview tab.</div>
                        <div class="sp-empty-sub">Please select an option from the tabs above to get started.</div>
                    </div>
                @elseif($tab === 'admission')
                    <div class="sp-panel-title">Admission History</div>
                    <div class="sp-panel-sub">Hook this to your admissions records grid.</div>
                @elseif($tab === 'enrollment')
                    <div class="sp-panel-title">Enrollment History</div>
                    <div class="sp-panel-sub">Hook this to your enrollments table / drafts / finalized records.</div>
                @elseif($tab === 'grades')
                    <div class="sp-panel-title">Grades</div>
                    <div class="sp-panel-sub">Hook this to grading module once ready.</div>
                @elseif($tab === 'billing')
                    <div class="sp-panel-title">Billing & Ledger</div>
                    <div class="sp-panel-sub">Hook this to ledger/transactions once ready.</div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
