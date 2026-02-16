@extends('layouts.app')

@push('styles')
<style>

/* Force left alignment for Utilities Terms table headers and cells */
.terms-card table.table thead th {
    text-align: left !important;
}
.terms-card table.table tbody td {
    text-align: left !important;
}



/* Restore table spacing (Utilities Terms) */
.terms-card table.table {
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
}

.terms-card table.table thead th,
.terms-card table.table tbody td {
    padding: 14px 18px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
}

.terms-card table.table thead th.actions,
.terms-card table.table tbody td.actions {
    padding-left: 32px !important;
}

.terms-card table.table tbody td.actions form {
    margin: 0 !important;
}

.terms-card table.table tbody td.actions .btn,
.terms-card table.table tbody td.actions button {
    margin-top: 2px !important;
}
</style>

<style>
  /* =========================================================
     ASCEND Utilities Terms — style-only (no logic changes)
     Matches the Pre-Registration Inbox toolbar look & feel.
     ========================================================= */


  /* School Year textbox: keep consistent with other fields */
.sy-input{ max-width: 100%; }

  .prereg-wrap{ padding-bottom: 28px; }

  /* Purple header band like the grid */
  .terms-hero{
    border-radius: 18px 18px 0 0;
    background: #6a00b8;
    padding: 22px 22px 18px;
    color: #fff;
  }
  .terms-hero h1{
    margin: 0;
    font-size: 34px;
    line-height: 1.05;
    font-weight: 900;
    letter-spacing: -0.5px;
  }
  .terms-hero p{
    margin: 8px 0 0;
    opacity: .92;
  }

  /* Toolbar card container */
  .terms-toolbar{
    background: #fff;
    border: 1px solid #eef2f7;
    border-top: none;
    border-radius: 0 0 18px 18px;
    padding: 14px;
    box-shadow: 0 12px 26px rgba(17,24,39,.08);
  }

  /* Button styling (pill + border like your screenshot) */
  .btn{
    border-radius: 999px !important;
    padding: 10px 14px !important;
    font-weight: 800 !important;
    letter-spacing: .1px;
  }
  .btn-muted{
    background: #fff !important;
    border: 2px solid #e5e7eb !important;
    color: #111827 !important;
  }
  .btn-muted:hover{ background:#f9fafb !important; }

  .btn-primary{
    background: #6a00b8 !important;
    border: 2px solid #6a00b8 !important;
    color: #fff !important;
  }
  .btn-primary:hover{ filter: brightness(.97); }

  /* Outline purple button (Walk-in style) */
  .btn-outline-purple{
    background: #fff !important;
    border: 2px solid #6a00b8 !important;
    color: #6a00b8 !important;
  }
  .btn-outline-purple:hover{ background: #faf5ff !important; }

  /* Back button icon alignment */
  .btn-back{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }
  .btn-back .arrow{
    display:inline-block;
    width: 18px;
    height: 18px;
    line-height: 18px;
    text-align:center;
    border-radius: 999px;
    border: 2px solid #e5e7eb;
    font-weight: 900;
  }

  /* Inputs like the grid */
  .terms-input{
    height: 44px;
    border-radius: 999px;
    border: 2px solid #e5e7eb;
    padding: 0 14px;
    outline: none;
    width: 100%;
    background: #fff;
    box-sizing: border-box;
  }
  .terms-input:focus{
    border-color: #c4b5fd;
    box-shadow: 0 0 0 4px rgba(196,181,253,.35);
  }

  /* Select with caret styling */
  .terms-select{
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image:
      linear-gradient(45deg, transparent 50%, #6b7280 50%),
      linear-gradient(135deg, #6b7280 50%, transparent 50%);
    background-position:
      calc(100% - 18px) 18px,
      calc(100% - 12px) 18px;
    background-size:
      6px 6px,
      6px 6px;
    background-repeat: no-repeat;
    padding-right: 40px;
  }

  /* Card polish for form & table */
  .terms-card{
    border: 1px solid #eef2f7;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 10px 22px rgba(17,24,39,.06);
  }

  .terms-section-title{
    margin: 0 0 10px;
    font-size: 18px;
    font-weight: 900;
    color: #111827;
  }

  .terms-alert{
    border-radius: 14px;
    padding: 12px 14px;
    margin-top: 10px;
    border: 1px solid;
  }
  .terms-alert.ok{ border-color:#bbf7d0; background:#f0fdf4; color:#166534; }
  .terms-alert.err{ border-color:#fecaca; background:#fef2f2; color:#991b1b; }

  /* ===============================
     TOOLBAR LAYOUT (NO OVERLAP)
     =============================== */

  .terms-toolbar .prereg-actions{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    flex-wrap:nowrap; /* stops Reset from dropping into the left cluster */
  }

  .terms-toolbar .prereg-actions-left,
  .terms-toolbar .prereg-actions-right{
    display:flex;
    align-items:center;
    gap:12px;
  }

  .terms-toolbar .prereg-actions-left{
    flex: 1 1 auto;
    flex-wrap:wrap;   /* left group can wrap internally */
    min-width: 260px;
  }

  .terms-toolbar .prereg-actions-right{
    flex: 0 0 auto;
  }

  .terms-toolbar .btn{ white-space:nowrap; }

  /* ===============================
     FORM FIELD WIDTHS (DROPDOWN)
     =============================== */

  .terms-form-grid{
    display:grid;
    grid-template-columns: minmax(260px, 360px) minmax(260px, 360px); /* align Generate Term with School Year */
    gap:12px;
    justify-content:start; /* keep fields compact, avoid stretching */
    align-items:start; /* top-align labels + inputs */
  }

  .terms-form-grid > div{
    width:100%;
    max-width:360px; /* same width for textbox + dropdown */
  }

  @media (max-width: 740px){
    .terms-toolbar .prereg-actions{
      flex-wrap:wrap;
    }
    .terms-form-grid{
      grid-template-columns: 1fr;
    }

    .terms-form-grid > div{ max-width:100%; }
  }


  /* ===============================
     FIX CARD/INPUT OVERFLOW (DATE GRID)
     =============================== */

  .terms-card{ overflow: hidden; }
  .terms-input{ min-width: 0; max-width: 100%; }

  /* Date cards grid: prevent columns from overflowing and "overlapping" visually */
  .terms-dates-grid{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap:12px;
    margin-top:12px;
  }
  .terms-dates-grid > .terms-card{ min-width: 0; }

  /* Stack date cards earlier on smaller widths */
  @media (max-width: 980px){
    .terms-dates-grid{ grid-template-columns: 1fr; }
  }


  /* ===============================
     DATE CARDS SPACING (VISUAL CLEANUP)
     =============================== */
  .terms-dates-grid .terms-card{
    padding: 14px !important;
  }
  .terms-dates-grid .terms-card label{
    margin-top: 10px;
  }
  .terms-dates-grid .terms-card .terms-input{
    margin-top: 4px;
  }


/* === School Year textbox: keep consistent with other fields === */
#school_year{ width: 100% !important; max-width: 100% !important; }


/* === Normalize height for textbox & dropdown (visual only) === */
.terms-input,
.terms-select{
  height: 44px;
  line-height: 44px;
}

</style>
@endpush

@section('content')
<div class="prereg-wrap">

  @php
    // UI helper: if any term is active, hide activation controls (no logic changes)
    $hasActiveTerm = collect($terms ?? [])->contains(function($row){
      return (int)($row->is_active ?? 0) === 1;
    });
  @endphp

  {{-- HERO + TOOLBAR (grid-style) --}}
  <div class="terms-hero">
    <h1>Utilities · School Year & Terms</h1>
    <p>Create terms and set the active term for enrollment.</p>
  </div>

  <div class="terms-toolbar">
    <div class="prereg-actions" style="margin:0;">
      <div class="prereg-actions-left">
        <a href="{{ route('dashboard') }}" class="btn btn-muted btn-back">
          <span class="arrow">←</span> Back
        </a>
      </div>
    </div>

    @if(session('ok'))
      <div class="terms-alert ok">{{ session('ok') }}</div>
    @endif

    @if ($errors->any())
      <div class="terms-alert err">
        <strong>Fix these:</strong>
        <ul style="margin:8px 0 0 18px;">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  {{-- CREATE --}}
  <div id="create" class="terms-card" style="margin-top:14px; padding:14px;">
    <h2 class="terms-section-title">Create School Year</h2>

    <form method="POST" action="{{ route('utilities.terms.store') }}">
      @csrf

      <div class="terms-form-grid">
        <div>
          <label style="display:block; font-weight:800; margin-bottom:6px;">School Year (YYYY-YYYY)</label>
          <input
  type="text"
  id="school_year" name="school_year"
  class="terms-input sy-input"
  placeholder="2026-2027"
>
<div style="font-size:12px; color:#6b7280; margin-top:6px;">
            Create one term at a time, then set start and end dates below.
          </div>

          <div style="height:14px;"></div>

          <label style="display:block; font-weight:800; margin-bottom:6px;">Start Date (optional)</label>
          <input type="date" id="start_date_ui" class="terms-input"
                 value="{{ old('start_date_1') ?? old('start_date_2') ?? old('start_date_summer') ?? '' }}">
        </div>

        <div>
          <label style="display:block; font-weight:800; margin-bottom:6px;">Generate Term</label>

          {{-- Dropdown (style-only change; still submits semesters[] as your controller expects) --}}
          <select class="terms-input terms-select" name="semesters[]" required>
            <option value="" disabled {{ empty(old('semesters')) ? 'selected' : '' }}>Select term…</option>

            <option value="1" {{ in_array('1', (array)old('semesters', []), true) ? 'selected' : '' }}>
              First Semester
            </option>

            <option value="2" {{ in_array('2', (array)old('semesters', []), true) ? 'selected' : '' }}>
              Second Semester
            </option>

            <option value="summer" {{ in_array('summer', (array)old('semesters', []), true) ? 'selected' : '' }}>
              Midyear
            </option>
          </select>

          <div style="font-size:12px; color:#6b7280; margin-top:6px;">
            Create one term at a time, then set the active term below.
          </div>

          <div style="height:14px;"></div>

          <label style="display:block; font-weight:800; margin-bottom:6px;">End Date (optional)</label>
          <input type="date" id="end_date_ui" class="terms-input"
                 value="{{ old('end_date_1') ?? old('end_date_2') ?? old('end_date_summer') ?? '' }}">

          <!-- Hidden fields expected by controller -->
          <input type="hidden" name="start_date_1" id="start_date_1" value="{{ old('start_date_1') }}">
          <input type="hidden" name="end_date_1" id="end_date_1" value="{{ old('end_date_1') }}">
          <input type="hidden" name="start_date_2" id="start_date_2" value="{{ old('start_date_2') }}">
          <input type="hidden" name="end_date_2" id="end_date_2" value="{{ old('end_date_2') }}">
          <input type="hidden" name="start_date_summer" id="start_date_summer" value="{{ old('start_date_summer') }}">
          <input type="hidden" name="end_date_summer" id="end_date_summer" value="{{ old('end_date_summer') }}">
        </div>
      </div>

     

      <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" type="submit">Save School Year / Term</button>
        <a href="{{ route('utilities.terms.index') }}" class="btn btn-muted">Refresh List</a>
      </div>
    

      <script>
        // termsDatesSync: maps the visible Start/End Date inputs to the hidden fields
        // that the controller already accepts (start_date_1/end_date_1, etc.)
        (function termsDatesSync(){
          const sel = document.querySelector('select[name="semesters[]"]');
          const startUI = document.getElementById('start_date_ui');
          const endUI   = document.getElementById('end_date_ui');

          const map = {
            '1': { s: 'start_date_1', e: 'end_date_1' },
            '2': { s: 'start_date_2', e: 'end_date_2' },
            'summer': { s: 'start_date_summer', e: 'end_date_summer' },
          };

          function clearAll(){
            ['start_date_1','end_date_1','start_date_2','end_date_2','start_date_summer','end_date_summer']
              .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
          }

          function apply(){
            if(!sel) return;
            const val = sel.value;
            clearAll();
            const m = map[val];
            if(!m) return;
            const hs = document.getElementById(m.s);
            const he = document.getElementById(m.e);
            if(hs) hs.value = startUI ? startUI.value : '';
            if(he) he.value = endUI ? endUI.value : '';
          }

          if(sel){ sel.addEventListener('change', apply); }
          if(startUI){ startUI.addEventListener('change', apply); }
          if(endUI){ endUI.addEventListener('change', apply); }

          // Initialize on load so old() values are correctly mapped for submission
          apply();
        })();
      </script>

</form>
  </div>

  {{-- LIST --}}
  <div class="terms-card" style="margin-top:14px;">
    <table class="table">
      <thead>
        <tr>
          <th>School Year</th>
          <th>Semester</th>
          <th>Start</th>
          <th>End</th>
          <th>Active</th>
          <th class="actions">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($terms as $t)
          <tr>
            <td><strong>{{ $t->school_year }}</strong></td>
            <td>
              @if($t->semester === '1') First Semester
              @elseif($t->semester === '2') Second Semester
              @else Midyear
              @endif
            </td>
            <td>{{ $t->start_date ?? '—' }}</td>
            <td>{{ $t->end_date ?? '—' }}</td>
            <td>
              @if((int)$t->is_active === 1)
                <span style="font-weight:900; color:#166534;">ACTIVE</span>
              @else
                <span style="color:#6b7280;">—</span>
              @endif
            </td>
            <td class="actions">
              @if((int)$t->is_active !== 1)
                <form method="POST" action="{{ route('utilities.terms.active', $t->term_id) }}">
                  @csrf
                  <button class="btn btn-primary" type="submit">Set Active</button>
                </form>
              @else
                {{-- active term: show status in the Active column only (no action button) --}}
                <span style="color:#6b7280;">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="empty">No terms yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
