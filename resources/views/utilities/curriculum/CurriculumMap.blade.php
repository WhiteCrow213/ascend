 @extends('layouts.app')

@section('title', 'Utilities | Curriculum Map')

@push('styles')
<style>
  /*
    ASCEND Curriculum Map (Color restored)
    Hero uses an OPAQUE purple gradient (no alpha stacking) so you get the "ASCEND colors"
    like the sidebar vibe, without the washed-out look.
    Panels remain clean/white like other Utilities forms.
  */

  .cmx-wrap{ padding:18px 22px; font-weight:200; }

  .cmx-hero{
    border-radius: 18px;
    padding: 18px 18px 14px 18px;
    background: linear-gradient(135deg, #3a2a72 0%, #6b5bb7 100%);
    border: 1px solid rgba(120, 100, 210, .35);
    box-shadow: 0 14px 34px rgba(18, 24, 60, .14);
    margin-bottom: 14px;
  }

  .cmx-hero-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:14px;
    margin-bottom: 10px;
  }

  .cmx-title{ font-size:22px; font-weight:200; margin:0; color: rgba(255,255,255,.95); }
  .cmx-sub{ margin-top:4px; font-size:13px; font-weight:200; color: rgba(255,255,255,.78); }

  .cmx-toast{
    background: #ffffff;
    border: 1px solid rgba(91,76,230,.35);
    color: #4b3fd1;
    padding: 10px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 8px 18px rgba(18,24,60,.10);
  }

  .cmx-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap:10px;
    align-items:end;
  }

  .cmx-grid label{
    font-size:12px;
    opacity:.82;
    color: rgba(255,255,255,.82);
  }

  .cmx-hero .form-control,
  .cmx-hero select.form-control{
    background:#ffffff;
    border: 1px solid rgba(255,255,255,.65);
    color:#1c2450;
    border-radius: 12px;
    height: 38px;
  }

  .cmx-hero .btn{ height: 38px; border-radius: 12px; }

  .cmx-panel{
    border-radius: 16px;
    background: #ffffff;
    border: 1px solid rgba(20,35,75,.08);
    box-shadow: 0 10px 22px rgba(18, 24, 60, .06);
    padding: 14px;
    margin-top: 14px;
  }

  .cmx-panel-title{
    font-size: 12.5px;
    letter-spacing: .7px;
    font-weight: 600;
    opacity: .9;
    color: #1c2450;
    margin: 0 0 10px 0;
  }

  .cmx-add{
    display:grid;
    grid-template-columns: 1.6fr 1fr 1fr 1fr auto;
    gap:10px;
    align-items:end;
  }

  .cmx-muted{ font-size:12px; color: rgba(28,36,80,.65); margin-top:6px; }

  .cmx-panel .form-control,
  .cmx-panel select.form-control{
    border-radius: 12px;
    height: 38px;
  }

  .cmx-panel .btn{ border-radius: 12px; }

  .cmx-pill{
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 12px;
    white-space: nowrap;
    display:inline-block;
  }

  .cmx-pill-yes{
    background: rgba(34,139,84,.12);
    border: 1px solid rgba(34,139,84,.22);
    color: rgba(34,139,84,1);
  }

  .cmx-pill-no{
    background: rgba(20,35,75,.04);
    border: 1px solid rgba(20,35,75,.10);
    color: rgba(28,36,80,.85);
  }

  .cmx-table th{
    white-space:nowrap;
    font-weight:600;
    font-size:12.5px;
    color:#1c2450;
  }

  .cmx-table td{
    font-size:13px;
    color: rgba(28,36,80,.95);
    vertical-align:middle;
  }

  /* Buttons (match SubjectsModal style) */
    .cmx-btn-primary{
    padding:8px 18px;
    border-radius:12px;
    font-weight:600;
    font-size:13px;
    border:none;
    cursor:pointer;
    background: linear-gradient(135deg,#5B4CE6 0%, #7A5FFF 100%);
    color:#fff;
    box-shadow: 0 8px 18px rgba(91,76,230,.28);
    height:36px;
    min-width:100px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    white-space:nowrap;
  }
  .cmx-btn-ghost{
    padding:10px 14px;
    border-radius:14px;
    font-weight:900;
    border:1px solid rgba(91,76,230,.25);
    background:#fff;
    color:#4b3fd1;
    cursor:pointer;
    height:38px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    white-space:nowrap;
  }
    .cmx-btn-danger{
    padding:6px 14px;
    border-radius:12px;
    font-weight:600;
    font-size:12px;
    border:1px solid rgba(220,53,69,.25);
    background: rgba(220,53,69,.06);
    color:#b32636;
    cursor:pointer;
    height:30px;
    min-width:80px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    white-space:nowrap;
    margin-left:-8px;
  }


  /* Table alignment (fixed layout) */
  .cmx-table{
    table-layout: fixed;
    width: 100%;
  }
  .cmx-table th,
  .cmx-table td{
    vertical-align: middle;
    padding: 12px 14px;
  }
  .cmx-table th{ text-align: left; }
  .cmx-table th.num,
  .cmx-table td.num{ text-align: center; }

  .cmx-table td,
  .cmx-table th{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }


  /* Group separators (Year Level / Semester) */
  .cmx-sep-row td{
    padding: 10px 12px !important;
    font-weight: 700;
    font-size: 12.5px;
    letter-spacing: .6px;
    text-transform: uppercase;
    color: rgba(28,36,80,.92);
    background: rgba(91,76,230,.06);
    border-top: 1px solid rgba(20,35,75,.10);
    border-bottom: 1px solid rgba(20,35,75,.06);
  }
  .cmx-sep-year td{ background: rgba(91,76,230,.10); }
  .cmx-sep-sem td{
    background: rgba(91,76,230,.06);
    font-weight: 600;
    text-transform: none;
    letter-spacing: .2px;
  }


  /* Semester subtotal row */
  .cmx-subtotal-row td{
    padding: 10px 12px !important;
    background: rgba(20,35,75,.03);
    border-top: 1px dashed rgba(20,35,75,.18);
    color: rgba(28,36,80,.92);
    font-size: 12.5px;
    font-weight: 600;
  }
  .cmx-subtotal-pill{
    display:inline-block;
    padding:4px 10px;
    border-radius: 999px;
    border: 1px solid rgba(91,76,230,.18);
    background: rgba(91,76,230,.06);
    color: rgba(28,36,80,.92);
    font-weight: 700;
    font-size: 12px;
    white-space: nowrap;
  }


  /* Slightly larger active search textbox */
  #cmx_subjectSearch{
    height: 42px !important;
    font-size: 14px;
    padding: 10px 12px;
  }


  /* Immediate popup (modal) for critical alerts */
  .cmx-modal-backdrop{
    position: fixed;
    inset: 0;
    background: rgba(12,18,45,.45);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
  }
  .cmx-modal{
    width: min(520px, 100%);
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(20,35,75,.12);
    box-shadow: 0 22px 60px rgba(18,24,60,.22);
    overflow: hidden;
  }
  .cmx-modal-head{
    padding: 12px 14px;
    background: rgba(220,53,69,.08);
    border-bottom: 1px solid rgba(220,53,69,.18);
    color:#b32636;
    font-weight: 800;
    font-size: 13px;
    letter-spacing: .4px;
    text-transform: uppercase;
  }
  .cmx-modal-body{
    padding: 14px;
    color: rgba(28,36,80,.95);
    font-size: 13.5px;
    line-height: 1.4;
  }
  .cmx-modal-actions{
    padding: 12px 14px;
    border-top: 1px solid rgba(20,35,75,.08);
    display:flex;
    justify-content:flex-end;
    gap:10px;
  }
  .cmx-modal-btn{
    height: 36px;
    padding: 8px 14px;
    border-radius: 12px;
    border: 1px solid rgba(20,35,75,.12);
    background: #fff;
    color: rgba(28,36,80,.95);
    font-weight: 700;
    font-size: 13px;
    cursor:pointer;
  }
  .cmx-modal-btn.primary{
    border: 1px solid rgba(220,53,69,.22);
    background: rgba(220,53,69,.10);
    color:#b32636;
  }


  /* Top Back Button */
  .cmx-topbar{
    display:flex;
    align-items:center;
    margin-bottom: 10px;
  }
  .cmx-back-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius: 12px;
    border:1px solid rgba(91,76,230,.25);
    background:#fff;
    color:#4b3fd1;
    font-weight:700;
    font-size:13px;
    text-decoration:none;
    transition: all .15s ease;
  }
  .cmx-back-btn:hover{
    background: rgba(91,76,230,.06);
  }

</style>
@endpush

@section('content')
<div class="cmx-wrap">

  
  <div class="cmx-topbar">
    <a href="{{ route('utilities.master-data') }}" class="cmx-back-btn">
      ← Back
    </a>
  </div>
<div class="cmx-hero">
    <div class="cmx-hero-top">
      <div>
        <h2 class="cmx-title">Curriculum Map</h2>
        <div class="cmx-sub">Assign subjects to a curriculum by year level and semester.</div>
      </div>

      @if(session('success'))
        <div class="cmx-toast">{{ session('success') }}</div>
      @endif
    </div>

    <form method="GET" action="{{ url()->current() }}" class="cmx-grid">
      <div>
        <select name="IDcurr" class="form-control" required onchange="this.form.submit()">
          <option value="">-- Select Curriculum --</option>
          @foreach($curriculums as $c)
            <option value="{{ $c->IDcurr }}" @selected((string)$IDcurr === (string)$c->IDcurr)>
              {{ $c->CurrName }}@if((int)$c->is_active === 1) (Active)@endif
            </option>
          @endforeach
        </select>
      </div>
    </form>
  </div>

  <div class="cmx-panel">
    <div class="cmx-panel-title">ADD SUBJECT TO CURRICULUM</div>

    <form method="POST" action="{{ route('utilities.curriculum.map.store') }}" class="cmx-add">
      @csrf
      <input type="hidden" name="IDcurr" value="{{ $IDcurr ?? '' }}">

      <div>
        <label style="font-size:12px; opacity:.75; color:#1c2450;">Subject</label>
        <input
          type="text"
          id="subjectSearch"
          class="form-control"
          autocomplete="off"
          @disabled(!$IDcurr)
        >
        <input type="hidden" name="IDsubj" id="selectedIDsubj" value="" />

        <div id="subjectResultsWrap" style="position:relative; display:none;">
          <div id="subjectResults"
               style="position:absolute; left:0; right:0; top:8px; background:#fff; border:1px solid rgba(20,35,75,.12);
                      border-radius:12px; box-shadow:0 14px 30px rgba(18,24,60,.12); max-height:260px; overflow:auto; z-index:1000;">
          </div>
        </div>

        <div class="cmx-muted" id="subjectPickHint" style="display:none;">Select a subject from the list.</div>

        @if(!$IDcurr)
          <div class="cmx-muted">Select a curriculum first.</div>
        @endif
      </div>

      <div>
        <label style="font-size:12px; opacity:.75; color:#1c2450;">Year Level</label>
        <select name="IDyearlvl" class="form-control" required @disabled(!$IDcurr)>
          @foreach($yearlevels as $yl)
            <option value="{{ $yl->IDyearlvl }}">{{ $yl->YearLevelName }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="font-size:12px; opacity:.75; color:#1c2450;">Semester</label>
        <select name="semester" class="form-control" required @disabled(!$IDcurr)>
          <option value="1">1st Semester</option>
          <option value="2">2nd Semester</option>
          <option value="3">Midyear</option>
        </select>
      </div>

      <div style="display:flex; align-items:center; gap:10px; height:38px;">
        <label style="display:flex; align-items:center; gap:8px; margin:0; font-size:13px; color:#1c2450;">
</label>
      </div>

      <button type="submit" class="cmx-btn-primary" @disabled(!$IDcurr)>Add</button>
    </form>
  </div>

  <div class="cmx-panel">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px;">
      <div class="cmx-panel-title" style="margin:0;">MAPPED SUBJECTS</div>
      <div style="font-size:12px; color: rgba(28,36,80,.70);">{{ $mapped->count() }} record(s)</div>
    </div>

    <div style="overflow:auto;">
      <table class="table table-sm cmx-table" style="min-width:920px;">

      <colgroup>
        <col style="width:14%;">   <!-- Course Code -->
        <col style="width:28%;">   <!-- Description -->
        <col style="width:8%;">    <!-- Units -->
        <col style="width:14%;">   <!-- Year Level -->
        <col style="width:14%;">   <!-- Semester -->
        <col style="width:14%;">   <!-- Prerequisites -->
        <col style="width:8%;">    <!-- Actions -->
      </colgroup>

        <thead>
          <tr>
            <th>Course Code</th>
            <th>Description</th>
            <th class="num">Units</th>
            <th>Year Level</th>
            <th>Semester</th>
            <th>Prerequisites</th>
            <th style="width:120px;"></th>
          </tr>
        </thead>
        <tbody>
          @php
              $prevYear = null;
              $prevSem  = null;
              $semCount = 0;
              $semUnits = 0;
            @endphp
            @forelse($mapped as $m)
              @php
                $curYearName = $m->YearLevelName ?? ('ID: '.$m->IDyearlvl);
                $curSemLabel = ((string)$m->semester === '1') ? '1st Semester' : (((string)$m->semester === '2') ? '2nd Semester' : 'Midyear');
              @endphp

              {{-- If moving to a new year/semester group, print subtotal for the previous semester --}}
              @if($prevSem !== null && ($prevYear !== $curYearName || $prevSem !== $curSemLabel))
                <tr class="cmx-subtotal-row">
                  <td colspan="7">
                    <span class="cmx-subtotal-pill">
                      Total ({{ $prevSem }}): {{ $semCount }} subject(s), {{ $semUnits }} unit(s)
                    </span>
                  </td>
                </tr>
                @php $semCount = 0; $semUnits = 0; @endphp
              @endif

              @if($prevYear !== $curYearName)
                <tr class="cmx-sep-row cmx-sep-year">
                  <td colspan="7">{{ $curYearName }}</td>
                </tr>
                @php $prevYear = $curYearName; $prevSem = null; @endphp
              @endif

              @if($prevSem !== $curSemLabel)
                <tr class="cmx-sep-row cmx-sep-sem">
                  <td colspan="7">{{ $curSemLabel }}</td>
                </tr>
                @php $prevSem = $curSemLabel; @endphp
              @endif

              <tr>
                <td>{{ $m->CourseCode }}</td>
                <td>{{ $m->CourseDescription }}</td>
                <td class="num">{{ $m->Units }}</td>
                <td>{{ $m->YearLevelName ?? ('ID: '.$m->IDyearlvl) }}</td>
                <td>{{ $curSemLabel }}</td>
                @php
                  $prereqCodes = null;
                  if (is_object($m) && property_exists($m, 'PrereqCodes')) {
                    $prereqCodes = $m->PrereqCodes;
                  } elseif (is_object($m) && property_exists($m, 'PrereqCourseCodes')) {
                    $prereqCodes = $m->PrereqCourseCodes;
                  } elseif (is_array($m) && array_key_exists('PrereqCodes', $m)) {
                    $prereqCodes = $m['PrereqCodes'];
                  } elseif (is_array($m) && array_key_exists('PrereqCourseCodes', $m)) {
                    $prereqCodes = $m['PrereqCourseCodes'];
                  }
                @endphp
                <td title="{{ $prereqCodes ?? '' }}">{{ ($prereqCodes ?? '') !== '' ? $prereqCodes : '—' }}</td>
                <td>
                  <form method="POST" action="{{ route('utilities.curriculum.map.delete', ['CurrMapID' => $m->CurrMapID]) }}">
                    @csrf
                    <button type="submit" class="cmx-btn-danger"
                            onclick="return confirm('Remove this mapping?')">
                      Remove
                    </button>
                  </form>
                </td>
              </tr>

              @php
                $semCount++;
                $semUnits += (float)($m->Units ?? 0);
              @endphp
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:18px; color: rgba(28,36,80,.70);">
                  No mapped subjects yet. Select a curriculum and add subjects above.
                </td>
              </tr>
            @endforelse

            {{-- Final subtotal for the last semester group --}}
            @if($prevSem !== null && $semCount > 0)
              <tr class="cmx-subtotal-row">
                <td colspan="7">
                  <span class="cmx-subtotal-pill">
                    Total ({{ $prevSem }}): {{ $semCount }} subject(s), {{ $semUnits }} unit(s)
                  </span>
                </td>
              </tr>
            @endif
        </tbody>
      </table>
    </div>
  </div>

</div>
{{-- Immediate popup for duplicate/validation messages --}}
  @if(session('error'))
    <div id="cmxModalBackdrop" class="cmx-modal-backdrop" aria-modal="true" role="dialog">
      <div class="cmx-modal">
        <div class="cmx-modal-head">Already Added</div>
        <div class="cmx-modal-body">
          {{ session('error') }}
        </div>
        <div class="cmx-modal-actions">
          <button type="button" class="cmx-modal-btn primary" id="cmxModalOk">OK</button>
        </div>
      </div>
    </div>
  @endif

<script>
(function(){
  const input = document.getElementById('subjectSearch');
  const hidden = document.getElementById('selectedIDsubj');
  const wrap = document.getElementById('subjectResultsWrap');
  const box = document.getElementById('subjectResults');
  const hint = document.getElementById('subjectPickHint');

  if(!input || !hidden || !wrap || !box){
    console.error('[CurrMap] Active search elements missing:', {input,hidden,wrap,box});
    return;
  }

  // Subjects dataset from server
  const SUBJECTS = @json($subjects ?? []);

  function esc(str){
    return String(str ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    })[m]);
  }

  function norm(s){
    return String(s ?? '').toLowerCase();
  }

  function render(items){
    if(items.length === 0){
      box.innerHTML = '<div style="padding:10px 12px; color:rgba(28,36,80,.65); font-size:13px;">No matches.</div>';
    } else {
      box.innerHTML = items.map(s => {
        const id = s.IDsubj ?? s.id ?? '';
        const code = s.CourseCode ?? s.code ?? '';
        const desc = s.CourseDescription ?? s.desc ?? '';
        const units = s.Units ?? s.units ?? '';
        return `
          <button type="button" data-id="${esc(id)}" data-label="${esc(code)} - ${esc(desc)}"
            style="width:100%; text-align:left; padding:10px 12px; border:0; background:#fff; cursor:pointer;">
            <div style="font-weight:700; color:#1c2450;">${esc(code)}</div>
            <div style="font-size:12px; color:rgba(28,36,80,.75);">${esc(desc)}${units !== '' ? ' • ' + esc(units) + 'u' : ''}</div>
          </button>
        `;
      }).join('');

      // hover
      Array.from(box.querySelectorAll('button')).forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.background = 'rgba(91,76,230,.08)');
        btn.addEventListener('mouseleave', () => btn.style.background = '#fff');
      });
    }
    wrap.style.display = 'block';
    if(hint) hint.style.display = 'block';
  }

  function hide(){
    wrap.style.display = 'none';
    if(hint) hint.style.display = 'none';
  }

  let t = null;
  input.addEventListener('input', function(){
    hidden.value = '';
    const q = norm(input.value).trim();
    clearTimeout(t);
    t = setTimeout(() => {
      if(!q){ hide(); return; }
      const matches = SUBJECTS
        .filter(s => {
          const code = norm(s.CourseCode ?? s.code);
          const desc = norm(s.CourseDescription ?? s.desc);
          return (code + ' ' + desc).includes(q);
        })
        .slice(0, 30);
      render(matches);
    }, 100);
  });

  box.addEventListener('click', function(e){
    const btn = e.target.closest('button[data-id]');
    if(!btn) return;
    hidden.value = btn.getAttribute('data-id');
    input.value = btn.getAttribute('data-label');
    hide();
  });

  document.addEventListener('click', function(e){
    if(e.target === input || wrap.contains(e.target)) return;
    hide();
  });

  const form = input.closest('form');
  if(form){
    form.addEventListener('submit', function(e){
      if(!hidden.value){
        e.preventDefault();
        input.focus();
        render(SUBJECTS.slice(0, 30));
      }
    });
  }

  // If user focuses, show top results quickly
  input.addEventListener('focus', function(){
    if(input.value.trim() === '') return;
    const q = norm(input.value).trim();
    const matches = SUBJECTS
      .filter(s => (norm(s.CourseCode ?? s.code) + ' ' + norm(s.CourseDescription ?? s.desc)).includes(q))
      .slice(0, 30);
    render(matches);
  });

  console.log('[CurrMap] Active search ready. Subjects:', SUBJECTS.length);
})();

  // auto-hide toast
  (function(){
    const t = document.getElementById('cmxToast');
    if(!t) return;
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => {
      t.classList.remove('show');
      setTimeout(() => { if(t && t.parentNode) t.parentNode.removeChild(t); }, 400);
    }, 3200);
  })();


  // immediate popup for errors
  (function(){
    const bd = document.getElementById('cmxModalBackdrop');
    if(!bd) return;
    const ok = document.getElementById('cmxModalOk');
    const close = () => { bd.style.display = 'none'; };
    bd.style.display = 'flex';
    if(ok) ok.addEventListener('click', close);
    bd.addEventListener('click', (e) => { if(e.target === bd) close(); });
    document.addEventListener('keydown', (e) => { if(e.key === 'Escape') close(); });
  })();

</script>

@endsection
