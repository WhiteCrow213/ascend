@extends('layouts.app')

@section('content')
<div class="page-wrap faculty-create-page">

    <div class="page-head ascend-card">
        <div>
            <div class="page-eyebrow">ASCEND • Faculty Module</div>
            <h1 class="page-title">Add Faculty</h1>
            <p class="page-sub">Create a faculty record with personal information, employment details, government IDs, educational background, and profile photo.</p>
        </div>

        <div class="page-head-actions">
            <a href="{{ route('faculty.index') }}" class="btn btn-light">Back to Faculty Directory</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-card alert-danger">
            <div class="alert-title">Please fix the following before saving.</div>
            <ul class="alert-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('faculty.store') }}" method="POST" enctype="multipart/form-data" class="faculty-form-wrap">
        @csrf

        <div class="form-layout">
            <div class="main-form-column">

                <div class="form-card ascend-card">
                    <div class="card-head">
                        <div>
                            <h2 class="section-title">Personal Information</h2>
                            <p class="section-sub">Primary identity and basic profile details of the faculty member.</p>
                        </div>
                    </div>

                    <div class="form-grid form-grid-12">
                        {{-- Personal Information defaults already adjusted. Later, just change the number in span-X for each field. --}}
                        <div class="form-group span-3 ">
                            <label for="FacultyLastName">Last Name <span class="required">*</span></label>
                            <input type="text" id="FacultyLastName" name="FacultyLastName" value="{{ old('FacultyLastName') }}" required>
                        </div>

                        <div class="form-group span-3">
                            <label for="FacultyFirstName">First Name <span class="required">*</span></label>
                            <input type="text" id="FacultyFirstName" name="FacultyFirstName" value="{{ old('FacultyFirstName') }}" required>
                        </div>

                        <div class="form-group span-2">
                            <label for="FacultyMiddleName">Middle Name</label>
                            <input type="text" id="FacultyMiddleName" name="FacultyMiddleName" value="{{ old('FacultyMiddleName') }}">
                        </div>

                        <div class="form-group span-2">
                            <label for="FacultySuffixName">Suffix</label>
                            <input type="text" id="FacultySuffixName" name="FacultySuffixName" value="{{ old('FacultySuffixName') }}" placeholder="Jr., Sr., III">
                        </div>

                        <div class="form-group span-3">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
                        </div>

                        <div class="form-group span-5">
                            <label for="Religion">Religion</label>
                            <input type="text" id="Religion" name="Religion" value="{{ old('Religion') }}">
                        </div>

                        <div class="form-group span-4">
                            <label for="civil_status">Civil Status</label>
                            <select id="civil_status" name="civil_status">
                                <option value="">Select civil status</option>
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            </select>


                        </div>
                    </div>
                </div>

                <div class="form-card ascend-card">
                    <div class="card-head">
                        <div>
                            <h2 class="section-title">Contact Information</h2>
                            <p class="section-sub">Core contact channels and address details.</p>
                        </div>
                    </div>

                    <div class="form-grid form-grid-2">
                        <div class="form-group span-1">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="0912-345-6789" maxlength="13">
                        </div>

                        <div class="form-group span-1">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="faculty@email.com">
                        </div>

                        <div class="form-group span-2">
                            <label for="home_address">Home Address</label>
                            <textarea id="home_address" name="home_address" rows="3" placeholder="Complete home address">{{ old('home_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-card ascend-card">
                    <div class="card-head">
                        <div>
                            <h2 class="section-title">Government IDs</h2>
                            <p class="section-sub">Professional and government account references for faculty records.</p>
                        </div>
                    </div>

                    <div class="form-grid form-grid-2">
                        <div class="form-group span-1">
                            <label for="prc_license_number">PRC License Number</label>
                            <input type="text" id="prc_license_number" name="prc_license_number" value="{{ old('prc_license_number') }}">
                        </div>

                        <div class="form-group span-1">
                            <label for="pagibig_number">Pag-IBIG Number</label>
                            <input type="text" id="pagibig_number" name="pagibig_number" value="{{ old('pagibig_number') }}">
                        </div>

                        <div class="form-group span-1">
                            <label for="tin_number">TIN Number</label>
                            <input type="text" id="tin_number" name="tin_number" value="{{ old('tin_number') }}">
                        </div>

                        <div class="form-group span-1">
                            <label for="gsis_number">GSIS Number</label>
                            <input type="text" id="gsis_number" name="gsis_number" value="{{ old('gsis_number') }}">
                        </div>
                    </div>
                </div>

                <div class="form-card ascend-card">
                    <div class="card-head">
                        <div>
                            <h2 class="section-title">Employment Information</h2>
                            <p class="section-sub">Employment classification to support future instructor assignment and faculty reporting.</p>
                        </div>
                    </div>

                    <div class="form-grid form-grid-2">
                        <div class="form-group span-1">
                            <label for="employee_number">Employee Number</label>
                            <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number') }}" placeholder="Optional employee reference number">
                        </div>

                        <div class="form-group span-1">
                            <label for="position">Position</label>
                            <select id="position" name="position">
                                <option value="">Select position</option>
                                <option value="Instructor" {{ old('position') == 'Instructor' ? 'selected' : '' }}>Instructor</option>
                                <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Registrar" {{ old('position') == 'Registrar' ? 'selected' : '' }}>Registrar</option>
                                <option value="President" {{ old('position') == 'President' ? 'selected' : '' }}>President</option>
                                <option value="Human Resource Officer" {{ old('position') == 'Human Resource Officer' ? 'selected' : '' }}>Human Resource Officer</option>
                                <option value="Security Personnel" {{ old('position') == 'Security Personnel' ? 'selected' : '' }}>Security Personnel</option>
                                <option value="Utility Personnel" {{ old('position') == 'Utility Personnel' ? 'selected' : '' }}>Utility Personnel</option>
                                <option value="Assessment Officer" {{ old('position') == 'Assessment Officer' ? 'selected' : '' }}>Assessment Officer</option>
                            </select>
                        </div>

                        <div class="form-group span-1">
                            <label for="employment_type">Employment Type</label>
                            <select id="employment_type" name="employment_type">
                                <option value="">Select employment type</option>
                                <option value="Job Order" {{ old('employment_type') == 'Job Order' ? 'selected' : '' }}>Job Order</option>
                                <option value="Permanent" {{ old('employment_type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="Contractual" {{ old('employment_type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                            </select>
                        </div>

                        <div class="form-group span-1">
                            <label for="employment_status">Employment Status</label>
                            <select id="employment_status" name="employment_status">
                                <option value="">Select employment status</option>
                                <option value="Full time" {{ old('employment_status') == 'Full time' ? 'selected' : '' }}>Full time</option>
                                <option value="Part time" {{ old('employment_status') == 'Part time' ? 'selected' : '' }}>Part time</option>
                            </select>
                        </div>

                        <div class="form-group span-1">
                            <label for="collegeID">Department</label>
                            <select id="collegeID" name="collegeID">
                                <option value="">Select Department</option>
                                @foreach($colleges as $college)
                                    <option value="{{ $college->collegeID }}"
                                        {{ old('collegeID') == $college->collegeID ? 'selected' : '' }}>
                                        {{ $college->college_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-card ascend-card">
                    <div class="card-head">
                        <div>
                            <h2 class="section-title">Educational Background</h2>
                            <p class="section-sub">Highest completed or relevant academic qualifications of the faculty member.</p>
                        </div>
                    </div>

                    <div class="form-grid form-grid-1">
                        <div class="form-group">
                            <label for="undergraduate_degree">Undergraduate Degree</label>
                            <input type="text" id="undergraduate_degree" name="undergraduate_degree" value="{{ old('undergraduate_degree') }}" placeholder="e.g. BS Information Technology">
                        </div>

                        <div class="form-group">
                            <label for="masters_degree">Master’s Degree</label>
                            <input type="text" id="masters_degree" name="masters_degree" value="{{ old('masters_degree') }}" placeholder="e.g. Master of Information Technology">
                        </div>

                        <div class="form-group">
                            <label for="doctoral_degree">Doctoral Degree</label>
                            <input type="text" id="doctoral_degree" name="doctoral_degree" value="{{ old('doctoral_degree') }}" placeholder="e.g. Doctor of Education">
                        </div>
                    </div>
                </div>
            </div>

            <div class="side-form-column">
                <div class="form-card ascend-card">
                    <div class="card-head compact-head">
                        <div>
                            <h2 class="section-title">Faculty Photo</h2>
                            <p class="section-sub">Upload a faculty profile image. The file path will be saved in the employee record.</p>
                        </div>
                    </div>

                    <div class="photo-upload-box">
                        <div class="photo-preview" id="photoPreviewBox">
                            <img id="photoPreviewImage" src="" alt="Faculty photo preview" style="display:none;">
                            <div class="photo-placeholder" id="photoPlaceholder">
                                <div class="photo-placeholder-icon">👤</div>
                                <div class="photo-placeholder-title">No photo selected</div>
                                <div class="photo-placeholder-sub">Recommended: square image for cleaner profile display.</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="photo">Faculty Photo</label>
                            <input type="file" id="photo" name="photo" accept="image/*">
                            <div class="field-help">Accepted file types depend on controller validation. Suggested formats: JPG, JPEG, PNG.</div>
                        </div>
                    </div>
                </div>

                <div class="form-card ascend-card sticky-card secondary-sticky">
                    <div class="card-head compact-head">
                        <div>
                            <h2 class="section-title">Save Record</h2>
                            <p class="section-sub">Review the faculty details before creating the employee profile.</p>
                        </div>
                    </div>

                    <div class="save-panel">
                        <div class="save-note">
                            This form is prepared for future integration with instructor assignment in Sections &amp; Offerings.
                        </div>

                        <div class="action-stack">
                            <button type="submit" class="btn btn-primary btn-block">Save Faculty</button>
                            <a href="{{ route('faculty.index') }}" class="btn btn-muted btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.faculty-create-page {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.faculty-create-page .ascend-card {
    background: #ffffff;
    border: 1px solid #e9e4f5;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(74, 31, 122, 0.08);
}

.faculty-create-page .page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 24px;
    background: linear-gradient(135deg, #4b1f7a 0%, #6d28d9 55%, #8b5cf6 100%);
    color: #ffffff;
    border: none;
}


#civil_status {
    height: 50px;
}
.faculty-create-page .page-eyebrow {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.85;
    margin-bottom: 6px;
}

.faculty-create-page .page-title {
    margin: 0;
    font-size: 28px;
    font-weight: 800;
    line-height: 1.15;
}

.faculty-create-page .page-sub {
    margin: 8px 0 0;
    font-size: 14px;
    opacity: 0.92;
}

.faculty-create-page .page-head-actions {
    display: flex;
    align-items: center;
}

.faculty-create-page .faculty-form-wrap {
    width: 100%;
}

.faculty-create-page .form-layout {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(320px, 0.9fr);
    gap: 18px;
    align-items: start;
}

.faculty-create-page .main-form-column,
.faculty-create-page .side-form-column {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.faculty-create-page .form-card {
    overflow: hidden;
}

.faculty-create-page .card-head {
    padding: 18px 20px 14px;
    border-bottom: 1px solid #eee7f8;
}

.faculty-create-page .compact-head {
    padding-bottom: 12px;
}

.faculty-create-page .section-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #2f1c4d;
}

.faculty-create-page .section-sub {
    margin: 6px 0 0;
    font-size: 13px;
    color: #6b7280;
}

.faculty-create-page .form-grid {
    display: grid;
    gap: 16px;
    padding: 18px 20px 20px;
}

.faculty-create-page .form-grid-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.faculty-create-page .form-grid-12 {
    grid-template-columns: repeat(12, minmax(0, 1fr));
}

.faculty-create-page .form-grid-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.faculty-create-page .form-grid-1 {
    grid-template-columns: 1fr;
}

.faculty-create-page .form-group {
    display: flex;
    flex-direction: column;
    gap: 7px;

    padding-right: 10px;
}

.faculty-create-page .form-grid {
    column-gap: 18px;
    row-gap: 14px;
}

.faculty-create-page .span-2 {
    grid-column: span 2;
}

.faculty-create-page .form-grid-4 .span-1,
.faculty-create-page .form-grid-2 .span-1 {
    grid-column: span 1;
}

.faculty-create-page .form-grid-4 .span-2,
.faculty-create-page .form-grid-2 .span-2 {
    grid-column: span 2;
}

.faculty-create-page .form-grid-4 .span-3 {
    grid-column: span 3;
}

.faculty-create-page .form-grid-4 .span-4 {
    grid-column: span 4;
}

.faculty-create-page .form-grid-12 .span-1  { grid-column: span 1; }
.faculty-create-page .form-grid-12 .span-2  { grid-column: span 2; }
.faculty-create-page .form-grid-12 .span-3  { grid-column: span 3; }
.faculty-create-page .form-grid-12 .span-4  { grid-column: span 4; }
.faculty-create-page .form-grid-12 .span-5  { grid-column: span 5; }
.faculty-create-page .form-grid-12 .span-6  { grid-column: span 6; }
.faculty-create-page .form-grid-12 .span-7  { grid-column: span 7; }
.faculty-create-page .form-grid-12 .span-8  { grid-column: span 8; }
.faculty-create-page .form-grid-12 .span-9  { grid-column: span 9; }
.faculty-create-page .form-grid-12 .span-10 { grid-column: span 10; }
.faculty-create-page .form-grid-12 .span-11 { grid-column: span 11; }
.faculty-create-page .form-grid-12 .span-12 { grid-column: span 12; }

.faculty-create-page label {
    font-size: 13px;
    font-weight: 700;
    color: #4b1f7a;
}

.faculty-create-page .required {
    color: #dc2626;
}

.faculty-create-page input[type="text"],
.faculty-create-page input[type="email"],
.faculty-create-page input[type="date"],
.faculty-create-page input[type="file"],
.faculty-create-page select,
.faculty-create-page textarea {
    width: 100%;
    height: 36px;
    padding: 6px 10px;
    border: 1px solid #d9cdee;
    border-radius: 10px;
    background: #ffffff;
    color: #2f1c4d;
    outline: none;
    transition: all 0.18s ease;
    font-size: 14px;
}

.faculty-create-page textarea {
    height: auto;
    min-height: 78px;
    resize: vertical;
}

.faculty-create-page input:focus,
.faculty-create-page select:focus,
.faculty-create-page textarea:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.10);
}

.faculty-create-page .photo-upload-box {
    padding: 18px 20px 20px;
}

.faculty-create-page .photo-preview {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 18px;
    border: 1px dashed #d8caef;
    background: linear-gradient(180deg, #faf7ff 0%, #f3ecff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 16px;
}

.faculty-create-page .photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.faculty-create-page .photo-placeholder {
    text-align: center;
    padding: 20px;
}

.faculty-create-page .photo-placeholder-icon {
    font-size: 42px;
    margin-bottom: 10px;
}

.faculty-create-page .photo-placeholder-title {
    font-size: 16px;
    font-weight: 800;
    color: #4b1f7a;
}

.faculty-create-page .photo-placeholder-sub,
.faculty-create-page .field-help,
.faculty-create-page .save-note {
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

.faculty-create-page .save-panel {
    padding: 18px 20px 20px;
}

.faculty-create-page .action-stack {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 14px;
}

.faculty-create-page .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 44px;
    padding: 0 14px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.18s ease;
    cursor: pointer;
}

.faculty-create-page .btn-block {
    width: 100%;
}

.faculty-create-page .btn-primary {
    background: linear-gradient(135deg, #4b1f7a 0%, #6d28d9 100%);
    color: #ffffff;
}

.faculty-create-page .btn-primary:hover {
    filter: brightness(1.04);
}

.faculty-create-page .btn-muted {
    background: #f6f1fc;
    color: #4b1f7a;
    border-color: #eee7f8;
}

.faculty-create-page .btn-muted:hover {
    background: #efe8fb;
}

.faculty-create-page .btn-light {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.24);
}

.faculty-create-page .btn-light:hover {
    background: rgba(255, 255, 255, 0.22);
}

.faculty-create-page .alert-card {
    padding: 16px 18px;
    border-radius: 16px;
    border: 1px solid #fecaca;
    background: #fff4f4;
}

.faculty-create-page .alert-title {
    font-size: 15px;
    font-weight: 800;
    color: #991b1b;
    margin-bottom: 8px;
}

.faculty-create-page .alert-list {
    margin: 0;
    padding-left: 18px;
    color: #7f1d1d;
    font-size: 13px;
}

.faculty-create-page .sticky-card {
    position: sticky;
    top: 18px;
}

.faculty-create-page .secondary-sticky {
    top: 420px;
}

@media (max-width: 1280px) {
    .faculty-create-page .form-grid-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .faculty-create-page .form-grid-12 {
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }
}

@media (max-width: 1100px) {
    .faculty-create-page .form-layout {
        grid-template-columns: 1fr;
    }

    .faculty-create-page .sticky-card,
    .faculty-create-page .secondary-sticky {
        position: static;
        top: auto;
    }

    .faculty-create-page .page-head {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .faculty-create-page .form-grid-4,
    .faculty-create-page .form-grid-2,
    .faculty-create-page .form-grid-12 {
        grid-template-columns: 1fr;
    }

    .faculty-create-page .span-1,
    .faculty-create-page .span-2,
    .faculty-create-page .span-3,
    .faculty-create-page .span-4,
    .faculty-create-page .span-5,
    .faculty-create-page .span-6,
    .faculty-create-page .span-7,
    .faculty-create-page .span-8,
    .faculty-create-page .span-9,
    .faculty-create-page .span-10,
    .faculty-create-page .span-11,
    .faculty-create-page .span-12 {
        grid-column: span 1;
    }


    
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photo');
    const previewImage = document.getElementById('photoPreviewImage');
    const placeholder = document.getElementById('photoPlaceholder');
    const contactInput = document.getElementById('contact_number');

    if (photoInput) {
        photoInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];

            if (!file) {
                previewImage.src = '';
                previewImage.style.display = 'none';
                placeholder.style.display = 'block';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    if (contactInput) {
        contactInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '').slice(0, 11);

            if (value.length > 4 && value.length <= 7) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            } else if (value.length > 7) {
                value = value.slice(0, 4) + '-' + value.slice(4, 7) + '-' + value.slice(7);
            }

            this.value = value;
        });
    }
});
</script>
@endsection
