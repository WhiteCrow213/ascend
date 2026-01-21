@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/prereg.css') }}">
@endpush

@section('content')
<div class="prereg-wrap">

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
          <a href="{{ route('admission.index') }}" class="btn btn-muted">← Back</a>

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

            <td>{{ $a->created_at->format('M d, Y') }}</td>

            <td>
              <span class="badge">Pending</span>
            </td>

            <td class="actions">
              <button class="btn" type="button">View</button>
              <button class="btn btn-primary" type="button">Approve</button>
              <button class="btn" type="button" style="border-color:#fca5a5;color:#b91c1c;">
                Reject
              </button>
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
      {{ $applicants->links() }}
    </div>
  </div>

</div>

<script>
(function () {
  const form = document.getElementById('preregSearch');
  const input = document.getElementById('preregSearchInput');
  const status = document.getElementById('preregStatusSelect');
  const resetBtn = document.getElementById('preregResetBtn');
  const walkinBtn = document.getElementById('walkinBtn');

  if (!form || !input) return;

  // Debounced auto-submit (type -> results)
  let t = null;
  const debounceSubmit = (ms = 350) => {
    clearTimeout(t);
    t = setTimeout(() => form.requestSubmit(), ms);
  };

  input.addEventListener('input', () => debounceSubmit(350));
  if (status) status.addEventListener('change', () => form.requestSubmit());

  // Hotkeys:
  // "/" focuses search
  // "Esc" resets
  // "Alt + N" opens Walk-in form
  document.addEventListener('keydown', (e) => {
    const tag = e.target?.tagName ? e.target.tagName.toLowerCase() : '';
    const typing = (tag === 'input' || tag === 'textarea' || e.target?.isContentEditable);

    if (!typing && e.key === '/') {
      e.preventDefault();
      input.focus();
      input.select();
      return;
    }

    if (e.key === 'Escape') {
      e.preventDefault();
      if (resetBtn?.href) window.location.href = resetBtn.href;
      return;
    }

    if (e.altKey && (e.key === 'n' || e.key === 'N')) {
      e.preventDefault();
      if (walkinBtn?.href) window.location.href = walkinBtn.href;
    }
  });
})();
</script>
@endsection
