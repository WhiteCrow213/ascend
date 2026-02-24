<div id="curriculumModal" style="
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.35);
  display:none;
  align-items:center;
  justify-content:center;
  z-index:9998;
">
  <div style="
    width:520px;
    max-width:92vw;
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
        <div id="modalTitle" style="font-weight:900;color:#4b3fd1;font-size:16px;">Add Curriculum</div>
        <div style="font-weight:700;color:#6b7280;font-size:12px;margin-top:2px;">
          Enter curriculum information below.
        </div>
      </div>

      <button type="button" onclick="closeModal()" style="
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

    <form id="curriculumForm" method="POST" style="padding:18px;">
      @csrf

      <input type="hidden" id="IDcurr" name="IDcurr">

      <label style="display:block;font-weight:900;color:#1e2142;margin-bottom:8px;">
        Curriculum Name
      </label>
      <input
        type="text"
        id="CurrName"
        name="CurrName"
        maxlength="50"
        required
        style="
          width:100%;
          padding:12px 14px;
          border-radius:14px;
          border:1px solid rgba(0,0,0,.14);
          outline:none;
          font-weight:800;
        "
        placeholder="e.g., BSIT 2026 Curriculum"
      >

      @if($errors->any())
        <div style="margin-top:12px;padding:12px;border-radius:14px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#b91c1c;font-weight:800;">
          Please check your input. The curriculum name may already exist.
        </div>
      @endif

      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;">
        <button type="button" onclick="closeModal()" style="
          padding:10px 14px;
          border-radius:14px;
          font-weight:900;
          border:1px solid rgba(91,76,230,.25);
          background:#fff;
          color:#4b3fd1;
          cursor:pointer;
        ">Cancel</button>

        <button type="submit" id="saveBtn" style="
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
  const modal = document.getElementById('curriculumModal');
  const form = document.getElementById('curriculumForm');
  const modalTitle = document.getElementById('modalTitle');
  const idField = document.getElementById('IDcurr');
  const nameField = document.getElementById('CurrName');

  const storeUrl = "{{ route('utilities.curriculum.store') }}";
  const updateUrlTemplate = "{{ route('utilities.curriculum.update', ['id' => '__ID__']) }}";

  function openAddModal(){
    modalTitle.innerText = 'Add Curriculum';
    idField.value = '';
    nameField.value = '';
    form.action = storeUrl;

    modal.style.display = 'flex';
    setTimeout(() => nameField.focus(), 50);
  }

  function openEditModal(id, name){
    modalTitle.innerText = 'Edit Curriculum';
    idField.value = id;
    nameField.value = name;

    form.action = updateUrlTemplate.replace('__ID__', id);

    modal.style.display = 'flex';
    setTimeout(() => nameField.focus(), 50);
  }

  function closeModal(){
    modal.style.display = 'none';
  }

  // Close when clicking outside the card
  modal.addEventListener('click', function(e){
    if(e.target === modal) closeModal();
  });

  // ESC closes modal
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && modal.style.display === 'flex') closeModal();
  });

  // If there are validation errors, reopen modal for convenience
  @if($errors->any())
    modal.style.display = 'flex';
  @endif
</script>