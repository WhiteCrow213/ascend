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
    text-decoration:none; box-sizing:border-box;
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
  .enf-col-time{ width: 120px; text-align:center; }
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
    flex-wrap: nowrap;
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
  .enf-empty{
    padding: 20px 14px;
    text-align:center;
    color: rgba(28,36,80,.68);
    font-size: 13px;
  }
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
          <div class="enf-grid-field" style="min-width:220px;">
            <div class="enf-grid-label">Search</div>
            <input
              type="text"
              id="offeringSearch"
              class="enf-grid-control"
              placeholder="Search subject / section / instructor"
              {{ $canQueryOfferings ? '' : 'disabled' }}
            >
          </div>

          <div class="enf-grid-field">
            <div class="enf-grid-label">Year Level</div>
            <select
              id="filterYearLevel"
              class="enf-grid-control"
              {{ $canQueryOfferings ? '' : 'disabled' }}
            >
              <option value="">Select year level</option>
              @foreach($yearLevelOptions as $y)
                <option value="{{ $y->id }}" {{ (string)$selectedYearLevel === (string)$y->id ? 'selected' : '' }}>
                  {{ $y->label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="enf-grid-field">
            <div class="enf-grid-label">Section</div>
            <select
              id="filterSection"
              class="enf-grid-control"
              {{ $canQueryOfferings ? '' : 'disabled' }}
            >
              <option value="">Select section</option>
            </select>
          </div>

          <button
            type="button"
            id="loadSelectedBtn"
            class="enf-btn enf-btn-primary"
            disabled
          >
            Load Selected
          </button>
        </div>
      </div>

      <div class="enf-grid-body">
        <table class="table enf-table enf-table-compact">
          <thead>
            <tr>
              <th class="enf-col-check">
                <input type="checkbox" id="selectAllOfferings" disabled aria-label="Select all" />
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
          <tbody id="offeringsGridBody">
              <tr>
                <td colspan="8" class="enf-muted">
                  Select year level and section to load offerings.
                </td>
              </tr>
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
          <button type="button" id="removeSelectedBtn" class="enf-btn" disabled>
            Remove Selected
          </button>
        </div>
      </div>

      <div class="enf-grid-body">
        <table class="table enf-table enf-table-compact">
          <thead>
            <tr>
              <th class="enf-col-check">
                <input type="checkbox" id="selectAllStudentLoad" aria-label="Select all loaded subjects" />
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
            @forelse(($studentLoadSubjects ?? []) as $l)
              <tr>
                <td class="enf-col-check">
                  <input
                    type="checkbox"
                    class="student-load-checkbox"
                    value="{{ $l->enroll_subj_id }}"
                    aria-label="Select loaded subject {{ $l->CourseCode ?? '' }}"
                  >
                </td>
                <td class="enf-code">{{ $l->CourseCode ?? '—' }}</td>
                <td class="enf-desc">{{ $l->CourseDescription ?? '—' }}</td>
                <td class="enf-col-units">{{ $l->Units ?? '—' }}</td>
                <td class="enf-muted enf-col-section">{{ $l->SectionName ?? '—' }}</td>
                <td class="enf-muted enf-col-day">{{ $l->Day ?? '—' }}</td>
                <td class="enf-muted enf-col-time">{{ $l->Time ?? '—' }}</td>
                <td class="enf-muted enf-col-instructor">{{ $l->InstructorName ?? '—' }}</td>
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
    <a class="enf-btn" href="{{ route('admission.enrollment.show', ['enrollmentId' => $enrollment->enrollment_id]) }}">Back</a>
    <button class="enf-btn enf-btn-primary" type="button" id="saveStudyLoadBtn">Save</button>
  </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    function initEnrollmentSubjectLoading() {
        const canQueryOfferings = @json($canQueryOfferings);
        const searchInput = document.getElementById('offeringSearch');
        const yearLevelSelect = document.getElementById('filterYearLevel');
        const sectionSelect = document.getElementById('filterSection');
        const offeringsGridBody = document.getElementById('offeringsGridBody');
        const selectAllOfferings = document.getElementById('selectAllOfferings');
        const loadSelectedBtn = document.getElementById('loadSelectedBtn');

        if (!yearLevelSelect || !sectionSelect || !offeringsGridBody || !selectAllOfferings || !loadSelectedBtn) {
            return;
        }

        let searchTimer = null;

        function escapeHtml(value) {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function resetOfferingsGrid(message = 'Select year level and section to load offerings.') {
            offeringsGridBody.innerHTML = `
                <tr>
                    <td colspan="8" class="enf-muted">${escapeHtml(message)}</td>
                </tr>
            `;
            selectAllOfferings.checked = false;
            selectAllOfferings.disabled = true;
            loadSelectedBtn.disabled = true;
        }

        function updateLoadSelectedButtonState() {
            const checked = document.querySelectorAll('.offering-checkbox:checked');
            loadSelectedBtn.disabled = checked.length === 0;
        }

        function bindOfferingCheckboxes() {
            const checkboxes = document.querySelectorAll('.offering-checkbox');
            const enabledCheckboxes = Array.from(checkboxes).filter(cb => !cb.disabled);

            selectAllOfferings.disabled = enabledCheckboxes.length === 0;
            selectAllOfferings.checked = false;

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const activeCheckboxes = Array.from(document.querySelectorAll('.offering-checkbox')).filter(x => !x.disabled);
                    const checkedCheckboxes = activeCheckboxes.filter(x => x.checked);

                    selectAllOfferings.checked = activeCheckboxes.length > 0 && checkedCheckboxes.length === activeCheckboxes.length;
                    updateLoadSelectedButtonState();
                });
            });

            updateLoadSelectedButtonState();
        }

        function renderOfferings(rows, allowSelection = true) {
            if (!Array.isArray(rows) || rows.length === 0) {
                resetOfferingsGrid('No section offerings found.');
                return;
            }

            offeringsGridBody.innerHTML = rows.map(row => {
                const offeringId = row.offering_id ?? '';
                const isSelectable = allowSelection && offeringId !== '';
                const sectionName = row.SectionName ?? row.section_name ?? '—';
                const day = row.Day ?? row.day_pattern ?? '—';
                const time = row.Time ?? (
                    ((row.time_start ?? '') && (row.time_end ?? ''))
                        ? `${row.time_start} - ${row.time_end}`
                        : '—'
                );
                const instructor = row.InstructorName ?? '—';

                return `
                    <tr>
                        <td class="enf-col-check">
                            <input
                                type="checkbox"
                                class="offering-checkbox"
                                value="${escapeHtml(offeringId)}"
                                ${isSelectable ? '' : 'disabled'}
                                aria-label="Select subject ${escapeHtml(row.CourseCode ?? '')}"
                            />
                        </td>
                        <td class="enf-code">${escapeHtml(row.CourseCode ?? '—')}</td>
                        <td class="enf-desc">${escapeHtml(row.CourseDescription ?? '—')}</td>
                        <td class="enf-col-units">${escapeHtml(row.Units ?? '—')}</td>
                        <td class="enf-muted enf-col-section">${escapeHtml(sectionName)}</td>
                        <td class="enf-muted enf-col-day">${escapeHtml(day)}</td>
                        <td class="enf-muted enf-col-time">${escapeHtml(time)}</td>
                        <td class="enf-muted enf-col-instructor">${escapeHtml(instructor)}</td>
                    </tr>
                `;
            }).join('');

            bindOfferingCheckboxes();
        }

        async function fetchJson(url) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok || data.ok === false) {
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }


        async function postJson(url, payload) {
            console.log('[ASCEND] postJson()', { url, payload });

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify(payload)
            });

            let data = {};
            try {
                data = await response.json();
            } catch (e) {
                data = {};
            }

            console.log('[ASCEND] postJson() response', { url, status: response.status, ok: response.ok, data });

            if (!response.ok) {
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }

        async function loadSelectedOfferings() {
            const selectedCheckboxes = Array.from(document.querySelectorAll('.offering-checkbox:checked'))
                .filter(cb => !cb.disabled);

            if (selectedCheckboxes.length === 0) {
                return;
            }

            const offeringIds = selectedCheckboxes
                .map(cb => parseInt(cb.value, 10))
                .filter(Number.isInteger);

            if (offeringIds.length === 0) {
                return;
            }

            loadSelectedBtn.disabled = true;
            loadSelectedBtn.textContent = 'Loading...';

            const url = @json(route('admission.enrollment.offerings.add', ['enrollmentId' => $enrollment->enrollment_id]));

            try {
                for (const offeringId of offeringIds) {
                    await postJson(url, { offering_id: offeringId });
                }

                window.location.reload();
            } catch (error) {
                console.error('[ASCEND] loadSelectedOfferings() failed.', error);
                alert(error.message || 'Failed to load selected subjects.');
                updateLoadSelectedButtonState();
                loadSelectedBtn.textContent = 'Load Selected';
            }
        }

        async function loadSections(yearLevel) {
            if (!yearLevel) {
                sectionSelect.innerHTML = '<option value="">Select section</option>';
                sectionSelect.disabled = true;
                resetOfferingsGrid('Select a section to load offerings.');
                return;
            }

            sectionSelect.disabled = true;
            sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
            resetOfferingsGrid('Loading sections...');

            try {
                const url = `{{ route('admission.enrollment.sections', ['enrollmentId' => $enrollment->enrollment_id]) }}?year_level=${encodeURIComponent(yearLevel)}`;
                const data = await fetchJson(url);

                sectionSelect.innerHTML = '<option value="">Select section</option>';

                if (Array.isArray(data.data) && data.data.length > 0) {
                    data.data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.section_id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });

                    sectionSelect.disabled = false;
                    resetOfferingsGrid('Select a section to load offerings.');
                } else {
                    sectionSelect.disabled = true;
                    resetOfferingsGrid('No sections found for the selected year level.');
                }
            } catch (error) {
                sectionSelect.innerHTML = '<option value="">Select section</option>';
                sectionSelect.disabled = true;
                resetOfferingsGrid(error.message || 'Failed to load sections.');
            }
        }

        async function loadOfferings(sectionId) {
            if (!sectionId) {
                resetOfferingsGrid('Select a section to load offerings.');
                return;
            }

            resetOfferingsGrid('Loading offerings...');

            try {
                const url = `{{ route('admission.enrollment.offerings', ['enrollmentId' => $enrollment->enrollment_id]) }}?section_id=${encodeURIComponent(sectionId)}`;
                const data = await fetchJson(url);
                renderOfferings(data.data, true);
            } catch (error) {
                resetOfferingsGrid(error.message || 'Failed to load offerings.');
            }
        }

        async function runSearch(q) {
            const query = (q || '').trim();

            if (query === '') {
                if (sectionSelect.value) {
                    await loadOfferings(sectionSelect.value);
                } else {
                    resetOfferingsGrid('Type in search or choose a year level and section.');
                }
                return;
            }

            resetOfferingsGrid('Searching offerings...');

            try {
                const url = `{{ route('admission.enrollment.offerings.search', ['enrollmentId' => $enrollment->enrollment_id]) }}?q=${encodeURIComponent(query)}`;
                const data = await fetchJson(url);
                renderOfferings(data.data, false);
            } catch (error) {
                resetOfferingsGrid(error.message || 'Failed to search offerings.');
            }
        }

        if (!canQueryOfferings) {
            resetOfferingsGrid('Apply Program and Year Level first.');
            return;
        }

        resetOfferingsGrid('Select a year level to begin.');

        yearLevelSelect.addEventListener('change', async function () {
            const selectedYearLevel = this.value;

            if (searchInput && searchInput.value.trim() !== '') {
                searchInput.value = '';
            }

            await loadSections(selectedYearLevel);
        });

        sectionSelect.addEventListener('change', async function () {
            if (searchInput && searchInput.value.trim() !== '') {
                searchInput.value = '';
            }

            await loadOfferings(this.value);
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(() => {
                    runSearch(this.value);
                }, 350);
            });
        }

        selectAllOfferings.addEventListener('change', function () {
            document.querySelectorAll('.offering-checkbox').forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = this.checked;
                }
            });

            updateLoadSelectedButtonState();
        });

        loadSelectedBtn.addEventListener('click', async function () {
            await loadSelectedOfferings();
        });


        const removeSelectedBtn = document.getElementById('removeSelectedBtn');
        const selectAllStudentLoad = document.getElementById('selectAllStudentLoad');
        const saveStudyLoadBtn = document.getElementById('saveStudyLoadBtn');

        function updateRemoveSelectedButtonState() {
            if (!removeSelectedBtn) return;
            const checked = document.querySelectorAll('.student-load-checkbox:checked');
            removeSelectedBtn.disabled = checked.length === 0;
        }

        function bindStudentLoadCheckboxes() {
            if (!removeSelectedBtn || !selectAllStudentLoad) {
                return;
            }

            const checkboxes = document.querySelectorAll('.student-load-checkbox');
            selectAllStudentLoad.checked = false;
            selectAllStudentLoad.disabled = checkboxes.length === 0;
            removeSelectedBtn.disabled = true;

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const all = Array.from(document.querySelectorAll('.student-load-checkbox'));
                    const checked = all.filter(x => x.checked);
                    selectAllStudentLoad.checked = all.length > 0 && checked.length === all.length;
                    updateRemoveSelectedButtonState();
                });
            });

            selectAllStudentLoad.addEventListener('change', function () {
                document.querySelectorAll('.student-load-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateRemoveSelectedButtonState();
            });

            removeSelectedBtn.addEventListener('click', async function () {
                const ids = Array.from(document.querySelectorAll('.student-load-checkbox:checked'))
                    .map(cb => parseInt(cb.value, 10))
                    .filter(Number.isInteger);

                if (ids.length === 0) {
                    return;
                }

                removeSelectedBtn.disabled = true;
                removeSelectedBtn.textContent = 'Removing...';

                const url = @json(route('admission.enrollment.removeSelected', ['enrollmentId' => $enrollment->enrollment_id]));

                try {
                    await postJson(url, { ids: ids });
                    window.location.reload();
                } catch (error) {
                    alert(error.message || 'Failed to remove selected subjects.');
                    removeSelectedBtn.textContent = 'Remove Selected';
                    updateRemoveSelectedButtonState();
                }
            });
        }

        bindStudentLoadCheckboxes();

        if (saveStudyLoadBtn) {
            saveStudyLoadBtn.addEventListener('click', async function () {
                saveStudyLoadBtn.disabled = true;
                saveStudyLoadBtn.textContent = 'Saving...';

                const url = @json(route('admission.enrollment.saveStudyLoad', ['enrollmentId' => $enrollment->enrollment_id]));

                try {
                    await postJson(url, {});
                    window.location.reload();
                } catch (error) {
                    alert(error.message || 'Failed to save student load.');
                    saveStudyLoadBtn.textContent = 'Save';
                    saveStudyLoadBtn.disabled = false;
                }
            });
        }

        if (yearLevelSelect.value) {
            loadSections(yearLevelSelect.value);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEnrollmentSubjectLoading);
    } else {
        initEnrollmentSubjectLoading();
    }
})();
</script>
@endpush

@include('admission.enrollment.EnrollmentModal')