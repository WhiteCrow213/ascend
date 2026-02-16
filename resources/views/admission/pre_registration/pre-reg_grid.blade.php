@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/prereg.css') }}">
@endpush

@section('content')
<div class="prereg-wrap">

  @php
  // Defensive fallback: some pages may include this view without providing $applicants.
  // Prefer the paginator from controller, but allow alternate variable names.
  $applicants = $applicants ?? ($preregs ?? ($students ?? null));
@endphp


  <div class="prereg-head">
    <div class="prereg-top">
      <h1 class="prereg-title">Pre-Registration Inbox</h1>
      <p class="prereg-sub">Review and manage applicant pre-registrations</p>
    </div>

    {{-- TOOLBAR --}}
    <div class="prereg-toolbar">

      {{-- TOP ROW: ACTIONS --}}
      <div class="prereg-actions">
        <div class="prereg-actions-left">
          <a href="{{ url('/admission') }}" class="btn btn-muted">← Back</a>


          <a id="walkinBtn" href="{{ route('admission.prereg.manual') }}" class="btn btn-outline">
            + Walk-in Pre-Registration
          </a>
        </div>

        <div class="prereg-actions-right">
          <a id="preregResetBtn" href="{{ route('admission.prereg.grid') }}" class="btn">
            Reset
          </a>
        </div>
      </div>

      {{-- BOTTOM ROW: FILTERS --}}
      <form id="preregSearch"
            method="GET"
            action="{{ route('admission.prereg.grid') }}"
            class="prereg-filters">

        <input id="preregSearchInput"
               class="prereg-input"
               type="text"
               name="q"
               value="{{ $search ?? '' }}"
               placeholder="Search applicant no., name, program..."
               autocomplete="off" />

        <select id="preregStatusSelect" class="prereg-select" name="status">
          <option value="all" @selected(($status ?? 'all') === 'all')>All</option>
          <option value="pending" @selected(($status ?? 'all') === 'pending')>Pending</option>
          <option value="approved" @selected(($status ?? 'all') === 'approved')>Approved</option>
          <option value="rejected" @selected(($status ?? 'all') === 'rejected')>Rejected</option>
        </select>

      </form>
    </div>
  </div>

  {{-- TABLE --}}
  @if($applicants)
  <div class="table-card">
    <table class="table">
      <thead>
        <tr>
          <th>Applicant No</th>
          <th>Full Name</th>
          <th>Program</th>
          <th>Date</th>
          <th>Status</th>
          <th class="actions">Actions</th>
        </tr>
      </thead>

      <tbody>
        @forelse($applicants as $a)
          <tr>
            <td><strong>{{ $a->ApplicantNum }}</strong></td>

            <td>
              {{ $a->LastName }},
              {{ $a->FirstName }}
              {{ $a->MidName ?? '' }}
            </td>

            <td>{{ $a->FirstProgramChoice }}</td>

            {{-- FIX: created_at may be string, so parse safely --}}
            <td>
              @php
                $dt = null;
                if (!empty($a->created_at)) {
                  try { $dt = \Illuminate\Support\Carbon::parse($a->created_at); } catch (\Throwable $e) { $dt = null; }
                }
              @endphp
              {{ $dt ? $dt->format('M d, Y') : '—' }}
            </td>

            <td>
              @php $st = strtolower($a->application_status ?? 'pending'); @endphp
              <span class="badge badge-{{ $st }}">{{ ucfirst($st) }}</span>
            </td>

            <td class="actions">
              <button
                class="btn"
                type="button"
                data-action="view"
                data-id="{{ $a->studID }}"
                data-applicant="{{ $a->ApplicantNum }}"
                data-last="{{ $a->LastName }}"
                data-first="{{ $a->FirstName }}"
                data-mid="{{ $a->MidName ?? '' }}"
                data-program="{{ $a->FirstProgramChoice ?? '' }}"
                data-created="{{ $dt ? $dt->format('M d, Y') : '—' }}"
                data-status="{{ $a->application_status ?? 'pending' }}"
                data-contact="{{ $a->ContactNo ?? '' }}"
                data-email="{{ $a->email ?? '' }}"
                data-birthdate="{{ $a->Birthdate ?? '' }}"
                data-pob="{{ $a->place_of_birth ?? '' }}"
              >View</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="empty">No applicants found.</td>
          </tr>
        @endforelse
      </tbody>

    </table>

    <div style="padding: 12px 14px; border-top: 1px solid #eef2f7;">
      @if ($applicants->hasPages())
        <div class="pager">
          @if ($applicants->onFirstPage())
            <span class="btn btn-muted is-disabled">Previous</span>
          @else
            <a class="btn btn-muted" href="{{ $applicants->previousPageUrl() }}" rel="prev">Previous</a>
          @endif

          <span class="pager-meta">
            Page <strong>{{ $applicants->currentPage() }}</strong> of <strong>{{ $applicants->lastPage() }}</strong>
          </span>

          @if ($applicants->hasMorePages())
            <a class="btn btn-primary" href="{{ $applicants->nextPageUrl() }}" rel="next">Next</a>
          @else
            <span class="btn btn-primary is-disabled">Next</span>
          @endif
        </div>
      @endif
    </div>
  </div>

</div>
  @else
    <div class="prereg-card" style="padding:16px;">
      <div style="font-weight:800;">No data provided to this view.</div>
      <div style="color:#6b7280;font-size:13px;margin-top:6px;">
        This page expects <code>$applicants</code> (a paginator) from the controller. If you’re seeing this inside another page,
        open the Pre-Registration Inbox route instead.
      </div>
    </div>
  @endif



{{-- VIEWER MODAL --}}
<div id="preregModal" class="modal" aria-hidden="true">
  <div class="modal-backdrop" data-close="1"></div>

  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="preregModalTitle">
    <div class="modal-head">
      <div>
        <h2 id="preregModalTitle" class="modal-title">Applicant Viewer</h2>
        <p class="modal-sub">Review details and approve or reject.</p>
      </div>
      <button type="button" class="modal-close" data-close="1" aria-label="Close">✕</button>
    </div>

    <div class="modal-body">
      <iframe id="viewerFrame" src="about:blank" style="width:100%;height:70vh;border:0;border-radius:12px;"></iframe>
      <input type="hidden" id="mStudID" value="">
    </div>

    <div class="modal-foot">
      
            <a id="pdfLink" class="btn btn-muted" href="#" target="_blank" rel="noopener">Download PDF</a>
<form id="approveForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="application_status" value="approved">
        <button type="submit" class="btn btn-primary">Approve</button>
      </form>

      <form id="rejectForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="application_status" value="rejected">
        <button type="submit" class="btn" style="border-color:#fca5a5;color:#b91c1c;">Reject</button>
      </form>

      <button type="button" class="btn btn-muted" data-close="1">Close</button>
    </div>
  </div>
</div>

<style>
.modal{ position:fixed; inset:0; display:none; z-index:9999; }
.modal.is-open{ display:block; }
.modal-backdrop{ position:absolute; inset:0; background: rgba(17,24,39,.55); }
.modal-card{
  position:relative;
  width: min(920px, calc(100% - 32px));
  margin: 40px auto;
  background:#fff;
  border-radius: 14px;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  overflow:hidden;
}
.modal-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:16px 16px 10px; border-bottom:1px solid #eef2f7; }
.modal-title{ margin:0; font-size:16px; font-weight:800; }
.modal-sub{ margin:3px 0 0; color:#6b7280; font-size:12px; }
.modal-close{ border:1px solid #e5e7eb; background:#fff; width:34px; height:34px; border-radius:10px; cursor:pointer; }
.modal-body{ padding:16px; }
.modal-grid{ display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px 16px; }
@media (max-width: 860px){ .modal-grid{ grid-template-columns: repeat(2, minmax(0,1fr)); } }
@media (max-width: 560px){ .modal-grid{ grid-template-columns: 1fr; } }
.kv .k{ color:#6b7280; font-size:11px; font-weight:700; }
.kv .v{ font-size:13px; font-weight:700; color:#111827; margin-top:2px; word-break: break-word; }
.modal-hr{ border:0; border-top:1px solid #eef2f7; margin:14px 0; }
.modal-foot{ display:flex; justify-content:flex-end; gap:10px; padding:12px 16px; border-top:1px solid #eef2f7; background:#fafbff; flex-wrap:wrap; }

/* ===== Pager (replaces default SVG pagination) ===== */
.pager{ display:flex; align-items:center; justify-content:flex-end; gap:10px; padding:10px 0; flex-wrap:wrap; }
.pager-meta{ color:#6b7280; font-size:12px; font-weight:700; padding:0 6px; }
.is-disabled{ opacity:.45; pointer-events:none; }

/* ===== Status badge colors ===== */
.badge{ display:inline-flex; align-items:center; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:800; }
.badge-pending{ background:#fde68a; color:#92400e; }
.badge-approved{ background:#bbf7d0; color:#166534; }
.badge-rejected{ background:#fecaca; color:#991b1b; }

</style>

<script>
(function () {
  // ======================
  // Search UX (safe/minimal)
  // ======================
  const form  = document.getElementById('preregSearch');
  const input = document.getElementById('preregSearchInput');

  if (input && form) {
    let t = null;

    // Debounce submit while typing (optional)
    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => form.submit(), 450);
    });

    // Enter = submit immediately
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(t);
        form.submit();
      }
      if (e.key === 'Escape') {
        input.value = '';
        clearTimeout(t);
        form.submit();
      }
    });

    // Hotkey: "/" focus search
    document.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement !== input) {
        e.preventDefault();
        input.focus();
      }
    });
  }

  // ======================
  // Viewer Modal (iframe)
  // ======================
  const modal = document.getElementById('preregModal');
  const frame = document.getElementById('viewerFrame');
  const approveForm = document.getElementById('approveForm');
  const pdfLink = document.getElementById('pdfLink');
  const rejectForm  = document.getElementById('rejectForm');

  const openModal = () => {
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (frame) frame.src = 'about:blank';
  };

  document.addEventListener('click', (e) => {
    // View button
    const viewBtn = e.target.closest('button[data-action="view"]');
    if (viewBtn) {
      const id = viewBtn.dataset.id;
      if (!id) return;

      if (frame) frame.src = `{{ url('/admission/prereg') }}/${id}/viewer`;

      const action = `{{ url('/admission/prereg') }}/${id}/status`;
      if (pdfLink) pdfLink.href = `{{ url('/admission/prereg') }}/${id}/pdf`;
      if (approveForm) approveForm.action = action;
      if (rejectForm)  rejectForm.action  = action;

      const mStud = document.getElementById('mStudID');
      if (mStud) mStud.value = id;

      openModal();
      return;
    }

    // Close modal
    if (e.target.closest('[data-close="1"]')) {
      closeModal();
      return;
    }
  });

  // ESC closes modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
      closeModal();
    }
  });
})();
</script>
@endsection
