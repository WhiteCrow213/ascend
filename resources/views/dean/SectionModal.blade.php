{{--
  File: resources/views/dean/SectionModal.blade.php
  Purpose: Add / Create Section modal (UI only for now)
  Rule 3: ALWAYS USE ASCEND THEME ON EVERYTHING!
  Pattern: Same as EnrollmentModal (inline style + inline script + delegated opener)
--}}

<div id="SectionModalOverlay" class="ascend-modal-overlay" style="display:none;">
  <div class="ascend-modal" style="width:min(560px, calc(100vw - 48px));">
    <div class="ascend-modal-header">
      <div class="ascend-modal-title">Create Section</div>
      <button type="button" class="ascend-modal-close" id="btnCloseSectionModal">×</button>
    </div>

    <div class="ascend-modal-body">
      <div style="display:flex; flex-direction:column; gap:8px;">
        <label for="SectionModalName" style="font-size:12px; font-weight:700; color:#374151;">Section Name</label>
        <input
          type="text"
          id="SectionModalName"
          class="enf-input"
          placeholder="Alpha"
          autocomplete="off"
        />
        <div style="font-size:11px; color:#6b7280; font-weight:600;">
          Tip: keep it short. Backend save comes next.
        </div>
        <div id="SectionModalError" style="display:none; margin-top:10px; color:#b91c1c; font-weight:700;"></div>
      </div>
    </div>

    <div style="padding:12px 16px 16px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #eef2ff; background:#fafafa;">
      <button type="button" class="btnx" id="btnCancelSectionModal">Cancel</button>
      <button type="button" class="btnx btnx-primary" id="btnCreateSectionModal">Create</button>
    </div>
  </div>
</div>

<style>
/* Reuse the same ASCEND modal engine from EnrollmentModal */
.ascend-modal-overlay{
  position:fixed; inset:0; background:rgba(17,24,39,.55);
  display:flex; align-items:flex-start; justify-content:center;
  padding:90px 24px 24px; z-index:9999;
}
.ascend-modal{
  background:#fff; border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,.25);
  overflow:hidden;
}
.ascend-modal-header{
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 16px;
  background:linear-gradient(135deg, #6d28d9, #7c3aed);
  color:#fff;
}
.ascend-modal-title{ font-weight:700; letter-spacing:.2px; }
.ascend-modal-close{
  border:none; background:rgba(255,255,255,.18); color:#fff;
  width:34px; height:34px; border-radius:10px;
  font-size:22px; line-height:0; cursor:pointer;
}
.ascend-modal-close:hover{ background:rgba(255,255,255,.28); }
.ascend-modal-body{ padding:14px 16px 16px; }

/* Input theme (same as EnrollmentModal uses) */
.enf-input{
  width:100%;
  height:38px;
  padding:8px 12px;
  border:1px solid #e5e7eb;
  border-radius:12px;
  outline:none;
  font-size:14px;
  background:#fff;
  box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
}
.enf-input:focus{
  border-color:#7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,.18);
}
</style>

<script>
(function(){
  // Prevent double-init if the partial gets included twice by mistake
  if(window.__ascendSectionModalInit){ return; }
  window.__ascendSectionModalInit = true;

  const overlay = document.getElementById('SectionModalOverlay');
  const btnClose = document.getElementById('btnCloseSectionModal');
  const btnCancel = document.getElementById('btnCancelSectionModal');
  const btnCreate = document.getElementById('btnCreateSectionModal');
  const input = document.getElementById('SectionModalName');
  const errorBox = document.getElementById('SectionModalError');

  if(!overlay || !btnClose || !btnCancel || !btnCreate || !input){ return; }

  function openModal(){
    overlay.style.display = 'flex';
    if(errorBox){
      errorBox.style.display = 'none';
      errorBox.textContent = '';
    }
    input.value = '';
    setTimeout(()=>input.focus(), 50);
  }

  function closeModal(){
    overlay.style.display = 'none';
  }

  // Expose opener for fallback usage (same idea as EnrollmentModal)
  window.AscendOpenSectionModal = openModal;

  // ✅ OPEN MODAL (delegated; works even if opener button is rendered later)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#btnNewSection, .btnNewSection, [data-open-sectionmodal="1"]');
    if(!btn) return;
    e.preventDefault();
    openModal();
  });

  // Close handlers
  btnClose.addEventListener('click', closeModal);
  btnCancel.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e)=>{ if(e.target === overlay) closeModal(); });

  // ESC
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape' && overlay.style.display === 'flex'){
      e.preventDefault();
      closeModal();
    }
  });

  // UI-only create action for now
  btnCreate.addEventListener('click', ()=>{
    const name = (input.value || '').trim();
    if(!name){
      input.focus();
      return;
    }
    // For now, just close. Backend wiring comes next.
    closeModal();
  });

  // Enter = create
  input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter'){
      e.preventDefault();
      btnCreate.click();
    }
  });
})();
</script>
