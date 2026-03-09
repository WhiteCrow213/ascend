@extends('layouts.app')

@section('title', 'Sections & Offerings')

@push('styles')
<style>
  /* =========================================================
     ASCEND — Sections & Offerings (Revamp)
     Rule 3: ALWAYS USE ASCEND THEME ON EVERYTHING!
     ========================================================= */

  :root{
    --a-pri: rgba(124,58,237,1);
    --a-pri-10: rgba(124,58,237,.10);
    --a-pri-14: rgba(124,58,237,.14);
    --a-pri-18: rgba(124,58,237,.18);
    --a-border: rgba(0,0,0,.08);
    --a-border2: rgba(0,0,0,.10);
    --a-muted: #6b7280;
    --a-text: #111827;
  }

  .so{ padding:16px; }
  .so-shell{
    background: linear-gradient(180deg, rgba(124,58,237,.05), rgba(124,58,237,0) 220px);
    border-radius: 22px;
    padding: 14px;
  }

  /* Header */
  .so-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    margin-bottom:12px;
  }
  .so-head h1{
    margin:0;
    font-size:22px;
    font-weight:950;
    letter-spacing:-0.4px;
    color: var(--a-text);
  }
  .so-head p{
    margin:6px 0 0;
    color: var(--a-muted);
    font-weight:650;
    max-width: 820px;
  }
  .so-actions{ display:flex; gap:10px; flex-wrap:wrap; }
  .so-actions .btnx{ white-space:nowrap; }


  /* Buttons */
  .btnx{
    border:1px solid var(--a-border2);
    background:#fff;
    border-radius: 12px;
    padding: 0 14px;
    height:38px;
    font-weight:900;
    cursor:pointer;
    transition:transform .08s ease, box-shadow .08s ease, background .08s ease;
    display:inline-flex; align-items:center; gap:8px;
    user-select:none;
    white-space:nowrap;
    font-size:13px;
  }
  .btnx:hover{ transform:translateY(-1px); box-shadow:0 10px 18px rgba(18,24,40,.08); }
  .btnx:active{ transform:translateY(0); box-shadow:none; }
  .btnx-primary{
    border-color: rgba(124,58,237,.40);
    background: linear-gradient(180deg, rgba(124,58,237,.18), rgba(124,58,237,.08));
  }
  .btnx-danger{
    border-color: rgba(220,38,38,.32);
    background: linear-gradient(180deg, rgba(220,38,38,.14), rgba(220,38,38,.06));
  }

  /* Card */
  .card{
    background:#fff;
    border:1px solid var(--a-border);
    border-radius: 18px;
    box-shadow: 0 10px 22px rgba(18,24,40,.06);
    overflow:hidden;
  }
  .card + .card{ margin-top: 12px; }

  .card-h{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding: 12px 14px;
    border-bottom:1px solid rgba(0,0,0,.06);
    background: linear-gradient(180deg, rgba(124,58,237,.06), rgba(124,58,237,.02));
  }
  .card-h .l{ display:flex; align-items:center; gap:10px; }
  .badge{
    width:36px; height:36px;
    border-radius: 12px;
    background: var(--a-pri-14);
    display:flex; align-items:center; justify-content:center;
    font-size:16px;
  }
  .card-h .t{ margin:0; font-size:14px; font-weight:950; color: var(--a-text); }
  .card-h .s{ margin:2px 0 0; font-size:12px; color: var(--a-muted); font-weight:650; }

  .card-b{ padding: 12px 14px 14px; }

  /* Fields */
  .grid{
    display:grid;
    grid-template-columns: 300px 150px 110px auto;
    gap:10px;
    align-items:end;
  }
  .fg label{
    display:block;
    font-size:11px;
    font-weight:950;
    color:#4b5563;
    margin:0 0 6px;
  }
  .fg input, .fg select{
    width:100%;
    height:38px;
    padding: 0 10px;
    border-radius: 12px;
    border:1px solid rgba(0,0,0,.12);
    background:#fff;
    outline:none;
    font-weight:750;
    color: var(--a-text);
    font-size:13px;
  }
  .fg input::placeholder{ color: rgba(107,114,128,.9); font-weight:650; }
  .fg input:focus, .fg select:focus{
    border-color: rgba(124,58,237,.55);
    box-shadow: 0 0 0 4px rgba(124,58,237,.12);
  }

  /* Keep top fields from visually stretching the layout */
  .fg select,
  .fg input{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Table area */
  .panel{
    border:1px solid var(--a-border);
    border-radius: 16px;
    overflow:hidden;
    background:#fff;
  }

  .panel-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding: 10px 12px;
    border-bottom:1px solid rgba(0,0,0,.06);
    background: rgba(17,24,39,.02);
    flex-wrap:wrap;
  }

  .search{
    display:flex; align-items:center; gap:8px;
    border:1px solid rgba(0,0,0,.10);
    background:#fff;
    border-radius: 12px;
    padding: 8px 10px;
    min-width: 320px;
    height: 38px;
  }
  .search input{
    border:none; outline:none; width:100%;
    font-weight:750; color: var(--a-text);
    font-size:13px;
    background: transparent;
  }

  .scroll{
    max-height: 420px;
    overflow:auto;
  }

  table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    font-size:13px;
  }
  thead th{
    text-align:left;
    padding: 10px 10px;
    background: rgba(124,58,237,.08);
    border-bottom:1px solid rgba(0,0,0,.08);
    color:#374151;
    font-weight:950;
    white-space:nowrap;
  }
  tbody td{
    padding: 9px 10px;
    border-bottom:1px solid rgba(0,0,0,.06);
    color: var(--a-text);
    font-weight:700;
    vertical-align:middle;
  }
  tbody tr:hover td{ background: rgba(124,58,237,.05); }
  tbody tr:last-child td{ border-bottom:none; }

  .mini{ color: var(--a-muted); font-weight:850; }
  .right{ text-align:right; }
  .center{ text-align:center; }

  /* Inline controls in cells — compact, not pill */
  .cell{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:nowrap;
  }
  .cell select, .cell input{
    height:30px;
    padding: 0 8px;
    border-radius: 10px;
    border:1px solid rgba(0,0,0,.14);
    font-weight:800;
    font-size:12px;
    background:#fff;
  }
  .w-day{ width:74px; }
  .w-time{ width:90px; }
  .w-room{ width:60px; }
  .room-input{
    height:30px;
    border-radius:10px;
    border:1px solid rgba(124,58,237,.35);
    background:linear-gradient(180deg,rgba(124,58,237,.08),rgba(124,58,237,.03));
    padding:0 8px;
    font-weight:800;
    color:#111827;
  }
  .room-input:focus{
    outline:none;
    border-color:rgba(124,58,237,.65);
    box-shadow:0 0 0 3px rgba(124,58,237,.15);
  }
  .w-instructor{ width:140px; }
  .instructor-input{
    height:30px;
    border-radius:10px;
    border:1px solid rgba(124,58,237,.35);
    background:linear-gradient(180deg,rgba(124,58,237,.08),rgba(124,58,237,.03));
    padding:0 8px;
    font-weight:800;
    color:#111827;
  }
  .instructor-input:focus{
    outline:none;
    border-color:rgba(124,58,237,.65);
    box-shadow:0 0 0 3px rgba(124,58,237,.15);
  }
  .w-limit{ width:40px; }
  .limit-input{
    height:30px;
    border-radius:10px;
    border:1px solid rgba(124,58,237,.35);
    background:linear-gradient(180deg,rgba(124,58,237,.10),rgba(124,58,237,.04));
    padding:0 8px;
    text-align:center;
    font-weight:900;
    color:#111827;
  }
  .limit-input:focus{
    outline:none;
    border-color:rgba(124,58,237,.65);
    box-shadow:0 0 0 3px rgba(124,58,237,.15);
  }

  .foot{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding: 10px 12px;
    border-top:1px solid rgba(0,0,0,.06);
    background: rgba(17,24,39,.02);
    flex-wrap:wrap;
  }
  .metric{
    display:flex; align-items:baseline; gap:8px;
    font-weight:950;
  }
  .metric .k{ color: var(--a-muted); font-size:12px; }
  .metric .v{ color: var(--a-pri); font-size:16px; }

  /* ASCEND Popup */
  .so-popup-backdrop{
    position:fixed;
    inset:0;
    background: rgba(17,24,39,.45);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:9999;
    padding:20px;
  }
  .so-popup-backdrop.show{ display:flex; }

  .so-popup{
    width:min(460px, 100%);
    background:#fff;
    border:1px solid rgba(124,58,237,.18);
    border-radius:20px;
    box-shadow:0 24px 60px rgba(17,24,39,.22);
    overflow:hidden;
  }
  .so-popup-head{
    display:flex;
    align-items:center;
    gap:10px;
    padding:14px 16px;
    background: linear-gradient(180deg, rgba(124,58,237,.12), rgba(124,58,237,.04));
    border-bottom:1px solid rgba(0,0,0,.06);
  }
  .so-popup-icon{
    width:38px;
    height:38px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(124,58,237,.14);
    font-size:18px;
    flex:0 0 38px;
  }
  .so-popup-title{
    margin:0;
    font-size:15px;
    font-weight:950;
    color: var(--a-text);
  }
  .so-popup-sub{
    margin:2px 0 0;
    font-size:12px;
    color: var(--a-muted);
    font-weight:700;
  }
  .so-popup-body{
    padding:16px;
  }
  .so-popup-msg{
    margin:0;
    font-size:13px;
    line-height:1.6;
    color: var(--a-text);
    font-weight:700;
    white-space:pre-line;
  }
  .so-popup-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    padding:0 16px 16px;
  }

  @media (max-width: 1100px){
    .grid{ grid-template-columns: 1fr 1fr; }
    .search{ min-width: 100%; }
    .cell{ flex-wrap:wrap; }
  }

</style>
@endpush

@section('content')
<div class="so">
  <div class="so-shell">

    <div class="so-head">
      <div>
        <h1>Subject Loading</h1>
      </div>

    
    </div>

    {{-- SECTION SETUP --}}
    <div class="card">
      <div class="card-h">
        <div class="l">
          <div class="badge">🏷️</div>
          <div>
            <p class="t">Section Setup</p>
          </div>
        </div>
</div>

      <div class="card-b">
        <div class="grid">
          <div class="fg">
            <label>Program</label>
            <select id="program_id" name="program_id">
              <option value="">— Select Program —</option>
              @foreach($programs as $program)
                <option value="{{ $program->IDProgram }}">
                  {{ $program->program_code }} - {{ $program->program_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="fg">
            <label>Year Level</label>
            <select id="year_level" name="year_level">
              <option value="">— Select Year Level —</option>
              @foreach($yearLevels as $yearLevel)
                <option value="{{ $yearLevel->IDyearlvl }}">
                  {{ $yearLevel->YearLevelName }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="fg">
            <label>Section Name</label>
            <input id="section_name" name="section_name" type="text" placeholder="Alpha" autocomplete="off" />
          </div>

          <div class="so-actions" style="justify-content:flex-start;">
            <button id="btnLoadSubjects" class="btnx btnx-primary" type="button" style="margin-left: 20px;">Save & Load Subjects</button>
          </div>
        </div>
      </div>
    </div>
    </div>

    {{-- OFFERINGS TABLE --}}
    <div class="card" style="margin-top:12px;">
      <div class="card-h">
        <div class="l">
          <div class="badge">📚</div>
          <div>
            <p class="t">Offerings & Schedule</p>
            <p class="s">Inline editing for day/time/room/limit</p>
          </div>
        </div>

    
      </div>

      <div class="card-b">
        <div class="panel">
          <div class="panel-top">
            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
              <div class="search">
                <span style="opacity:.7;">🔎</span>
                <input type="text" placeholder="Search code or title…" />
              </div>
            </div>
          </div>

          <div class="scroll">
            <table>
              <thead>
                <tr>
                  <th style="width:40px;"><input type="checkbox" /></th>
                  <th style="width:110px;">Code</th>
                  <th style="width:520px;">Title</th>
                  <th style="width:64px;" class="right">Units</th>
                  <th style="width:320px;" class="center">Schedule</th>
                  <th style="width:80px;" class = "center">Room</th>
                  <th style="width:160px;" class="center">Instructor</th>
                  <th style="width:40px;" class="center">Limit</th>
                  <th style="width:66px;" class="right">Enrolled</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
   
          <div class="foot">
            <div style="display:flex; gap:18px; flex-wrap:wrap;">
              <div class="metric"><span class="k">Subjects</span> <span class="v">9</span></div>
              <div class="metric"><span class="k">Units</span> <span class="v">26</span></div>
            </div>

             <div class="so-actions">
              <a href="{{ route('dean.index') }}" class="btnx">Back</a>
          <button id="btnSaveSchedule" class="btnx btnx-primary" type="button">Save</button>
          <button class="btnx btnx-danger" type="button">Delete Selected</button>
        </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>


<div id="soPopupBackdrop" class="so-popup-backdrop" aria-hidden="true">
  <div class="so-popup" role="dialog" aria-modal="true" aria-labelledby="soPopupTitle">
    <div class="so-popup-head">
      <div class="so-popup-icon">⚠️</div>
      <div>
        <p id="soPopupTitle" class="so-popup-title">ASCEND Notice</p>
        <p class="so-popup-sub">Sections & Offerings</p>
      </div>
    </div>
    <div class="so-popup-body">
      <p id="soPopupMessage" class="so-popup-msg"></p>
    </div>
    <div class="so-popup-actions">
      <button id="soPopupOk" type="button" class="btnx btnx-primary">Okay</button>
    </div>
  </div>
</div>

<script>
(function(){
  // Init guard
  if(window.__ascendSectionsOfferingsInit){ return; }
  window.__ascendSectionsOfferingsInit = true;

  const btnLoad = document.getElementById('btnLoadSubjects');
  const btnSave = document.getElementById('btnSaveSchedule');
  const programEl = document.getElementById('program_id');
  const yearEl = document.getElementById('year_level');
  const sectionEl = document.getElementById('section_name');

  const tbody = document.querySelector('table tbody');
  const popupBackdrop = document.getElementById('soPopupBackdrop');
  const popupMessage = document.getElementById('soPopupMessage');
  const popupOk = document.getElementById('soPopupOk');

  function toast(msg){
    if(!popupBackdrop || !popupMessage){
      alert(msg);
      return;
    }
    popupMessage.textContent = msg || 'Notice';
    popupBackdrop.classList.add('show');
    popupBackdrop.setAttribute('aria-hidden', 'false');
    setTimeout(() => popupOk?.focus(), 0);
  }

  function closeToast(){
    popupBackdrop?.classList.remove('show');
    popupBackdrop?.setAttribute('aria-hidden', 'true');
  }

  popupOk?.addEventListener('click', closeToast);
  popupBackdrop?.addEventListener('click', function(e){
    if(e.target === popupBackdrop){ closeToast(); }
  });
  document.addEventListener('input', function(e){
    if(e.target.classList.contains('limit-input')){
      e.target.value = (e.target.value || '').replace(/[^0-9]/g, '');
    }
  });

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && popupBackdrop?.classList.contains('show')){
      closeToast();
    }
  });

  async function postJson(url, payload){
    const token = '{{ csrf_token() }}';
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || ''
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(()=> ({}));
    if(!res.ok){
      const m = data?.message || ('Request failed (' + res.status + ')');
      throw new Error(m);
    }
    return data;
  }

  function renderRows(rows){
    if(!tbody) return;
    tbody.innerHTML = '';
    rows.forEach((r)=>{
      const tr = document.createElement('tr');
      tr.dataset.subjectId = (r.subject_id ?? '');
      tr.innerHTML = `
        <td><input type="checkbox" /></td>
        <td>${r.subject_code ?? ''}</td>
        <td>${r.subject_title ?? ''}</td>
        <td class="right">${r.units ?? ''}</td>
        <td>
          <div class="cell">
            <select class="w-day">
              <option value="">TBA</option>
              <option value="MW">MW</option><option value="TTH">TTH</option><option value="MWF">MWF</option><option value="Sat">Sat</option><option value="Sun">Sun</option>
            </select>
            <input class="w-time" type="time" value="${r.time_start ?? ''}" />
            <span class="mini">to</span>
            <input class="w-time" type="time" value="${r.time_end ?? ''}" />
          </div>
        </td>
        <td class="center"><input class="w-room room-input" type="text" value="${r.room ?? ''}" /></td>
        <td class="center"><input class="w-instructor instructor-input" type="text" value="${r.instructor ?? ''}" /></td>
        <td class="right"><input class="w-limit limit-input" type="text" value="${r.seat_limit ?? ''}" inputmode="numeric" pattern="[0-9]*" /></td>
        <td class="right mini">${r.enrolled_count ?? ''}</td>
      `;
      // set day after adding options
      const daySel = tr.querySelector('select.w-day');
      if(daySel && (r.day ?? '') !== ''){
        daySel.value = r.day;
      }
      tbody.appendChild(tr);
    });
  }

  btnLoad?.addEventListener('click', async function(){
    const program_id = (programEl?.value || '').trim();
    const year_level = (yearEl?.value || '').trim();
    const section_name = (sectionEl?.value || '').trim();

    if(!program_id){ toast('Select Program first.'); return; }
    if(!year_level){ toast('Select Year Level first.'); return; }
    if(!section_name){ toast('Enter Section Name.'); sectionEl?.focus(); return; }

    try{
      const data = await postJson('{{ route('dean.sections-offerings.load-subjects') }}', {
        program_id, year_level, section_name
      });
      // Expected response: { ok:true, mode:'create'|'edit', rows:[...] }
      if(!data || data.ok !== true){
        toast(data?.message || 'Load failed.');
        return;
      }
      window.__so_section_id = data.section_id || null;
      renderRows(data.rows || []);
      // Optional status cue
      if(data.mode === 'edit'){
        // editing existing schedule
        // keep simple for now
      }
    }catch(err){
      toast(err.message || 'Load failed.');
    }
  });


  btnSave?.addEventListener('click', async function(){
    const program_id = (programEl?.value || '').trim();
    const year_level = (yearEl?.value || '').trim();
    const section_name = (sectionEl?.value || '').trim();

    if(!program_id){ toast('Select Program first.'); return; }
    if(!year_level){ toast('Select Year Level first.'); return; }
    if(!section_name){ toast('Enter Section Name.'); sectionEl?.focus(); return; }

    const trs = Array.from(tbody?.querySelectorAll('tr') || []);
    if(trs.length === 0){ toast('No subjects to save. Click Load Subjects first.'); return; }

    const rows = trs.map(tr => {
      const subject_id = tr.dataset.subjectId || '';
      const day = tr.querySelector('select.w-day')?.value || '';
      const times = tr.querySelectorAll('input.w-time');
      const time_start = times[0]?.value || '';
      const time_end = times[1]?.value || '';
      const room = tr.querySelector('input.w-room')?.value || '';
      const seat_limit_raw = tr.querySelector('input.w-limit')?.value || '';
      const seat_limit = seat_limit_raw.replace(/[^0-9]/g, '');

      if (seat_limit_raw !== '' && seat_limit === '') {
        throw new Error('Limit must contain numbers only.');
      }

      if (seat_limit !== '' && parseInt(seat_limit, 10) <= 0) {
        throw new Error('Limit must be greater than 0.');
      }

      return { subject_id, day, time_start, time_end, room, seat_limit };
    });

    try{
      const data = await postJson('{{ route('dean.sections-offerings.save') }}', {
        program_id,
        year_level,
        section_name,
        section_id: window.__so_section_id || null,
        rows
      });

      if(!data || data.ok !== true){
        toast(data?.message || 'Save failed.');
        return;
      }

      window.__so_section_id = data.section_id || window.__so_section_id || null;
      toast('Saved.');
    }catch(err){
      toast(err.message || 'Save failed.');
    }
  });

})();
</script>


@endsection
