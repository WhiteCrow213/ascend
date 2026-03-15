@extends('layouts.app')

@section('title', 'Create Assessment Template')

@push('styles')
<style>
  .assessment-page {
    padding: 16px;
  }

  .assessment-page p {
    margin: 0 0 14px;
    color: #6b7280;
  }

  .assessment-shell {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(18, 24, 40, 0.06);
    overflow: hidden;
  }

  .assessment-shell-head {
    padding: 18px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    background: linear-gradient(180deg, rgba(124,58,237,0.06), rgba(124,58,237,0.02));
  }

  .assessment-shell-head h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #111827;
  }

  .assessment-shell-body {
    padding: 20px;
  }

  .assessment-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 16px;
  }

  .col-12 { grid-column: span 12; }
  .col-6  { grid-column: span 6; }
  .col-4  { grid-column: span 4; }
  .col-3  { grid-column: span 3; }
  .col-2  { grid-column: span 2; }

  .field-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 800;
    color: #374151;
  }

  .field-control,
  .field-select {
    width: 100%;
    height: 44px;
    border: 1px solid rgba(0,0,0,0.10);
    border-radius: 12px;
    padding: 0 14px;
    background: #fff;
    color: #111827;
    font-size: 14px;
    outline: none;
  }

  .field-control:focus,
  .field-select:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
  }

  .field-preview {
    display: flex;
    align-items: center;
    min-height: 44px;
    padding: 10px 14px;
    border: 1px dashed rgba(124, 58, 237, 0.35);
    border-radius: 12px;
    background: rgba(124, 58, 237, 0.05);
    color: #5b21b6;
    font-weight: 800;
    letter-spacing: .02em;
  }

  .field-readonly {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
  }

  .section-block + .section-block {
    margin-top: 20px;
  }

  .section-title {
    margin: 0 0 14px;
    font-size: 15px;
    font-weight: 800;
    color: #111827;
  }

  .section-note {
    margin: -2px 0 14px;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
  }

  .fee-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 15px; /* extra spacing between fee fields */
  }

  .fee-card {
    grid-column: span 2;
    padding-left: 6px;
    padding-right: 6px;
    box-sizing: border-box;
  }
  /* Fee fields use their own width so they don't inherit the 80% rule above */
  .fee-grid .field-control {
    width: 70%;
  }


  .form-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 24px;
  }

  .btn-save,
  .btn-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    padding: 0 16px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 800;
    border: 0;
    cursor: pointer;
  }

  .btn-save {
    background: linear-gradient(135deg, #7c3aed, #5b21b6);
    color: #fff;
    box-shadow: 0 10px 24px rgba(91, 33, 182, 0.22);
  }

  .btn-back {
    background: #f3f4f6;
    color: #374151;
  }

  @media (max-width: 1200px) {
    .fee-card { grid-column: span 4; }
  }

  @media (max-width: 992px) {
    .col-6, .col-4, .col-3, .col-2 { grid-column: span 6; }
    .fee-card { grid-column: span 6; }
  }

  @media (max-width: 768px) {
    .col-12, .col-6, .col-4, .col-3, .col-2 { grid-column: span 12; }
    .fee-card { grid-column: span 12; }
  }
</style>
@endpush

@section('content')
<div class="assessment-page">

  <h1>Create Assessment Template</h1>
  <p>Set the template scope and encode the standard charges for this template.</p>

  <div class="assessment-shell">
    <div class="assessment-shell-head">
      <h2>Assessment Template Form</h2>
    </div>

    <div class="assessment-shell-body">
      @if (session('success'))
        <div style="margin-bottom:16px; padding:12px 14px; border-radius:12px; background:rgba(34,197,94,0.10); border:1px solid rgba(34,197,94,0.20); color:#166534; font-weight:700;">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div style="margin-bottom:16px; padding:12px 14px; border-radius:12px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.18); color:#991b1b;">
          <div style="font-weight:800; margin-bottom:6px;">Please fix the following:</div>
          <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('utilities.assessment.store') }}">
        @csrf

        <div class="section-block">
          <h3 class="section-title">Template Scope</h3>
          <p class="section-note">This section defines which students this template applies to.</p>

          <div class="assessment-grid">

            <div class="field-group col-4">
              <label for="program_code">Program</label>
              <select id="program_code" name="program_code" class="field-select">
                <option value="">Select Program</option>
                @foreach ($programs as $program)
                  <option value="{{ $program->IDProgram }}" {{ old('program_code') == $program->IDProgram ? 'selected' : '' }}>
                    {{ $program->program_code }} - {{ $program->program_name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="field-group col-2">
              <label for="year_level">Year Level</label>
              <select id="year_level" name="year_level" class="field-select">
                <option value="">Select Year</option>
                <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1</option>
                <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2</option>
                <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3</option>
                <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4</option>
              </select>
            </div>

            <div class="field-group col-3">
              <label for="term_code">Term</label>
              <select id="term_code" name="term_code" class="field-select">
                <option value="">Select Term</option>
                <option value="1" {{ old('term_code') == '1' ? 'selected' : '' }}>First Semester</option>
                <option value="2" {{ old('term_code') == '2' ? 'selected' : '' }}>Second Semester</option>
                <option value="summer" {{ old('term_code') == 'summer' ? 'selected' : '' }}>Midyear</option>
              </select>
            </div>

            <div class="field-group col-3">
              <label for="student_status">Student Status</label>
              <select id="student_status" name="student_status" class="field-select">
                <option value="">Select Status</option>
                <option value="OS" {{ old('student_status') == 'OS' ? 'selected' : '' }}>Old Student</option>
                <option value="TR" {{ old('student_status') == 'TR' ? 'selected' : '' }}>Transferee</option>
              </select>
            </div>

            <div class="field-group col-3">
              <label for="athlete_status">Athlete Status</label>
              <select id="athlete_status" name="athlete_status" class="field-select">
                <option value="">Select Athlete Status</option>
                <option value="AT" {{ old('athlete_status') == 'AT' ? 'selected' : '' }}>Athlete</option>
                <option value="NA" {{ old('athlete_status') == 'NA' ? 'selected' : '' }}>Non-Athlete</option>
              </select>
            </div>

            <div class="field-group col-6">
              <label for="template_name">Template Name</label>
              <input id="template_name" name="template_name" type="text" class="field-control" placeholder="Auto-generated or editable" value="{{ old('template_name') }}">
            </div>

            <div class="field-group col-12">
              <label>Template Name Preview</label>
              <div class="field-preview" id="templatePreview">—</div>
            </div>

          </div>
        </div>

        <div class="section-block">
          <h3 class="section-title">Charge Details</h3>
          <p class="section-note">Encode the amounts that belong to this assessment template.</p>

          <div class="fee-grid">

            <div class="field-group fee-card">
              <label for="tuition_fee">Tuition Fee</label>
              <input id="tuition_fee" name="tuition_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('tuition_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="athletic_fee">Athletic Fee</label>
              <input id="athletic_fee" name="athletic_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('athletic_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="computer_fee">Computer Fee</label>
              <input id="computer_fee" name="computer_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('computer_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="sociocultural_fee">Sociocultural Fee</label>
              <input id="sociocultural_fee" name="sociocultural_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('sociocultural_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="guidance_fee">Guidance Fee</label>
              <input id="guidance_fee" name="guidance_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('guidance_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="library_fee">Library Fee</label>
              <input id="library_fee" name="library_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('library_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="medical_dental_fee">Medical / Dental Fee</label>
              <input id="medical_dental_fee" name="medical_dental_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('medical_dental_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="development_fee">Development Fee</label>
              <input id="development_fee" name="development_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('development_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="registration_fee">Registration Fee</label>
              <input id="registration_fee" name="registration_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('registration_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="laboratory_units">Laboratory Units</label>
              <input id="laboratory_units" name="laboratory_units" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('laboratory_units', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="laboratory_fee">Laboratory Fee</label>
              <input id="laboratory_fee" name="laboratory_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('laboratory_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="entrance_exam_fee">Entrance Exam Fee</label>
              <input id="entrance_exam_fee" name="entrance_exam_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('entrance_exam_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="admission_fee">Admission Fee</label>
              <input id="admission_fee" name="admission_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('admission_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="handbook_fee">Handbook Fee</label>
              <input id="handbook_fee" name="handbook_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('handbook_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="school_id_fee">School ID Fee</label>
              <input id="school_id_fee" name="school_id_fee" type="text" inputmode="decimal" autocomplete="off" class="field-control js-decimal-only" value="{{ old('school_id_fee', '0.00') }}">
            </div>

            <div class="field-group fee-card">
              <label for="total_fees">Total Fees</label>
              <input id="total_fees" name="total_fees" type="text" class="field-control field-readonly" value="{{ old('total_fees', '0.00') }}" readonly tabindex="-1">
            </div>

          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Save Template</button>
          <a href="{{ route('utilities.assessment.index') }}" class="btn-back">Back</a>
        </div>

      </form>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  (function () {
    const program = document.getElementById('program_code');
    const year = document.getElementById('year_level');
    const term = document.getElementById('term_code');
    const status = document.getElementById('student_status');
    const athlete = document.getElementById('athlete_status');
    const templateName = document.getElementById('template_name');
    const preview = document.getElementById('templatePreview');

    function buildTemplateName() {
      const parts = [];

      if (program && program.value) parts.push(program.value);
      if (year && year.value) parts.push(year.value);
      if (term && term.value) parts.push(term.value);
      if (status && status.value) parts.push(status.value);
      if (athlete && athlete.value) parts.push(athlete.value);

      return parts.join(':');
    }

    function updateTemplatePreview() {
      const generated = buildTemplateName();
      preview.textContent = generated || '—';

      if (templateName && !templateName.dataset.manualEdit) {
        templateName.value = generated;
      }
    }

    function calculateTotalFees() {
      const totalFees = document.getElementById('total_fees');

      if (!totalFees) return;

      const feeFieldIds = [
        'tuition_fee',
        'athletic_fee',
        'computer_fee',
        'sociocultural_fee',
        'guidance_fee',
        'library_fee',
        'medical_dental_fee',
        'development_fee',
        'registration_fee',
        'laboratory_fee',
        'entrance_exam_fee',
        'admission_fee',
        'handbook_fee',
        'school_id_fee'
      ];

      let total = 0;

      feeFieldIds.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);

        if (!field) return;

        const parsed = parseFloat(field.value);

        if (!isNaN(parsed) && parsed >= 0) {
          total += parsed;
        }
      });

      totalFees.value = total.toFixed(2);
    }

    if (templateName) {
      templateName.addEventListener('input', function () {
        this.dataset.manualEdit = this.value.trim() !== '' ? '1' : '';
      });
    }

    [program, year, term, status, athlete].forEach(function (el) {
      if (!el) return;
      el.addEventListener('change', updateTemplatePreview);
    });

    document.querySelectorAll('.js-decimal-only').forEach(function (input) {
      input.addEventListener('input', function () {
        let value = this.value.replace(/[^\d.]/g, '');
        const firstDot = value.indexOf('.');

        if (firstDot !== -1) {
          value = value.slice(0, firstDot + 1) + value.slice(firstDot + 1).replace(/\./g, '');
        }

        this.value = value;
        calculateTotalFees();
      });

      input.addEventListener('blur', function () {
        const raw = this.value.trim();

        if (raw === '' || raw === '.') {
          this.value = '0.00';
          calculateTotalFees();
          return;
        }

        const parsed = parseFloat(raw);

        if (isNaN(parsed) || parsed < 0) {
          this.value = '0.00';
          calculateTotalFees();
          return;
        }

        this.value = parsed.toFixed(2);
        calculateTotalFees();
      });
    });

    updateTemplatePreview();
    calculateTotalFees();
  })();
</script>
@endpush

