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

    <form id="subjectsForm" method="POST" style="padding:18px;">
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

        <select
          id="PrereqIDs"
          name="PrereqIDs[]"
          multiple
          style="width:100%; padding:12px 14px; border-radius:14px; border:1px solid rgba(0,0,0,.14); outline:none; font-weight:200; min-height:110px;"
        >
          @foreach($subjectOptions as $opt)
            <option value="{{ $opt->IDsubj }}">
              {{ $opt->CourseCode }} — {{ $opt->CourseDescription }}
            </option>
          @endforeach
        </select>

        <div style="color:#6b7280;font-weight:400;font-size:12px;margin-top:8px; line-height:1.3;">
          Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple prerequisites.
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
  const PrereqIDs = document.getElementById('PrereqIDs');
  function syncTotalUnits(){
    const lec = parseFloat(LectureUnits.value || '0') || 0;
    const lab = parseFloat(LabUnits.value || '0') || 0;
    // Keep to 2 decimals max, but avoid trailing .00 if integer-looking
    const total = Math.round((lec + lab) * 100) / 100;
    Units.value = total.toFixed(2).replace(/\.00$/, '');
  }

  
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
    if (PrereqIDs) { Array.from(PrereqIDs.options).forEach(o => o.selected = false); }
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
    if (PrereqIDs) { Array.from(PrereqIDs.options).forEach(o => o.selected = false); }
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
