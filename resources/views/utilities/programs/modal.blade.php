<div id="programModal" style="display:none; position:fixed; inset:0; z-index:9999;">
    <div style="position:absolute; inset:0; background:rgba(20,20,40,.5);" onclick="closeModal()"></div>

    <div style="
        position:relative;
        width:520px;
        margin:100px auto;
        background:#fff;
        border-radius:18px;
        box-shadow:0 24px 60px rgba(20,20,40,.2);
        overflow:hidden;
    ">

        <div style="
            padding:18px;
            background:linear-gradient(135deg,#5B4CE6 0%, #7600bc 100%);
            color:#fff;
            font-weight:900;
            font-size:16px;
        ">
            Program
        </div>

        <form id="programForm" method="POST" style="padding:20px;">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="font-weight:800; font-size:12px;">Program Code</label>
                <input type="text" name="program_code" id="program_code"
                       style="width:100%; padding:10px; border-radius:12px; border:1px solid #ddd; margin-top:6px;"
                       required>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-weight:800; font-size:12px;">Program Name</label>
                <input type="text" name="program_name" id="program_name"
                       style="width:100%; padding:10px; border-radius:12px; border:1px solid #ddd; margin-top:6px;"
                       required>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-weight:800; font-size:12px;">College</label>
                <select name="collegeID" id="collegeID" required
                        style="width:100%; padding:10px; border-radius:12px; border:1px solid #ddd; margin-top:6px; background:#fff;">
                    <option value="">— Select College —</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->collegeID }}">
                            {{ $college->college_code }} — {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>
                <div style="margin-top:6px; font-size:12px; color:#6b7280; font-weight:700;">
                    Associates this program with a college (tbl_colleges).
                </div>
            </div>


            <div style="margin-bottom:20px;">
                <label style="font-weight:800; font-size:12px;">Default Curriculum</label>
                <select name="IDcurr" id="IDcurr"
                        style="width:100%; padding:10px; border-radius:12px; border:1px solid #ddd; margin-top:6px; background:#fff;">
                    <option value="">— None —</option>
                    @foreach($curriculums as $curr)
                        <option value="{{ $curr->IDcurr }}">{{ $curr->CurrName }}</option>
                    @endforeach
                </select>
                <div style="margin-top:6px; font-size:12px; color:#6b7280; font-weight:700;">
                    Used as default for new students. Student curriculum can still be overridden during enrollment.
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModal()"
                        style="padding:8px 12px; border-radius:12px; border:1px solid #ddd; background:#fff; font-weight:800;">
                    Cancel
                </button>

                <button type="submit"
                        style="padding:8px 14px; border-radius:12px; border:none; background:#5B4CE6; color:#fff; font-weight:900;">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const updateUrlTemplate = @json(route('utilities.programs.update', ['id' => '__ID__']));

    function openAddModal(){
        document.getElementById('programForm').action = "{{ route('utilities.programs.store') }}";
        document.getElementById('program_code').value='';
        document.getElementById('program_name').value='';
        if (document.getElementById('collegeID')) document.getElementById('collegeID').value='';
        document.getElementById('IDcurr').value='';
        document.getElementById('programModal').style.display='block';
    }

    function openEditModal(id,code,name,collegeId,currId){
        document.getElementById('programForm').action = updateUrlTemplate.replace('__ID__', id);
        document.getElementById('program_code').value = code || '';
        document.getElementById('program_name').value = name || '';
        if (document.getElementById('collegeID')) {
            document.getElementById('collegeID').value = (collegeId === null || collegeId === undefined) ? '' : String(collegeId);
        }
        document.getElementById('IDcurr').value = (currId === null || currId === undefined) ? '' : String(currId);
        document.getElementById('programModal').style.display='block';
    }

    function closeModal(){
        document.getElementById('programModal').style.display='none';
    }
</script>
