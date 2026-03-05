@extends('layouts.app')

@section('title', 'Sections & Offerings')

@push('styles')
<style>
  /* =========================================================
     ASCEND — Sections & Offerings (Revamp)
     Goals:
     - Cleaner hierarchy (less whitespace, better alignment)
     - Compact table inputs (no giant pill look)
     - Proper density to avoid "ugly AF" spaciousness
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

  /* Buttons */
  .btnx{
    border:1px solid var(--a-border2);
    background:#fff;
    border-radius: 12px;
    padding: 8px 11px;
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
    grid-template-columns: 1.2fr .55fr .65fr .9fr 1fr;
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

  .row{
    margin-top:10px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
  }
  .chips{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
  .chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 10px;
    border-radius: 12px;
    border:1px solid rgba(0,0,0,.10);
    background: rgba(17,24,39,.02);
    font-weight:900;
    color: var(--a-text);
    font-size:12px;
  }
  .chip span{ color: var(--a-muted); font-weight:950; }

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
  }
  .tag{
    display:inline-flex; align-items:center; gap:8px;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(124,58,237,.10);
    border:1px solid rgba(124,58,237,.18);
    font-weight:950;
    color: #3b2a7a;
    font-size:12px;
    height: 38px;
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
  }
  .w-day{ width:74px; }
  .w-time{ width:108px; }
  .w-room{ width:92px; }
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
  .w-limit{ width:84px; }
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

  .note{
    margin: 10px 2px 0;
    color: var(--a-muted);
    font-weight:650;
    font-size:12px;
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
        <h1>Scheduling</h1>
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

        <div class="so-actions">
          <button class="btnx btnx-primary" type="button">➕ New Section</button>
        </div>
      </div>

      <div class="card-b">
        <div class="grid">
          <div class="fg">
            <label>Program</label>
            <select>
              <option>— Select Program —</option>
              <option>BEED</option>
              <option>BSIT</option>
              <option>BSCRIM</option>
            </select>
          </div>

          <div class="fg">
            <label>Year</label>
            <select>
              <option>—</option>
              <option>1</option><option>2</option><option>3</option><option>4</option>
            </select>
          </div>

          <div class="fg">
            <label>Term</label>
            <select>
              <option>— Active —</option>
              <option>1st Sem</option>
              <option>2nd Sem</option>
              <option>Summer</option>
            </select>
          </div>

          <div class="fg">
            <label>Section (existing)</label>
            <select>
              <option>— Select —</option>
              <option>Alpha</option>
              <option>Beta</option>
              <option>Gamma</option>
            </select>
          </div>

          <div class="so-actions">
            <button class="btnx" type="button">Load Subjects</button>
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
                  <th style="width:320px;">Schedule</th>
                  <th style="width:80px;" class = "center">Room</th>
                  <th style="width:40px;" class="center">Limit</th>
                  <th style="width:66px;" class="right">Enrolled</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox" /></td>
                  <td>GE 109</td>
                  <td>Living in the IT Era</td>
                  <td class="right">3</td>
                  <td>
                    <div class="cell">
                      <select class="w-day">
                        <option>MW</option><option selected>TTH</option><option>MWF</option><option>Sat</option>
                      </select>
                      <input class="w-time" type="time" value="09:00" />
                      <span class="mini">to</span>
                      <input class="w-time" type="time" value="10:30" />
                    </div>
                  </td>
                  <td><input class="w-room room-input" type="text" value="IT LAB" /></td>
                  <td class="right"><input class="w-limit limit-input" type="number" min="1" value="60" /></td>
                  <td class="right mini">52</td>
                </tr>

                <tr>
                  <td><input type="checkbox" /></td>
                  <td>GE 108</td>
                  <td>Ethics</td>
                  <td class="right">3</td>
                  <td>
                    <div class="cell">
                      <select class="w-day">
                        <option selected>MW</option><option>TTH</option><option>MWF</option><option>Sat</option>
                      </select>
                      <input class="w-time" type="time" value="07:30" />
                      <span class="mini">to</span>
                      <input class="w-time" type="time" value="09:00" />
                    </div>
                  </td>
                  <td><input class="w-room room-input" type="text" value="12" /></td>
                  <td class="right"><input class="w-limit limit-input" type="number" min="1" value="50" /></td>
                  <td class="right mini">50</td>
                </tr>

                <tr>
                  <td><input type="checkbox" /></td>
                  <td>NSTP 102</td>
                  <td>Literacy Training Service 2</td>
                  <td class="right">3</td>
                  <td>
                    <div class="cell">
                      <select class="w-day">
                        <option>MW</option><option selected>TTH</option><option>MWF</option><option>Sat</option>
                      </select>
                      <input class="w-time" type="time" value="13:00" />
                      <span class="mini">to</span>
                      <input class="w-time" type="time" value="14:30" />
                    </div>
                  </td>
                  <td><input class="w-room room-input" type="text" value="10" /></td>
                  <td class="right"><input class="w-limit limit-input" type="number" min="1" value="55" /></td>
                  <td class="right mini">55</td>
                </tr>

                <tr>
                  <td><input type="checkbox" /></td>
                  <td>GE 110</td>
                  <td>Gender and Society</td>
                  <td class="right">3</td>
                  <td>
                    <div class="cell">
                      <select class="w-day">
                        <option selected>MW</option><option>TTH</option><option>MWF</option><option>Sat</option>
                      </select>
                      <input class="w-time" type="time" value="14:30" />
                      <span class="mini">to</span>
                      <input class="w-time" type="time" value="16:00" />
                    </div>
                  </td>
                  <td><input class="w-room room-input" type="text" value="17" /></td>
                  <td class="right"><input class="w-limit limit-input" type="number" min="1" value="52" /></td>
                  <td class="right mini">52</td>
                </tr>
              </tbody>
            </table>
          </div>
   
          <div class="foot">
            <div style="display:flex; gap:18px; flex-wrap:wrap;">
              <div class="metric"><span class="k">Subjects</span> <span class="v">9</span></div>
              <div class="metric"><span class="k">Units</span> <span class="v">26</span></div>
            </div>

             <div class="so-actions">
          <button class="btnx btnx-danger" type="button">Delete Selected</button>
        </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
@endsection
