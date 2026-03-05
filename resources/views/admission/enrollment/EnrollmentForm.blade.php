{{-- resources/views/admission/enrollment/EnrollmentForm.blade.php --}}
@extends('layouts.app')

@section('title', 'Enrollment | Subject Loading')

@push('styles')
<style>
  .enf-page{ padding:18px 22px 26px; font-weight:200; }
  .enf-hero{
    border-radius: 18px;
    padding: 16px 18px;
    background: linear-gradient(135deg, #3a2a72 0%, #6b5bb7 100%);
    border: 1px solid rgba(120, 100, 210, .35);
    box-shadow: 0 14px 34px rgba(18, 24, 60, .14);
    margin-bottom: 14px;
    color: rgba(255,255,255,.95);
  }
  .enf-title{ font-size:18px; font-weight:200; margin:0; }
  .enf-sub{ margin-top:4px; font-size:13px; opacity:.85; }
  .enf-panel{
    border-radius: 16px;
    background:#fff;
    border: 1px solid rgba(20,35,75,.08);
    box-shadow: 0 10px 22px rgba(18,24,60,.06);
    padding: 14px;
  }
  .enf-pill{
    display:inline-flex; align-items:center; gap:8px;
    padding: 6px 10px; border-radius: 999px; font-size:12px;
    background: rgba(20,35,75,.04);
    border: 1px solid rgba(20,35,75,.10);
    color: rgba(28,36,80,.85);
    white-space:nowrap;
  }
  .enf-table th{ white-space:nowrap; font-size:12.5px; font-weight:600; color:#1c2450; }
  .enf-table td{ font-size:13px; color: rgba(28,36,80,.95); vertical-align:middle; }
  .enf-muted{ font-size:12px; color: rgba(28,36,80,.70); }
  .enf-actions{ display:flex; gap:10px; align-items:center; justify-content:flex-end; margin-top:12px; }
  .enf-btn{
    padding:10px 14px; border-radius:12px; font-weight:600; font-size:13px;
    border:1px solid rgba(91,76,230,.25);
    background:#fff; color:#4b3fd1; cursor:pointer;
    height:38px; display:inline-flex; align-items:center; justify-content:center; line-height:1; white-space:nowrap;
    text-decoration:none;
  }
  .enf-btn-primary{
    border:none;
    background: linear-gradient(135deg,#5B4CE6 0%, #7A5FFF 100%);
    color:#fff;
    box-shadow: 0 8px 18px rgba(91,76,230,.28);
  }
  .enf-btn[aria-disabled="true"]{ opacity:.55; pointer-events:none; }

  .enf-table thead th{ border-bottom: 1px solid rgba(28,36,80,.10); }
  .enf-table tbody tr{ border-bottom: 1px solid rgba(28,36,80,.06); }
  .enf-group-row td{
    background: rgba(107,91,183,.10);
    color:#1c2450;
    font-weight:700;
    letter-spacing:.3px;
    text-transform:uppercase;
    border-bottom: none;
  }
  .enf-sem-row td{
    background: rgba(107,91,183,.06);
    color:#1c2450;
    font-weight:600;
    border-top: none;
  }
  .enf-code{ font-weight:600; color:#1c2450; white-space:nowrap; }
  .enf-desc{ max-width:520px; }
  .enf-remove{
    pointer-events:auto;

    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    border: 1px solid rgba(220, 53, 69, .35);
    background: rgba(220, 53, 69, .06);
    color: #dc3545;
    cursor: pointer;
  }
  .enf-remove:disabled{ cursor:not-allowed; opacity:.6; }


  .enf-table{ table-layout: fixed; width:100%; }
  .enf-table th, .enf-table td{ padding: 10px 12px; }
  .enf-col-code{ width: 92px; }
  .enf-col-units{ width: 56px; text-align:center; }
  .enf-col-section{ width: 92px; text-align:center; }
  .enf-col-day{ width: 72px; text-align:center; }
  .enf-col-time{ width: 92px; text-align:center; }
  .enf-col-instructor{ width: 170px; }
  .enf-col-actions{ width: 96px; text-align:right; }
  .enf-desc{ max-width:none; }


  /* =========================
     Two-grid layout (ASCEND)
     ========================= */
  .enf-grids{
    display:flex;
    flex-direction:column;
    gap: 14px;
    margin-top: 12px;
  }
  @media (max-width: 1180px){
    .enf-grids{ }
  }
  .enf-grid-card{
    border-radius: 18px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(120, 100, 210, .18);
    box-shadow: 0 12px 28px rgba(18, 24, 60, .08);
    overflow: hidden;
  }
  .enf-grid-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    padding: 14px 14px 12px;
    background:
      radial-gradient(1200px 260px at 10% -30%, rgba(107,91,183,.20), rgba(107,91,183,0) 60%),
      linear-gradient(180deg, rgba(58,42,114,.06), rgba(58,42,114,0));
    border-bottom: 1px solid rgba(120, 100, 210, .12);
  }
  .enf-grid-title{
    font-size: 14px;
    font-weight: 700;
    color: #1c2450;
    letter-spacing: .2px;
  }
  .enf-grid-sub{
    margin-top: 2px;
    font-size: 12px;
    color: rgba(28,36,80,.72);
  }
  .enf-grid-tools{
    display:flex;
    align-items:flex-end;
    justify-content:flex-end;
    gap: 10px;
    flex-wrap: wrap;
  }
  .enf-grid-field{ min-width: 150px; }
  .enf-grid-label{
    font-size: 11px;
    color: rgba(28,36,80,.78);
    margin-bottom: 6px;
    white-space: nowrap;
  }
  .enf-grid-control{
    width: 100%;
    height: 38px;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid rgba(91,76,230,.18);
    background: rgba(255,255,255,.9);
    color: rgba(28,36,80,.92);
    outline: none;
  }
  .enf-grid-control:disabled{
    opacity: .72;
    cursor: not-allowed;
    background: rgba(255,255,255,.75);
  }
  .enf-grid-body{
    max-height: clamp(220px, 32vh, 420px);
    overflow:auto;
  }
  .enf-table-compact th{ font-size: 12px; }
  .enf-table-compact td{ font-size: 12.75px; }
  .enf-col-check{ width:44px; text-align:center; }
  .enf-col-check input{ width:16px; height:16px; }

</style>
@endpush

@section('content')
<div class="enf-page">

  <div class="enf-hero">
    <h2 class="enf-title">Subject Loading</h2>
    <div class="enf-sub">
      {{ $enrollment->LastName }}, {{ $enrollment->FirstName }} {{ $enrollment->MiddleName ?? '' }}
      <span style="opacity:.6">•</span>
      {{ $enrollment->stud_number ?? $enrollment->ApplicantNum }}
    </div>

    <div style="margin-top:12px;">
      <form method="POST" action="{{ route('admission.enrollment.applyAcademic', ['enrollmentId' => $enrollment->enrollment_id]) }}"
            style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
        @csrf

        <div style="min-width:280px; flex:1;">
          <div style="font-size:12px; opacity:.85; margin-bottom:6px;">Program</div>
          <select name="IDprogram" required
                  style="width:100%; height:38px; border-radius:12px; border:1px solid rgba(20,35,75,.18);
                         padding:0 12px; background:#fff; color:#1c2450; outline:none;">
            <option value="">Select program</option>
            @foreach($programOptions as $p)
              <option value="{{ $p->id }}" {{ (string)$selectedProgram === (string)$p->id ? 'selected' : '' }}>
                {{ $p->label }}
              </option>
            @endforeach
          </select>
        </div>

        <div style="min-width:180px;">
          <div style="font-size:12px; opacity:.85; margin-bottom:6px;">Year Level</div>
          <select name="IDyearlvl" required
                  style="width:100%; height:38px; border-radius:12px; border:1px solid rgba(20,35,75,.18);
                         padding:0 12px; background:#fff; color:#1c2450; outline:none;">
            <option value="">Select year level</option>
            @foreach($yearLevelOptions as $y)
              <option value="{{ $y->id }}" {{ (string)$selectedYearLevel === (string)$y->id ? 'selected' : '' }}>
                {{ $y->label }}
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit"
                class="enf-btn enf-btn-primary"
                style="height:38px; border-radius:12px; padding:10px 14px;">
          Apply
        </button>
      </form>
    </div>
  </div>

    @if(!$canQueryOfferings)
      <div class="alert alert-warning" style="border-radius:12px;">
        Cannot load offered subjects yet. Missing student curriculum/year level linkage (IDcurr / IDyearlvl).
      </div>
    @endif

    <div class="enf-grids">

      <!-- Grid 1: Section Offerings (Selection Grid) -->
      <div class="enf-grid-card">
        <div class="enf-grid-head">
          <div>
            <div class="enf-grid-title">Section Offerings</div>
            <div class="enf-grid-sub">Active term offerings (filter by year level/section). Select subjects to load.</div>
          </div>

          <div class="enf-grid-tools">
            <div class="enf-grid-field">
              <div class="enf-grid-label">Year Level</div>
              <select class="enf-grid-control" disabled>
                <option selected>—</option>
              </select>
            </div>

            <div class="enf-grid-field">
              <div class="enf-grid-label">Section</div>
              <select class="enf-grid-control" disabled>
                <option selected>—</option>
              </select>
            </div>

            <button type="button" class="enf-btn enf-btn-primary" disabled>
              Load Selected
            </button>
          </div>
        </div>

        <div class="enf-grid-body">
          <table class="table enf-table enf-table-compact">
            <thead>
              <tr>
                <th class="enf-col-check">
                  <input type="checkbox" disabled aria-label="Select all" />
                </th>
                <th class="enf-col-code">Course Code</th>
                <th>Description</th>
                <th class="enf-col-units">Units</th>
                <th class="enf-col-section">Section</th>
                <th class="enf-col-day">Day</th>
                <th class="enf-col-time">Time</th>
                <th class="enf-col-instructor">Instructor</th>
              </tr>
            </thead>
            <tbody>
              @forelse($offeredSubjects as $s)
                <tr>
                  <td class="enf-col-check">
                    <input type="checkbox" disabled aria-label="Select subject {{ $s->CourseCode }}" />
                  </td>
                  <td class="enf-code">{{ $s->CourseCode }}</td>
                  <td class="enf-desc">{{ $s->CourseDescription }}</td>
                  <td class="enf-col-units">{{ $s->Units }}</td>
                  <td class="enf-muted enf-col-section">—</td>
                  <td class="enf-muted enf-col-day">—</td>
                  <td class="enf-muted enf-col-time">—</td>
                  <td class="enf-muted enf-col-instructor">—</td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="enf-muted">
                    No section offerings to show yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid 2: Student Load (Final Load Grid) -->
      <div class="enf-grid-card">
        <div class="enf-grid-head">
          <div>
            <div class="enf-grid-title">Student Load</div>
            <div class="enf-grid-sub">Final load (saved rows) for this enrollment.</div>
          </div>

          <div class="enf-grid-tools">
            <button type="button" class="enf-btn" disabled>
              Remove Selected
            </button>
          </div>
        </div>

        <div class="enf-grid-body">
          <table class="table enf-table enf-table-compact">
            <thead>
              <tr>
                <th class="enf-col-code">Course Code</th>
                <th>Description</th>
                <th class="enf-col-units">Units</th>
                <th class="enf-col-section">Section</th>
                <th class="enf-col-day">Day</th>
                <th class="enf-col-time">Time</th>
                <th class="enf-col-instructor">Instructor</th>
                <th class="enf-col-actions"></th>
              </tr>
            </thead>
            <tbody>
              @forelse(($studentLoadSubjects ?? []) as $l)
                <tr>
                  <td class="enf-code">{{ $l->CourseCode ?? '—' }}</td>
                  <td class="enf-desc">{{ $l->CourseDescription ?? '—' }}</td>
                  <td class="enf-col-units">{{ $l->Units ?? '—' }}</td>
                  <td class="enf-muted enf-col-section">—</td>
                  <td class="enf-muted enf-col-day">—</td>
                  <td class="enf-muted enf-col-time">—</td>
                  <td class="enf-muted enf-col-instructor">—</td>
                  <td class="enf-col-actions">
                    <button type="button" class="enf-remove" disabled>Remove</button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="enf-muted">
                    No student load yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
    <div class="enf-actions">
      <a class="enf-btn" href="{{ route('admission.enrollment.show', ['enrollmentId' => $enrollment->enrollment_id]) }}">Back to Workspace</a>
      <button class="enf-btn enf-btn-primary" aria-disabled="true" type="button">Save Draft (Next)</button>
    </div>
  </div>

</div>
@endsection


@include('admission.enrollment.EnrollmentModal')
