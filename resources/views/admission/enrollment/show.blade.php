@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/prereg.css') }}">
<style>
  /* Workspace-only tiny helpers (surgical, no global refactor) */
  .enr-grid{display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:12px;}
  @media (max-width: 900px){ .enr-grid{ grid-template-columns: 1fr; } }
  .enr-mn{white-space:pre;}
  .enr-badge{display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; border:1px solid rgba(0,0,0,.08); background:#fff; font-size:12px; white-space:nowrap;}
  .enr-muted{color: rgba(0,0,0,.55);}
  .enr-kv{display:grid; gap:8px;}
  .enr-k{font-size:12px; color: rgba(0,0,0,.55); margin-bottom:2px;}
  .enr-v{font-weight:600;}
</style>
@endpush

@section('content')
<div class="prereg-wrap">

  <div class="prereg-head">
    <div class="prereg-top">
      <h1 class="prereg-title">Enrollment Workspace</h1>
      <p class="prereg-sub">Review the applicant record before proceeding with enrollment.</p>
    </div>

    <div class="prereg-toolbar">

      <div class="prereg-actions">
        <div class="prereg-actions-left">
          <a href="{{ route('admission.enrollment.index') }}" class="btn btn-muted">← Back to Enrollment Candidates</a>
        </div>
      </div>

      {{-- Alerts --}}
      @if(session('enroll_ok'))
        <div class="prereg-card" style="padding:12px 14px; border:1px solid #bbf7d0; background:#f0fdf4; color:#166534; margin-top:10px;">
          {{ session('enroll_ok') }}
        </div>
      @endif

      @if(session('enroll_err'))
        <div class="prereg-card" style="padding:12px 14px; border:1px solid #fecaca; background:#fef2f2; color:#991b1b; margin-top:10px;">
          {{ session('enroll_err') }}
        </div>
      @endif

    </div>
  </div>

  {{-- Applicant Header Card --}}
  <div class="prereg-card" style="padding:16px;">
    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap;">
      <div style="min-width:260px;">
        <div style="font-weight:800; font-size:18px; letter-spacing:.2px;">
          {{ $enrollment->LastName }}, {{ $enrollment->FirstName }} <span class="enr-mn">{{ $enrollment->MiddleName ?? '' }}</span>
        </div>

        <div class="enr-muted" style="margin-top:6px; font-size:13px;">
          Applicant No: <strong>{{ $enrollment->ApplicantNum ?? '—' }}</strong>
          <span style="opacity:.6;"> • </span>
          StudID: <strong>{{ $enrollment->studID }}</strong>
          <span style="opacity:.6;"> • </span>
          Enrollment ID: <strong>{{ $enrollment->enrollment_id }}</strong>
        </div>
      </div>

      <div style="display:flex; align-items:flex-start; gap:8px; flex-wrap:wrap;">
        <span class="enr-badge">Status: <strong>{{ $enrollment->status }}</strong></span>
        <span class="enr-badge">Term ID: <strong>{{ $enrollment->term_id }}</strong></span>
        <span class="enr-badge">Finalized: <strong>{{ $enrollment->finalized_at ? $enrollment->finalized_at : 'No' }}</strong></span>
      </div>
    </div>
  </div>

  {{-- 3-column Workspace Panels --}}
  <div class="enr-grid" style="margin-top:12px;">
    <div class="prereg-card" style="padding:14px;">
      <div style="font-weight:800; margin-bottom:10px;">Admission Snapshot</div>
      <div class="enr-kv">
        <div>
          <div class="enr-k">Application Status</div>
          <div class="enr-v">{{ $enrollment->application_status ?? '—' }}</div>
        </div>
        <div>
          <div class="enr-k">First Program Choice</div>
          <div class="enr-v">{{ $enrollment->FirstProgramChoice ?? '—' }}</div>
        </div>
      </div>
    </div>

    <div class="prereg-card" style="padding:14px;">
      <div style="font-weight:800; margin-bottom:10px;">Academic Context</div>
      <div class="enr-muted" style="font-size:13px; line-height:1.5;">
        <div style="font-size:12px; opacity:.75; margin-bottom:6px;">(Phase 1)</div>
        This section will show academic history / transferee info once those records exist.
      </div>
    </div>

    <div class="prereg-card" style="padding:14px;">
      <div style="font-weight:800; margin-bottom:10px;">Assessment</div>
      <div class="enr-muted" style="font-size:13px; line-height:1.5;">
        <div style="font-size:12px; opacity:.75; margin-bottom:6px;">(Phase 1)</div>
        This section will show admission/assessment checks once assessment tables are wired.
      </div>
    </div>
  </div>

  {{-- Next step --}}
  <div class="prereg-card" style="padding:14px; margin-top:12px;">
    <div style="display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; align-items:center;">
      <div>
        <div style="font-weight:800;">Next</div>
        <div class="enr-muted" style="font-size:13px; margin-top:4px;">
          When you’re ready, we’ll add the actual enrollment encoding here (year level, section, subjects), then Finalize.
        </div>
      </div>

      <button type="button" class="btn btn-muted" disabled style="opacity:.55; cursor:not-allowed;">
        Finalize (next step)
      </button>
    </div>
  </div>

</div>
@endsection
