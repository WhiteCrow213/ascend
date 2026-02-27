{{-- resources/views/admissions/enrollment/EnrollmentWorkspace.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Overview')

@push('styles')
<style>
  /* Scoped styles for Enrollment Workplace */
  .ew-page { padding: 18px 22px 26px; font-weight: 200; }
  .ew-hero {
    position: relative;
    border-radius: 18px;
    padding: 26px 26px 18px;
    background: linear-gradient(135deg, rgba(20,35,75,.95), rgba(85,45,170,.55));
    border: 1px solid rgba(255,255,255,.10);
    overflow: hidden;
  }
  .ew-hero:before{
    content:"";
    position:absolute; inset:-80px -120px auto auto;
    width:320px; height:320px;
    background: radial-gradient(circle, rgba(255,255,255,.16), transparent 60%);
    transform: rotate(18deg);
    pointer-events:none;
  }
  .ew-hero-topline{
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap:16px;
    color: rgba(255,255,255,.90);
    font-size: 13px;
    opacity:.95;
    margin-bottom: 14px;
  }
  .ew-hero-topline .left { font-size: 26px; letter-spacing: .2px; }
  .ew-hero-topline .right { white-space: nowrap; opacity:.9; }

  .ew-hero-grid{
    display:grid;
    grid-template-columns: 260px 1fr 360px;
    gap: 22px;
    align-items: stretch;
    margin-top: 6px;
  }

  /* Photo card */
  .ew-photo-card{
    position: relative;
    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 16px;
    padding: 14px;
    height: 210px;
    display:flex;
    align-items:center;
    justify-content:center;
  }
  .ew-photo{
    width: 190px;
    height: 190px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.06));
    border: 1px solid rgba(255,255,255,.14);
    display:flex; align-items:center; justify-content:center;
    color: rgba(255,255,255,.75);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .8px;
  }
  .ew-photo-img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 14px;
    display: block;
  }
  .ew-studno-pill{
    position:absolute;
    left: 14px;
    bottom: 14px;
    background: rgba(255,255,255,.92);
    color: rgba(20,35,75,.95);
    border-radius: 12px;
    padding: 8px 10px;
    display:flex;
    align-items:center;
    gap:8px;
    font-weight: 400;
    font-size: 13px;
    box-shadow: 0 10px 24px rgba(0,0,0,.18);
  }
  .ew-pill-dot{
    width: 18px; height: 18px; border-radius: 50%;
    background: rgba(85,45,170,.25);
    border: 1px solid rgba(85,45,170,.45);
    display:inline-block;
  }

  /* Middle identity block */
  .ew-ident{
    padding: 10px 6px;
    color: rgba(255,255,255,.92);
  }
  .ew-name{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 40px;
    font-weight: 200;
    letter-spacing: .2px;
    line-height: 1.08;
    margin: 8px 0 10px;
  }
  .ew-meta{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 8px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,.14);
  }
  .ew-meta .block{
    font-size: 16px;
    opacity:.95;
    line-height: 1.55;
  }
  .ew-meta .label{ font-size: 13px; opacity: .85; }
  .ew-meta .value{ font-size: 18px; }

  /* Status card right */
  .ew-status-card{
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(255,255,255,.75);
    border-radius: 16px;
    padding: 18px 18px 16px;
    color: #1c2450;
    box-shadow: 0 18px 40px rgba(0,0,0,.18);
    display:flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .ew-status-top{
    display:flex; align-items:center; justify-content: space-between; gap:12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(20,35,75,.10);
  }
  .ew-status-top .left{
    display:flex; align-items:center; gap:10px;
    font-size: 18px;
    font-weight: 500;
    letter-spacing: .4px;
  }
  .ew-status-badge{
    background: rgba(34,139,84,.15);
    color: rgba(34,139,84,1);
    border: 1px solid rgba(34,139,84,.30);
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 13px;
    font-weight: 600;
  }
  .ew-check{
    width: 22px; height: 22px; border-radius: 50%;
    background: rgba(34,139,84,.15);
    border: 1px solid rgba(34,139,84,.30);
    display:flex; align-items:center; justify-content:center;
    color: rgba(34,139,84,1);
    font-size: 12px;
  }
  .ew-status-bottom{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 14px;
  }
  .ew-stat{
    padding: 10px 12px;
    border-radius: 12px;
    background: rgba(20,35,75,.03);
    border: 1px solid rgba(20,35,75,.06);
  }
  .ew-stat .k{ font-size: 12px; opacity:.70; }
  .ew-stat .v{ font-size: 22px; font-weight: 600; margin-top: 3px; }
  .ew-stat .sub{ font-size: 12px; opacity:.75; margin-top: 2px; }


  /* Prevent wrapping for college & program text */
  .ew-meta .value{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Tabs */
  .ew-tabs{
    margin-top: 14px;
    display:flex;
    gap: 26px;
    align-items:flex-end;
    padding: 0 10px;
  }
  .ew-tab{
    position:relative;
    padding: 10px 2px;
    color: rgba(255,255,255,.85);
    text-decoration:none;
    font-weight: 300;
    letter-spacing: .2px;
    cursor: default;
    user-select:none;
  }
  .ew-tab.active{
    color: rgba(255,255,255,.96);
    font-weight: 500;
  }
  .ew-tab.active:after{
    content:"";
    position:absolute;
    left:0; right:0; bottom:-2px;
    height: 3px;
    border-radius: 3px;
    background: rgba(167,139,250,.95);
  }

  /* Content area under hero */
  .ew-body{
    margin-top: 16px;
    display:grid;
    grid-template-columns: 280px 1fr;
    gap: 18px;
  }
  .ew-panel{
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 16px;
    padding: 14px;
  }
  .ew-panel-title{
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .6px;
    opacity: .92;
    margin-bottom: 12px;
    color: rgba(255,255,255,.92);
  }

  /* Action buttons */
  .ew-actions{ display:flex; flex-direction: column; gap: 10px; }
  .ew-action{
    display:flex;
    align-items:center;
    gap: 10px;
    padding: 12px 12px;
    border-radius: 14px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.10);
    color: rgba(255,255,255,.92);
    text-decoration:none;
    font-weight: 300;
    cursor: default;
    user-select:none;
  }
  .ew-ic{
    width: 34px; height: 34px;
    border-radius: 10px;
    background: rgba(167,139,250,.16);
    border: 1px solid rgba(167,139,250,.28);
    display:flex; align-items:center; justify-content:center;
    font-size: 16px;
  }

  /* Quick details */
  .ew-detail{
    padding: 12px;
    border-radius: 14px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.10);
    color: rgba(255,255,255,.90);
    margin-bottom: 10px;
  }
  .ew-detail .k{ font-size: 12px; opacity:.80; }
  .ew-detail .v{ font-size: 18px; margin-top: 4px; font-weight: 400; }

  /* Main content placeholder */
  .ew-main{
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 16px;
    min-height: 360px;
    padding: 18px;
    display:flex;
    align-items:center;
    justify-content:center;
  }

.ew-studno-inline{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.20);
    color: rgba(255,255,255,.95);
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 13px;
    margin-bottom: 10px;
}

  .ew-welcome{
    text-align:center;
    color: rgba(255,255,255,.92);
    max-width: 520px;
  }
  .ew-welcome h2{
    font-size: 24px;
    font-weight: 300;
    margin: 10px 0 8px;
  }
  .ew-welcome p{
    font-size: 14px;
    opacity: .85;
    margin: 0;
  }
  .ew-illu{
    width: 140px;
    height: 140px;
    margin: 0 auto 6px;
    opacity: .95;
  }

  @media (max-width: 1200px){
    .ew-hero-grid{ grid-template-columns: 240px 1fr; }
    .ew-status-card{ grid-column: 1 / -1; }
  }
  @media (max-width: 900px){
    .ew-body{ grid-template-columns: 1fr; }
    .ew-hero-grid{ grid-template-columns: 1fr; }
    .ew-photo-card{ height: 220px; }
  }

  .ew-body{
    color: #1c2450;
}

.ew-panel,
.ew-main{
    background: #ffffff;
    border: 1px solid rgba(20,35,75,.08);
}

.ew-panel-title{
    color: #1c2450;
}

.ew-action{
    color: #1c2450;
    background: rgba(85,45,170,.06);
    border: 1px solid rgba(85,45,170,.12);
}

.ew-detail{
    background: rgba(20,35,75,.04);
    border: 1px solid rgba(20,35,75,.08);
    color: #1c2450;
}

.ew-welcome{
    color: #1c2450;
}
</style>
@endpush

@section('content')
<div class="ew-page">
  @php
    // Dashboard header: Active School Year + current date/time
    // If controller didn't pass $activeTerm, we safely fetch the active term here.
    $activeTerm = $activeTerm ?? \Illuminate\Support\Facades\DB::table('tbl_terms')
        ->where('is_active', 1)
        ->orderByDesc('term_id')
        ->first();

    $schoolYearText = $activeTerm->school_year ?? '—';


    // ✅ Active semester text (DB-confirmed):
    // tbl_terms.semester holds values like 1, 2, or strings like "summer".
    $semesterRaw = $activeTerm->semester ?? null;

    if (is_null($semesterRaw) || trim((string)$semesterRaw) === '') {
        $semesterText = '—';
    } elseif (is_numeric($semesterRaw)) {
        $semInt = (int) $semesterRaw;
        $semesterText = match ($semInt) {
            1 => 'First Semester',
            2 => 'Second Semester',
            default => 'Semester ' . $semInt,
        };
    } else {
        // e.g., "summer" -> "Summer"
        $semesterText = ucwords(str_replace('_', ' ', (string) $semesterRaw));
    }

    $now = \Carbon\Carbon::now();

    // ✅ Student context
    // Controller may pass $student (canonical) and/or $enrollment (optional context).
    // For backward compatibility, we treat $enrollment as a valid snapshot for identity fields.
    $student = $student ?? $enrollment ?? null;

    $studentNumber = $student?->stud_number
        ?? $student?->StudentNumber
        ?? $student?->ApplicantNum
        ?? '—';

    $fullName = trim(implode(' ', array_filter([
        $student?->FirstName ?? null,
        $student?->MiddleName ?? ($student?->MidName ?? null),
        $student?->LastName ?? null,
    ]))) ?: '—';

    // Middle initial display (do not show full middle name)
    $midRaw = $student->MiddleName ?? $student->MidName ?? $student->middle_name ?? null;
    $middleInitial = '';
    if (!empty($midRaw)) {
        $middleInitial = strtoupper(substr(trim($midRaw), 0, 1)) . '.';
    }
    $fullNameDisplay = trim(($student->FirstName ?? '') . ' ' . $middleInitial . ' ' . ($student->LastName ?? ''));


    $programText = $student?->FirstProgramChoice ?? '—';

    // ✅ College text (from program->college join via controller)
    $collegeText = $student?->college_name
        ?? $student?->CollegeName
        ?? null;


  @endphp


  <div class="ew-hero">
    <div class="ew-hero-topline">
      <div class="left">Overview</div>
      <div class="right">
        <span style="opacity:.95;">{{ $schoolYearText }}</span> <span class="mx-1">|</span> <span style="opacity:.95;">{{ $semesterText }}</span>
        <span class="mx-1">|</span>
        <span id="ascendDate"></span>
        <span class="mx-1">|</span>
        <span id="ascendTime"></span>
      </div>
    </div>

    <div class="ew-hero-grid">
      {{-- Photo card --}}
      <div class="ew-photo-card">
        <div class="ew-photo">
          @php
            $photoPath = $student?->profile_photo_path ?? $enrollment?->profile_photo_path ?? null;
            $photoPath = $photoPath ? ltrim($photoPath, '/') : null;
          @endphp

          @if($photoPath)
            <img src="{{ '/storage/' . $photoPath }}" alt="Student Photo" class="ew-photo-img">
          @else
            PHOTO
          @endif
        </div>
      </div>

      {{-- Identity / snapshot --}}
      <div class="ew-ident">

      <div class="ew-studno-inline">
    <span class="ew-pill-dot"></span>
    <span>{{ $studentNumber }}</span>
</div>
        <div class="ew-name">{{ $fullNameDisplay ?? $fullName }}</div>

        <div class="ew-meta">
          <div class="block">
            @if(!empty($collegeText))
            <div class="value">{{ $collegeText }}</div>
            @endif
            <div class="value">{{ $programText }}</div>
          </div>
        </div>
      </div>


      {{-- Enrollment / finance snapshot (right card) --}}
      <div class="ew-status-card">
        <div class="ew-status-top">
          <div class="left">
            <span class="ew-check">✓</span>
            <span>ENROLLED</span>
          </div>
          <div class="ew-status-badge">ENROLLED</div>
        </div>

        <div class="ew-status-bottom">
          <div class="ew-stat">
            <div class="k">Active Term:</div>
            <div class="v">{{ $activeTerm?->term_id ?? '—' }}</div>
            <div class="sub">{{ $semesterText }}</div>
          </div>
          <div class="ew-stat">
            <div class="k">Balance:</div>
            <div class="v">₱ 12,500.00</div>
            <div class="sub">Ledger Placeholder</div>
          </div>
        </div>
      </div>


    </div>

    {{-- Tabs row (placeholders) --}}
    <div class="ew-tabs">
      <span class="ew-tab active">Overview</span>
      <span class="ew-tab">Admission History</span>
      <span class="ew-tab">Enrollment History</span>
      <span class="ew-tab">Grades</span>
      <span class="ew-tab">Billing &amp; Ledger</span>
    </div>

  {{-- Body: left panels + main content --}}
  <div class="ew-body">

    <div>
      <div class="ew-panel">
        <div class="ew-panel-title">STUDENT ACTIONS</div>
        <div class="ew-actions">
          <span class="ew-action"><span class="ew-ic">👤</span> View Information</span>
          <span class="ew-action"><span class="ew-ic">🧾</span> View Grades</span>
          <span class="ew-action"><span class="ew-ic">📚</span> View Load</span>
          <span class="ew-action"><span class="ew-ic">➕</span> Enroll Student</span>
        </div>
      </div>

      <div style="height:14px"></div>

      <div class="ew-panel">
        <div class="ew-panel-title">QUICK DETAILS</div>
        <div class="ew-detail">
          <div class="k">Admission Date:</div>
          <div class="v">Jun 10, 2024</div>
        </div>
        <div class="ew-detail">
          <div class="k">Last Status Update:</div>
          <div class="v">Mar 15, 2024</div>
        </div>
      </div>
    </div>

    <div class="ew-main">
      <div class="ew-welcome">
        {{-- Simple inline SVG illustration placeholder --}}
        <svg class="ew-illu" viewBox="0 0 128 128" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="22" y="18" width="84" height="92" rx="14" fill="rgba(255,255,255,.10)" stroke="rgba(255,255,255,.20)"/>
          <rect x="36" y="34" width="56" height="8" rx="4" fill="rgba(167,139,250,.55)"/>
          <rect x="36" y="52" width="44" height="8" rx="4" fill="rgba(255,255,255,.25)"/>
          <rect x="36" y="70" width="50" height="8" rx="4" fill="rgba(255,255,255,.22)"/>
          <rect x="36" y="88" width="34" height="8" rx="4" fill="rgba(255,255,255,.20)"/>
          <circle cx="96" cy="30" r="10" fill="rgba(34,139,84,.18)" stroke="rgba(34,139,84,.35)"/>
          <path d="M92 30.2l2.6 2.6L100 27.4" stroke="rgba(34,139,84,1)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h2>Welcome to the student overview tab.</h2>
        <p>Please select an option from the tabs above to get started.</p>
      </div>
    </div>

  </div>
</div>


<script>
(function () {
    const dateEl = document.getElementById('ascendDate');
    const timeEl = document.getElementById('ascendTime');
    if (!dateEl || !timeEl) return;

    function pad(n) { return String(n).padStart(2, '0'); }

    function updateClock() {
        const now = new Date();

        const dateStr = now.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit'
        });

        let hours = now.getHours();
        const minutes = pad(now.getMinutes());
        const seconds = pad(now.getSeconds());
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 -> 12
        const timeStr = `${pad(hours)}:${minutes}:${seconds} ${ampm}`;

        dateEl.textContent = dateStr;
        timeEl.textContent = timeStr;
    }

    updateClock();
    setInterval(updateClock, 1000);
})();

  // Fit long names into one line by shrinking font-size (no wrap)
  function fitNameText() {
    const el = document.querySelector('.ew-name');
    if (!el) return;

    const max = 40;   // starting size (matches current design)
    const min = 22;   // do not go smaller than this
    el.style.fontSize = max + 'px';

    // If it overflows, shrink until it fits (or hits min)
    let size = max;
    while (size > min && el.scrollWidth > el.clientWidth) {
      size -= 1;
      el.style.fontSize = size + 'px';
    }
  }


  // Run once after clock paint
  fitNameText();
  window.addEventListener('resize', fitNameText);
</script>

@endsection



