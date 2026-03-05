<div id="addSubjectModalOverlay" class="ascend-modal-overlay" style="display:none;">
<div class="ascend-modal">
        <div class="ascend-modal-header">
            <div class="ascend-modal-title">Add Subject</div>
            <button type="button" class="ascend-modal-close" id="btnCloseAddSubjectModal">×</button>
        </div>

        <div class="ascend-modal-body">
            <div style="display:flex; gap:10px; align-items:center; margin-bottom:12px;">
                <input type="text" id="addSubjectSearch" class="enf-input" placeholder="Search course code or description..." style="flex:1;">
            </div>

            <div class="ascend-modal-tablewrap">
                <table class="table ascend-table" style="table-layout:fixed; width:100%;">
                    <thead>
                        <tr>
                            <th style="width:90px;">Course Code</th>
                            <th style="width:320px;">Description</th>
                            <th style="width:30px;">Units</th>
                            <th style="width:90px;">Section</th>
                            <th style="width:50px;">Day</th>
                            <th style="width:70px;">Time</th>
                            <th>Instructor</th>
                            <th style="width:60px; text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="addSubjectResults">
                        <tr><td colspan="8" style="padding:14px; color:#6b7280;">Type to search…</td></tr>
                    </tbody>
                </table>
            </div>

            <div id="addSubjectError" style="display:none; margin-top:10px; color:#b91c1c; font-weight:600;"></div>
        </div>
    </div>
</div>

<style>
/* ASCEND modal theme */
.ascend-modal-overlay{
    position:fixed; inset:0; background:rgba(17,24,39,.55);
    display:flex; align-items:flex-start; justify-content:center;
    padding:90px 24px 24px; z-index:9999;
}
.ascend-modal{
    width:min(920px, calc(100vw - 48px));
    background:#fff; border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,.25);
    overflow:hidden;
}
.ascend-modal-header{
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px;
    background:linear-gradient(135deg, #6d28d9, #7c3aed);
    color:#fff;
}
.ascend-modal-title{ font-weight:600; letter-spacing:.2px; }
.ascend-modal-close{
    border:none; background:rgba(255,255,255,.18); color:#fff;
    width:34px; height:34px; border-radius:10px;
    font-size:22px; line-height:0; cursor:pointer;
}
.ascend-modal-close:hover{ background:rgba(255,255,255,.28); }
.ascend-modal-body{ padding:14px 16px 16px; }
.ascend-modal-tablewrap{ max-height:420px; overflow:auto; border:1px solid #eef2ff; border-radius:12px; }
.ascend-table thead th{ position:sticky; top:0; background:#f8fafc; z-index:1; font-weight:600; font-size:13px; }
.ascend-table td{ font-weight:400; font-size:13px; }


.enf-btn-sm{ height:30px; padding:6px 10px; border-radius:10px; font-size:12px; }
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
    if(window.__ascendAddSubjectModalInit){ return; }
    window.__ascendAddSubjectModalInit = true;
    const overlay = document.getElementById('addSubjectModalOverlay');
    const btnClose = document.getElementById('btnCloseAddSubjectModal');
    const input = document.getElementById('addSubjectSearch');
    const tbody = document.getElementById('addSubjectResults');
    const errorBox = document.getElementById('addSubjectError');

    if(!overlay || !btnClose || !input || !tbody){ return; }

    const enrollmentId = {{ $enrollment->enrollment_id ?? 0 }};
    const searchUrl = "{{ route('admission.enrollment.offerings.search', ['enrollmentId' => $enrollment->enrollment_id ?? 0]) }}";
    const addUrl = "{{ route('admission.enrollment.offerings.add', ['enrollmentId' => $enrollment->enrollment_id ?? 0]) }}";
    const csrf = "{{ csrf_token() }}";

    function openModal(){
        overlay.style.display = 'flex';
        errorBox.style.display = 'none';
        errorBox.textContent = '';
        input.value = '';
        tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">Type to search…</td></tr>';
        setTimeout(()=>input.focus(), 50);
    }
    // ASCEND: expose opener for fallback scripts
    window.AscendOpenAddSubjectModal = openModal;



    // ✅ OPEN MODAL (delegated so it works even if the button renders later)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#btnOpenAddSubjectModal, .btnOpenAddSubjectModal, [data-open-addsubject="1"]');
        if(!btn) return;
        e.preventDefault();
        openModal();
    });

    function closeModal(){
        overlay.style.display = 'none';
    }
    btnClose.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e)=>{ if(e.target === overlay) closeModal(); });

    let t = null;
    input.addEventListener('input', function(){
        const q = input.value.trim();
        if(t) clearTimeout(t);
        t = setTimeout(()=>runSearch(q), 250);
    });

    async function runSearch(q){
        errorBox.style.display = 'none';
        errorBox.textContent = '';
        if(q.length === 0){
            tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">Type to search…</td></tr>';
            return;
        }
        tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">Searching…</td></tr>';

        try{
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', q);
            const res = await fetch(url.toString(), { headers: { 'Accept':'application/json' }});
            const json = await res.json();
            if(!json.ok){
                tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">No results.</td></tr>';
                if(json.message){
                    errorBox.textContent = json.message;
                    errorBox.style.display = 'block';
                }
                return;
            }
            const rows = json.data || [];
            if(rows.length === 0){
                tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">No results.</td></tr>';
                return;
            }
            tbody.innerHTML = rows.map(r => `
                <tr>
                    <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:500;">${escapeHtml(r.CourseCode || r.course_code || '')}</td>
                    <td style="overflow:hidden; text-overflow:ellipsis;">${escapeHtml(r.CourseDescription || r.description || '')}</td>
                    <td style="white-space:nowrap;">${escapeHtml(String((r.Units ?? r.units) ?? ''))}</td>
                    <td style="white-space:nowrap;">${escapeHtml(r.Section || r.section || '—')}</td>
                    <td style="white-space:nowrap;">${escapeHtml(r.Day || r.day || '—')}</td>
                    <td style="white-space:nowrap;">${escapeHtml(r.Time || r.time || '—')}</td>
                    <td style="overflow:hidden; text-overflow:ellipsis;">${escapeHtml(r.Instructor || r.instructor || '—')}</td>
                    <td style="text-align:right;">
                        <button type="button" class="enf-btn enf-btn-primary enf-btn-sm" data-id="${r.IDsubj ?? r.idsubj ?? r.id ?? ''}">Add</button>
                    </td>
                </tr>
            `).join('');
        }catch(err){
            tbody.innerHTML = '<tr><td colspan="8" style="padding:14px; color:#6b7280;">Error loading results.</td></tr>';
        }
    }

    tbody.addEventListener('click', async function(e){
        const btn = e.target.closest('button[data-id]');
        if(!btn) return;
        const id = btn.getAttribute('data-id');
        btn.disabled = true;
        btn.textContent = 'Adding…';

        errorBox.style.display = 'none';
        errorBox.textContent = '';

        try{
            const fd = new FormData();
            // send multiple common keys so controller can accept without breaking
            fd.append('IDsubj', id);
            fd.append('idsubj', id);
            fd.append('IDSubj', id);
            fd.append('subject_id', id);

            const res = await fetch(addUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: fd
            });

            // Controller returns redirect on error/success; but we accept JSON here too.
            // If we get non-OK, reload to show flash message.
            if(!res.ok){
                window.location.reload();
                return;
            }

            // If backend returns HTML/redirect, just reload to reflect changes.
            const ct = (res.headers.get('content-type') || '').toLowerCase();
            if(res.redirected || !ct.includes('application/json')){
                window.location.reload();
                return;
            }

            const json = await res.json().catch(()=>null);
            if(json && json.ok === false){
                window.location.reload();
                return;
            }

            window.location.reload();
        }catch(err){
            errorBox.textContent = 'Failed to add subject. Please try again.';
            errorBox.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Add';
        }
    });

    function escapeHtml(str){
        return String(str).replace(/[&<>"']/g, (m)=>({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
        }[m]));
    }
})();
</script>