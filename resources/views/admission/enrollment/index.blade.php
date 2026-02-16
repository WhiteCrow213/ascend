@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/prereg.css') }}">
@endpush

@section('content')
<div class="prereg-wrap">

  <div class="prereg-head">
    <div class="prereg-top">
      <h1 class="prereg-title">Enrollment</h1>
      <p class="prereg-sub">Select approved students to start enrollment (draft only, not counted).</p>
    </div>

    {{-- TOOLBAR --}}
    <div class="prereg-toolbar">

      <div class="prereg-actions">
        <div class="prereg-actions-left">
          <a href="{{ route('admission.index') }}" class="btn btn-muted">← Back</a>
        </div>

        <div class="prereg-actions-right">
          <a href="{{ route('admission.enrollment.index') }}" class="btn">Reset</a>
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

      {{-- SEARCH --}}
      <form method="GET"
            action="{{ route('admission.enrollment.index') }}"
            class="prereg-filters">

        <input class="prereg-input"
               type="text"
               name="q"
               value="{{ $search ?? '' }}"
               placeholder="Search applicant no., name, program..."
               autocomplete="off" />

        {{-- no status dropdown here by design (approved is hidden filter) --}}
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <table class="table">
      <thead>
        <tr>
          <th>Applicant No</th>
          <th>Full Name</th>
          <th>Program</th>
          <th>Date</th>
          <th class="actions">Action</th>
        </tr>
      </thead>

      <tbody>
        @forelse($students as $s)
          @php
            $dt = null;
            if (!empty($s->created_at)) {
              try { $dt = \Illuminate\Support\Carbon::parse($s->created_at); } catch (\Throwable $e) { $dt = null; }
            }
          @endphp

          <tr>
            <td><strong>{{ $s->ApplicantNum }}</strong></td>

            <td>
              {{ $s->LastName }},
              {{ $s->FirstName }}
              {{ $s->MidName ?? '' }}
            </td>

            <td>{{ $s->FirstProgramChoice ?? '—' }}</td>

            <td>{{ $dt ? $dt->format('M d, Y') : '—' }}</td>

            <td class="actions">
              <form method="POST" action="{{ route('admission.enrollment.start', $s->studID) }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                  Start Enrollment
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="empty">No students ready for enrollment.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div style="padding: 12px 14px; border-top: 1px solid #eef2f7;">
      @if ($students->hasPages())
        <div class="pager">
          @if ($students->onFirstPage())
            <span class="btn btn-muted is-disabled">Previous</span>
          @else
            <a class="btn btn-muted" href="{{ $students->previousPageUrl() }}" rel="prev">Previous</a>
          @endif

          <span class="pager-meta">
            Page <strong>{{ $students->currentPage() }}</strong> of <strong>{{ $students->lastPage() }}</strong>
          </span>

          @if ($students->hasMorePages())
            <a class="btn btn-primary" href="{{ $students->nextPageUrl() }}" rel="next">Next</a>
          @else
            <span class="btn btn-primary is-disabled">Next</span>
          @endif
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
