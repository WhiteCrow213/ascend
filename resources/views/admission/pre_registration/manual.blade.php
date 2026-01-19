@extends('layouts.app')

@section('content')
<div class="wrap">

  <h1>Manual Pre-Registration (Walk-in)</h1>
  <p>Step 1: Personal Information → Step 2: Educational Background → Step 3: Program Choices</p>

  @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert error">
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="steps">
    <button type="button" id="tab1" class="tab active">1. Personal Info</button>
    <button type="button" id="tab2" class="tab">2. Educational Background</button>
    <button type="button" id="tab3" class="tab">3. Program Choices</button>
  </div>

  {{-- Use URL so it never breaks if route names change --}}
  <form method="POST" action="{{ url('admission/pre-registration/manual') }}">
    @csrf

    {{-- ================= STEP 1 ================= --}}
    <section id="step1">
      <h2>Personal Information</h2>

      <div class="grid3">
        <div class="field">
          <label>First Name</label>
          <input name="FistName" value="{{ old('FistName') }}" required autofocus>
        </div>

        <div class="field">
          <label>Middle Name</label>
          <input name="MidName" value="{{ old('MidName') }}">
        </div>

        <div class="field">
          <label>Last Name</label>
          <input name="LastName" value="{{ old('LastName') }}" required>
        </div>

        <div class="field">
          <label>Suffix</label>
          <input name="Suffix" value="{{ old('Suffix') }}">
        </div>

        <div class="field">
          <label>Contact No.</label>
          <input name="ContactNo" value="{{ old('ContactNo') }}" required>
        </div>

        <div class="field">
          <label>Email</label>
          <input name="email" type="email" value="{{ old('email') }}" required>
        </div>

        <div class="field">
          <label>Birthdate</label>
          <input name="Birthdate" type="date" value="{{ old('Birthdate') }}" required>
        </div>

        <div class="field">
          <label>Gender</label>
          <select name="Gender" required>
            <option value="">Select</option>
            <option value="Male" @selected(old('Gender')==='Male')>Male</option>
            <option value="Female" @selected(old('Gender')==='Female')>Female</option>
          </select>
        </div>

        <div class="field">
          <label>Citizenship</label>
          <input name="Citizenship" value="{{ old('Citizenship','Filipino') }}" required>
        </div>

        <div class="field">
          <label>Civil Status</label>
          <select name="CivilStatus" required>
            <option value="">Select</option>
            @foreach(['Single','Married','Widowed','Separated'] as $cs)
              <option value="{{ $cs }}" @selected(old('CivilStatus')===$cs)>{{ $cs }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label>Religion</label>
          <input name="Religion" value="{{ old('Religion') }}" required>
        </div>

        <div class="field">
          <label>Blood Type</label>
          <input name="Bloodtype" value="{{ old('Bloodtype') }}">
        </div>

        <div class="field">
          <label>Height (cm)</label>
          <input name="Height" value="{{ old('Height') }}">
        </div>

        <div class="field">
          <label>Weight (kg)</label>
          <input name="Weight" value="{{ old('Weight') }}">
        </div>
      </div>

      <div class="actions">
        <button type="button" id="next1" class="btn primary">Next</button>
      </div>
    </section>

    {{-- ================= STEP 2 ================= --}}
    <section id="step2" style="display:none">
      <h2>Educational Background</h2>

      <div class="grid2">
        <div class="field">
          <label>Primary School</label>
          <input name="PrimarySchool" value="{{ old('PrimarySchool') }}" required disabled>
        </div>

        <div class="field">
          <label>Primary School Address</label>
          <input name="PrimarySchool_Address" value="{{ old('PrimarySchool_Address') }}" required disabled>
        </div>

        <div class="field">
          <label>Year Graduated (Primary)</label>
          {{-- textbox per request --}}
          <input name="YearGradPrimary" value="{{ old('YearGradPrimary') }}"
                 type="text" inputmode="numeric" maxlength="4" placeholder="e.g. 2006"
                 required disabled>
        </div>

        <div class="field spacer" aria-hidden="true"></div>

        {{-- Secondary School moved to next line (full width) --}}
        <div class="field" style="grid-column:1/-1;">
          <label>Secondary School</label>
          <input name="SecondarySchool" value="{{ old('SecondarySchool') }}" required disabled>
        </div>

        <div class="field">
          <label>Secondary School Address</label>
          <input name="SecondarySchool_Address" value="{{ old('SecondarySchool_Address') }}" required disabled>
        </div>

        <div class="field">
          <label>Year Graduated (Secondary)</label>
          {{-- textbox per request --}}
          <input name="YearGradSecondary" value="{{ old('YearGradSecondary') }}"
                 type="text" inputmode="numeric" maxlength="4" placeholder="e.g. 2012"
                 required disabled>
        </div>

        {{-- Last School Attended not too long --}}
        <div class="field short">
          <label>Last School Attended</label>
          <input name="LastSchoolAttended" value="{{ old('LastSchoolAttended') }}" required disabled>
        </div>

        <div class="field spacer" aria-hidden="true"></div>
      </div>

      <div class="actions between">
        <button type="button" id="back2" class="btn ghost">Back</button>
        <button type="button" id="next2" class="btn primary">Next</button>
      </div>
    </section>

    {{-- ================= STEP 3 ================= --}}
    <section id="step3" style="display:none">
      <h2>Preferred College Programs</h2>

      @php
        $programs = [
          'Bachelor of Science in Criminology',
          'Bachelor of Industrial Security Management',
          'Bachelor of Elementary Education',
          'Bachelor of Secondary Education Major in English',
          'Bachelor of Secondary Education Major in Filipino',
          'Bachelor of Secondary Education Major in Science',
          'Bachelor of Secondary Education Major in Mathematics',
        ];
      @endphp

      <div class="grid2">
        <div class="field">
          <label>First Program Choice</label>
          <select name="FirstProgramChoice" required disabled>
            <option value="">Select a program</option>
            @foreach($programs as $p)
              <option value="{{ $p }}" @selected(old('FirstProgramChoice')===$p)>{{ $p }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label>Second Program Choice</label>
          <select name="SecondProgramChoice" required disabled>
            <option value="">Select a program</option>
            @foreach($programs as $p)
              <option value="{{ $p }}" @selected(old('SecondProgramChoice')===$p)>{{ $p }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="actions between">
        <button type="button" id="back3" class="btn ghost">Back</button>
        <button type="submit" class="btn primary">Save Pre-Registration</button>
      </div>
    </section>

  </form>
</div>

<script>
(function () {
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');

  const tab1 = document.getElementById('tab1');
  const tab2 = document.getElementById('tab2');
  const tab3 = document.getElementById('tab3');

  const next1 = document.getElementById('next1');
  const next2 = document.getElementById('next2');
  const back2 = document.getElementById('back2');
  const back3 = document.getElementById('back3');

  function setEnabled(panel, enabled) {
    panel.querySelectorAll('input, select, textarea, button').forEach(el => {
      // keep navigation buttons enabled
      if (el.id === 'next1' || el.id === 'next2' || el.id === 'back2' || el.id === 'back3') return;
      if (enabled) el.removeAttribute('disabled');
      else el.setAttribute('disabled', 'disabled');
    });
  }

  function validateRequired(panel) {
    const required = panel.querySelectorAll('[required]');
    for (const el of required) {
      if (!el.disabled && !el.value) {
        el.focus();
        el.reportValidity();
        return false;
      }
    }
    return true;
  }

  function activateTab(n) {
    [tab1, tab2, tab3].forEach(t => t.classList.remove('active'));
    if (n === 1) tab1.classList.add('active');
    if (n === 2) tab2.classList.add('active');
    if (n === 3) tab3.classList.add('active');
  }

  function showStep(n) {
    step1.style.display = (n === 1) ? 'block' : 'none';
    step2.style.display = (n === 2) ? 'block' : 'none';
    step3.style.display = (n === 3) ? 'block' : 'none';

    setEnabled(step1, n === 1);
    setEnabled(step2, n === 2);
    setEnabled(step3, n === 3);

    activateTab(n);

    const first = (n === 1 ? step1 : n === 2 ? step2 : step3).querySelector('input, select');
    if (first) first.focus();
  }

  next1.addEventListener('click', () => {
    if (!validateRequired(step1)) return;
    showStep(2);
  });

  next2.addEventListener('click', () => {
    if (!validateRequired(step2)) return;
    showStep(3);
  });

  back2.addEventListener('click', () => showStep(1));
  back3.addEventListener('click', () => showStep(2));

  // allow clicking tabs, but keep validation friendly
  tab1.addEventListener('click', () => showStep(1));
  tab2.addEventListener('click', () => { if (validateRequired(step1)) showStep(2); });
  tab3.addEventListener('click', () => { if (validateRequired(step1) && validateRequired(step2)) showStep(3); });

  showStep(1);
})();
</script>

<style>
* { box-sizing: border-box; }

.wrap { max-width: 1100px; margin: auto; padding: 24px; }

.steps { display:flex; gap:10px; margin: 10px 0 18px; }
.tab {
  padding: 10px 14px; border-radius: 10px; border: 1px solid #e5e7eb;
  background: #f3f4f6; cursor: pointer;
}
.tab.active { background: #ede9fe; border-color:#c4b5fd; font-weight: 700; }

.grid3 { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
.grid2 { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }

.field { display:flex; flex-direction: column; min-width: 0; }
.field.short { max-width: 520px; }          /* keeps Last School Attended shorter */
.field.spacer { visibility: hidden; }       /* keeps grid alignment without showing */

label { font-size: 12px; font-weight: 600; margin-bottom: 6px; color:#374151; }

input, select {
  width:100%; height: 40px; padding: 8px 10px; font-size: 14px;
  border-radius: 10px; border: 1px solid #d1d5db; background:#fff;
}

select {
  appearance: none; -webkit-appearance:none; -moz-appearance:none;
  padding-right: 32px;
  background-image:
    linear-gradient(45deg, transparent 50%, #6b7280 50%),
    linear-gradient(135deg, #6b7280 50%, transparent 50%);
  background-position: calc(100% - 18px) 50%, calc(100% - 12px) 50%;
  background-size: 6px 6px, 6px 6px;
  background-repeat: no-repeat;
}

input:focus, select:focus {
  outline:none; border-color:#7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,.12);
}

h2 { margin: 16px 0 14px; font-size: 16px; font-weight: 700; }

.actions { margin-top: 18px; display:flex; justify-content:flex-end; }
.actions.between { justify-content: space-between; }

.btn { padding: 10px 16px; border-radius: 10px; border: none; cursor: pointer; }
.btn.primary { background:#7c3aed; color:#fff; }
.btn.ghost { background:#e5e7eb; color:#111827; }

.alert { margin: 10px 0 16px; padding: 10px 14px; border-radius: 10px; }
.alert.success { background:#ecfdf5; color:#065f46; }
.alert.error { background:#fef2f2; color:#991b1b; }

@media (max-width: 900px) { .grid3 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .grid3, .grid2 { grid-template-columns: 1fr; } .field.short{max-width:100%;} }
</style>
@endsection
