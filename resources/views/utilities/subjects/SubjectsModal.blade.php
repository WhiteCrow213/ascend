<div id="subjectsModal" style="
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.35);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:9998;
">
  <div style="
    width:650px;
    max-width:95vw;
    background:#fff;
    border-radius:18px;
    box-shadow:0 18px 40px rgba(0,0,0,.22);
    overflow:hidden;
  ">

    <div style="
      padding:16px 18px;
      background: linear-gradient(135deg, rgba(91,76,230,.12), rgba(122,95,255,.08));
      border-bottom:1px solid rgba(0,0,0,.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    ">
      <div>
        <div id="subjectsModalTitle" style="font-weight:900;color:#4b3fd1;font-size:16px;">Add Subject</div>
        <div style="font-weight:700;color:#6b7280;font-size:12px;margin-top:2px;">
          Enter subject information below.
        </div>
      </div>

      <button type="button" onclick="closeSubjectsModal()" style="
        border:none;
        background:#fff;
        border-radius:12px;
        padding:6px 10px;
        font-weight:900;
        cursor:pointer;
        border:1px solid rgba(91,76,230,.25);
        color:#4b3fd1;
      ">✕</button>
    </div>

    <form id="subjectsForm" method="POST" action="{{ route('utilities.subjects.store') }}" style="padding:18px; padding-bottom:54px;">
      @csrf

      <input type="hidden" id="IDsubj" name="IDsubj">

      <!-- Row 1: Code + Description -->
      <div style="display:grid; grid-template-columns: 200px 1fr; gap:24px; margin-bottom:20px;">
        <div>
          <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">Course Code</label>
          <input
            type="text"
            id="CourseCode"
            name="CourseCode"
            maxlength="20"
            required
            style="width:50%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"
           
          >
        </div>

        <div>
          <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">Course Description</label>
          <input
            type="text"
            id="CourseDescription"
            name="CourseDescription"
            maxlength="255"
            required
            style="width:80%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"

          >
        </div>
      </div>

      <!-- Row 2: Units -->
      <div style="display:grid; grid-template-columns: 160px 140px 140px; gap:24px; margin-bottom:10px;">
        <div>
          <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">Total Units</label>
          <input
            type="number"
            id="Units"
            name="Units"
            min="0"
            step="1"
            readonly required
            style="width:40%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"

          >
        </div>

        <div>
          <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">Lecture Units</label>
          <input
            type="number"
            id="LectureUnits"
            name="LectureUnits"
            min="0"
            step="0.25"
            required
            style="width:40%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"

          >
        </div>

        <div>
          <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">Lab Units</label>
          <input
            type="number"
            id="LabUnits"
            name="LabUnits"
            min="0"
            step="0.25"
            required
            style="width:40%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"

          >
        </div>
      </div>

      <div style="color:#6b7280;font-weight:700;font-size:12px;margin:0 0 18px 0;">
        Total Units is automatically computed as Lecture + Lab.
      </div>

      <!-- Row 3: Prerequisites -->
      <div style="margin-top:6px;">
        <label style="display:block;font-weight:600;color:#1e2142;margin-bottom:8px;">Prerequisite Subject(s)</label>

        <!-- Active search (multi-select) -->
        <div style="position:relative;">
          <input
            type="text"
            id="prereqSearch"
            autocomplete="off"
            placeholder="Type to search subjects…"
            style="width:90%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200;"
          >

          <div id="prereqResultsWrap" style="position:relative; display:none;">
            <div id="prereqResults"
                 style="position:absolute; left:0; right:0; top:8px; background:#fff; border:1px solid rgba(0,0,0,.12);
                        border-radius:14px; box-shadow:0 14px 30px rgba(0,0,0,.12); max: height 560px; overflow:auto; z-index:10000;">
            </div>
          </div>
        </div>

        <div id="prereqChips" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;"></div>
        <div id="prereqHiddenInputs"></div>

        <div style="color:#6b7280;font-weight:400;font-size:12px;margin-top:8px; line-height:1.3;">
          Search and click to add multiple prerequisites. Click ✕ to remove a selected prerequisite.
        </div>
      </div>

      @if($errors->any())
        <div style="margin-top:12px;padding:12px;border-radius:14px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#b91c1c;font-weight:200;">
          Please check your input. The course code may already exist.
        </div>
      @endif

      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
        <button type="button" onclick="closeSubjectsModal()" style="
          padding:10px 14px;
          border-radius:14px;
          font-weight:900;
          border:1px solid rgba(91,76,230,.25);
          background:#fff;
          color:#4b3fd1;
          cursor:pointer;
        ">Cancel</button>

        <button type="submit" style="
          padding:10px 14px;
          border-radius:14px;
          font-weight:900;
          border:none;
          cursor:pointer;
          background: linear-gradient(135deg,#5B4CE6 0%, #7A5FFF 100%);
          color:#fff;
          box-shadow: 0 10px 22px rgba(91,76,230,.35);
        ">Save</button>
      </div>
    </form>

  </div>
</div>

<script>
  const subjectsModal = document.getElementById('subjectsModal');
  const subjectsForm = document.getElementById('subjectsForm');
  const subjectsModalTitle = document.getElementById('subjectsModalTitle');

  const IDsubj = document.getElementById('IDsubj');
  const CourseCode = document.getElementById('CourseCode');
  const CourseDescription = document.getElementById('CourseDescription');
  const Units = document.getElementById('Units');
  const LectureUnits = document.getElementById('LectureUnits');
  const LabUnits = document.getElementById('LabUnits');
  // Prerequisites (Active Search Multi-select)
  const prereqSearch = document.getElementById('prereqSearch');
  const prereqResultsWrap = document.getElementById('prereqResultsWrap');
  const prereqResults = document.getElementById('prereqResults');
  const prereqChips = document.getElementById('prereqChips');
  const prereqHiddenInputs = document.getElementById('prereqHiddenInputs');

  // Dataset from server (same list used previously in the <select>)
  const SUBJECT_OPTIONS = @json($subjectOptions ?? []);

  // internal selection state (id -> {id, code, desc})
  const selectedPrereqs = new Map();

  function syncTotalUnits(){
    const lec = parseFloat(LectureUnits.value || '0') || 0;
    const lab = parseFloat(LabUnits.value || '0') || 0;
    // Keep to 2 decimals max, but avoid trailing .00 if integer-looking
    const total = Math.round((lec + lab) * 100) / 100;
    Units.value = total.toFixed(2).replace(/\.00$/, '');
  }


  function esc(str){
    return String(str ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    })[m]);
  }

  function norm(x){ return String(x ?? '').toLowerCase(); }

  function rebuildPrereqHiddenInputs(){
    if(!prereqHiddenInputs) return;
    prereqHiddenInputs.innerHTML = '';
    Array.from(selectedPrereqs.values()).forEach(s => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'PrereqIDs[]';
      input.value = s.id;
      prereqHiddenInputs.appendChild(input);
    });
  }

  function renderPrereqChips(){
    if(!prereqChips) return;
    if(selectedPrereqs.size === 0){
      prereqChips.innerHTML = '';
      return;
    }

    prereqChips.innerHTML = Array.from(selectedPrereqs.values()).map(s => {
      const label = `${esc(s.code)}${s.desc ? ' — ' + esc(s.desc) : ''}`;
      return `
        <span data-id="${esc(s.id)}" style="
          display:inline-flex;
          align-items:center;
          gap:8px;
          padding:8px 10px;
          border-radius:999px;
          background: rgba(91,76,230,.08);
          border: 1px solid rgba(91,76,230,.18);
          color:#1e2142;
          font-weight:200;
          font-size:12.5px;
          max-width:100%;
        ">
          <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:420px;">${label}</span>
          <button type="button" data-remove="${esc(s.id)}" style="
            border:none;
            background:#fff;
            border-radius:999px;
            width:22px;
            height:22px;
            cursor:pointer;
            border:1px solid rgba(91,76,230,.20);
            color:#4b3fd1;
            font-weight:900;
            line-height:1;
            display:inline-flex;
            align-items:center;
            justify-content:center;
          ">✕</button>
        </span>
      `;
    }).join('');
  }

  function addPrereq(subj){
    const id = String(subj.IDsubj ?? subj.id ?? '').trim();
    if(!id) return;

    // Prevent selecting self when editing
    if(IDsubj && IDsubj.value && String(IDsubj.value) === id){
      return; // quietly ignore
    }

    if(selectedPrereqs.has(id)) return;

    selectedPrereqs.set(id, {
      id,
      code: String(subj.CourseCode ?? subj.code ?? '').trim(),
      desc: String(subj.CourseDescription ?? subj.desc ?? '').trim(),
    });

    rebuildPrereqHiddenInputs();
    renderPrereqChips();
  }

  function removePrereq(id){
    selectedPrereqs.delete(String(id));
    rebuildPrereqHiddenInputs();
    renderPrereqChips();
  }

  function clearPrereqs(){
    selectedPrereqs.clear();
    rebuildPrereqHiddenInputs();
    renderPrereqChips();
    if(prereqSearch) prereqSearch.value = '';
    hidePrereqResults();
  }

  function showPrereqResults(items){
    if(!prereqResultsWrap || !prereqResults) return;

    if(items.length === 0){
      prereqResults.innerHTML = '<div style="padding:10px 12px; color:rgba(31,33,66,.65); font-size:13px;">No matches.</div>';
    } else {
      prereqResults.innerHTML = items.map(s => {
        const id = s.IDsubj ?? s.id ?? '';
        const code = s.CourseCode ?? s.code ?? '';
        const desc = s.CourseDescription ?? s.desc ?? '';
        const units = s.Units ?? s.units ?? '';
        const disabled = (IDsubj && IDsubj.value && String(IDsubj.value) === String(id)) ? 'data-disabled="1"' : '';
        return `
          <button type="button" ${disabled} data-id="${esc(id)}"
            style="width:100%; text-align:left; padding:10px 12px; border:0; background:#fff; cursor:pointer;">
            <div style="font-weight:900; color:#1e2142; font-size:13px;">${esc(code)}</div>
            <div style="font-size:12px; color:rgba(31,33,66,.75); font-weight:200;">
              ${esc(desc)}${units !== '' ? ' • ' + esc(units) + 'u' : ''}
            </div>
          </button>
        `;
      }).join('');

      Array.from(prereqResults.querySelectorAll('button')).forEach(btn => {
        const isDisabled = btn.getAttribute('data-disabled') === '1';
        btn.addEventListener('mouseenter', () => btn.style.background = isDisabled ? 'rgba(239,68,68,.06)' : 'rgba(91,76,230,.08)');
        btn.addEventListener('mouseleave', () => btn.style.background = '#fff');
        if(isDisabled){
          btn.style.cursor = 'not-allowed';
          btn.title = 'Cannot set subject as its own prerequisite.';
        }
      });
    }

    prereqResultsWrap.style.display = 'block';
  }

  function hidePrereqResults(){
    if(prereqResultsWrap) prereqResultsWrap.style.display = 'none';
  }

  // Wire active search
  (function initPrereqSearch(){
    if(!prereqSearch || !prereqResultsWrap || !prereqResults) return;

    let t = null;

    prereqSearch.addEventListener('input', function(){
      const q = norm(prereqSearch.value).trim();
      clearTimeout(t);
      t = setTimeout(() => {
        const matches = SUBJECT_OPTIONS
          .filter(s => {
            const code = norm(s.CourseCode ?? s.code);
            const desc = norm(s.CourseDescription ?? s.desc);
            return (code + ' ' + desc).includes(q);
          })
          .slice(0, 30);

        // If input is empty, still show top results to avoid scrolling fatigue
        if(!q){
          showPrereqResults(SUBJECT_OPTIONS.slice(0, 30));
          return;
        }

        showPrereqResults(matches);
      }, 100);
    });

    prereqSearch.addEventListener('focus', function(){
      // Show top results on focus
      showPrereqResults(SUBJECT_OPTIONS.slice(0, 30));
    });

    prereqResults.addEventListener('click', function(e){
      const btn = e.target.closest('button[data-id]');
      if(!btn) return;
      if(btn.getAttribute('data-disabled') === '1') return;

      const id = btn.getAttribute('data-id');
      const subj = SUBJECT_OPTIONS.find(s => String(s.IDsubj ?? s.id) === String(id));
      if(subj){
        addPrereq(subj);
        prereqSearch.value = '';
        prereqSearch.focus();
      }
      hidePrereqResults();
    });

    prereqChips && prereqChips.addEventListener('click', function(e){
      const rm = e.target.closest('button[data-remove]');
      if(!rm) return;
      removePrereq(rm.getAttribute('data-remove'));
    });

    document.addEventListener('click', function(e){
      if(e.target === prereqSearch || prereqResultsWrap.contains(e.target)) return;
      hidePrereqResults();
    });
  })();

  
  function enforcePositiveDecimal(input) {
    input.addEventListener('input', function () {
      let value = input.value ?? '';

      // Remove anything that is not digit or dot
      value = value.replace(/[^0-9.]/g, '');

      // Prevent multiple dots
      const parts = value.split('.');
      if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
      }

      input.value = value;
    });
  }

  // Prevent negative / non-numeric input
  enforcePositiveDecimal(LectureUnits);
  enforcePositiveDecimal(LabUnits);


  // Keep Total Units always in sync
  LectureUnits.addEventListener('input', syncTotalUnits);
  LabUnits.addEventListener('input', syncTotalUnits);

  // Enforce match on submit (extra safety)
  subjectsForm.addEventListener('submit', function(e){
    syncTotalUnits(); // ensure computed
    const total = parseFloat(Units.value || '0') || 0;
    const lec = parseFloat(LectureUnits.value || '0') || 0;
    const lab = parseFloat(LabUnits.value || '0') || 0;
    const sum = Math.round((lec + lab) * 100) / 100;

    if (Math.abs(sum - total) > 0.001) {
      e.preventDefault();
      alert('Total Units must equal Lecture Units + Lab Units.');
    }
  });


  const storeUrl = "{{ route('utilities.subjects.store') }}";
  const updateUrlTemplate = "{{ route('utilities.subjects.update', ['id' => '__ID__']) }}";

  function openAddModal(){
    subjectsModalTitle.innerText = 'Add Subject';
    IDsubj.value = '';
    CourseCode.value = '';
    CourseDescription.value = '';
    Units.value = '';
    LectureUnits.value = '0';
    LabUnits.value = '0';
    // clear prerequisites selection
    clearPrereqs();
syncTotalUnits();
    subjectsForm.action = storeUrl;

    subjectsModal.style.display = 'flex';
    setTimeout(() => CourseCode.focus(), 50);
  }

  function openEditModal(id, code, desc, units, lec, lab){
    subjectsModalTitle.innerText = 'Edit Subject';
    IDsubj.value = id;
    CourseCode.value = code ?? '';
    CourseDescription.value = desc ?? '';
    Units.value = units ?? 0;
    LectureUnits.value = lec ?? 0;
    LabUnits.value = lab ?? 0;
    // prerequisites will be loaded in a later step (DB mapping)
    clearPrereqs();
syncTotalUnits();

    subjectsForm.action = updateUrlTemplate.replace('__ID__', id);

    subjectsModal.style.display = 'flex';
    setTimeout(() => CourseCode.focus(), 50);
  }

  function closeSubjectsModal(){
    subjectsModal.style.display = 'none';
  }

  subjectsModal.addEventListener('click', function(e){
    if(e.target === subjectsModal) closeSubjectsModal();
  });

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && subjectsModal.style.display === 'flex') closeSubjectsModal();
  });

  @if($errors->any())
    subjectsModal.style.display = 'flex';
  @endif
</script>
