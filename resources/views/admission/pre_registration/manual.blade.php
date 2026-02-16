@extends('layouts.app')

@section('content')

<script>
  // Force sentence case (capitalize first letter)
  document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('sentence')) return;

    let val = e.target.value;
    if (!val) return;

    // Trim leading spaces, capitalize first character only
    e.target.value =
      val.charAt(0).toUpperCase() + val.slice(1);
  });
</script>

<div class="wrap">

  <h1>Manual Pre-Registration</h1>
  <p>Step 1: Personal Information → Step 2: Contact Information → Step 3: Educational Background → Step 4: Program Choice</p>

  {{-- ✅ SAVE ACKNOWLEDGEMENT --}}
  @if(session('prereg_saved'))
    <div class="ack">
      <h2 class="ack-title">Pre-registration saved ✅</h2>
      <p class="ack-sub">
        Applicant Number:
        <span class="ack-no">{{ session('applicant_no') }}</span>
      </p>

      <div class="ack-actions">
        <a class="btn primary" href="{{ route('admission.prereg.manual') }}">Add Another</a>
        <a class="btn ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
      </div>
    </div>

  @else

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
      <button type="button" id="tab1" class="tab active">1. Student Info</button>
      <button type="button" id="tab2" class="tab">2. Parent/Guardian</button>
      <button type="button" id="tab3" class="tab">3. Educational Background</button>
      <button type="button" id="tab4" class="tab">4. Program Choices</button>
      <button type="button" id="tab5" class="tab">5. Photo & Print</button>
    </div>

    {{-- Use URL so it never breaks if route names change --}}
    <form method="POST" action="{{ route('admission.prereg.manual.store') }}">
    @csrf

     {{-- ================= STEP 1 ================= --}}
<section id="step1">
  <h2>Personal Information</h2>

  {{-- ROW 1 --}}
  <div class="grid3">
    <div class="field">
      <label>First Name</label>
      <input name="FirstName" value="{{ old('FirstName') }}" required autofocus class="sentence">
    </div>

    <div class="field">
      <label>Middle Name</label>
      <input name="MidName" value="{{ old('MidName') }}" class="sentence">
    </div>

    <div class="field">
      <label>Last Name</label>
      <input name="LastName" value="{{ old('LastName') }}" required class="sentence">
    </div>

    {{-- ROW 2 --}}
    <div class="field">
      <label>Suffix</label>
      <input name="Suffix" value="{{ old('Suffix') }}" class="sentence">
    </div>

    <div class="field">
      <label>Contact No.</label>
      <input name="ContactNo" value="{{ old('ContactNo') }}" required>
    </div>

    <div class="field">
      <label>Email</label>
      <input name="email" type="email" value="{{ old('email') }}" required>
    </div>

    {{-- ROW 3 (Birthdate + Place of Birth + Gender) --}}
    <div class="field">
      <label>Birthdate</label>
      <input name="Birthdate" type="date" value="{{ old('Birthdate') }}" required>
    </div>

    <div class="field">
      <label>Place of Birth</label>
      <input name="place_of_birth"
             value="{{ old('place_of_birth', $student->place_of_birth ?? '') }}"
             class="sentence"
             placeholder="e.g., Don Carlos, Bukidnon">
    </div>

    <div class="field">
      <label>Gender</label>
      <select name="Gender" required>
        <option value="">Select</option>
        <option value="Male" @selected(old('Gender')==='Male')>Male</option>
        <option value="Female" @selected(old('Gender')==='Female')>Female</option>
      </select>
    </div>

    {{-- ROW 4 (Citizenship + Civil Status + Religion) --}}
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
      <input name="Religion" value="{{ old('Religion') }}" required class="sentence">
    </div>

    {{-- ROW 5 (Height + Weight + Blood Type) --}}
    <div class="field">
      <label>Height (cm)</label>
      <input name="Height" value="{{ old('Height') }}">
    </div>

    <div class="field">
      <label>Weight (kg)</label>
      <input name="Weight" value="{{ old('Weight') }}">
    </div>

    <div class="field">
      <label>Blood Type</label>
      <input name="Bloodtype" value="{{ old('Bloodtype') }}">
    </div>
  </div>

  {{-- ================= ADDRESS BLOCK ================= --}}
  <h2 style="margin-top:24px;">Address Information</h2>

  <div class="grid2">
    <div class="field">
      <label>Region</label>
      <select id="region_psgc" name="region_psgc" required>
        <option value="">Select region</option>
        @foreach(($regions ?? []) as $r)
          <option value="{{ $r->psgc_code }}" @selected(old('region_psgc')===$r->psgc_code)>
            {{ $r->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="field">
      <label>Province</label>
      <select id="province_psgc" name="province_psgc">
        <option value="">Select province</option>
      </select>
      <small class="hint">NCR has no province</small>
    </div>

    <div class="field">
      <label>City / Municipality</label>
      <select id="citymun_psgc" name="citymun_psgc" required>
        <option value="">Select city / municipality</option>
      </select>
    </div>

    <div class="field">
      <label>Barangay</label>
      <select id="brgy_psgc" name="brgy_psgc" required>
        <option value="">Select barangay</option>
      </select>
    </div>

    <div class="field">
      <label>House No. / Street / Purok</label>
      <input name="address_line" value="{{ old('address_line') }}" class="sentence"
             placeholder="e.g. Purok 3, Maligaya St.">
    </div>

    <div class="field">
      <label>ZIP Code</label>
      <input id="zip_code" type="text" readonly placeholder="Auto">
    </div>
  </div>

  <div class="actions">
    <button type="button" id="next1" class="btn primary">Next</button>
  </div>
</section>

{{-- ================= STEP 2 ================= --}}
<section id="step2" style="display:none">
  <h2>Parent / Guardian Information</h2>

  <h3 style="margin:14px 0 10px;">Father Information</h3>
  <input type="hidden" name="guardians[0][relationship]" value="Father" disabled>
  <div class="grid3">
    <div class="field">
      <label>Father First Name</label>
      <input name="guardians[0][guardFNAME]" value="{{ old('guardians.0.guardFNAME') }}" required class="sentence" disabled>
    </div>
    <div class="field">
      <label>Father Middle Name</label>
      <input name="guardians[0][guardMname]" value="{{ old('guardians.0.guardMname') }}" class="sentence" disabled>
    </div>
    <div class="field">
      <label>Father Last Name</label>
      <input name="guardians[0][guardLname]" value="{{ old('guardians.0.guardLname') }}" required class="sentence" disabled>
    </div>

    <div class="field">
      <label>Father Contact Number</label>
      <input name="guardians[0][contact_number]" value="{{ old('guardians.0.contact_number') }}" required disabled>
    </div>
    <div class="field">
      <label>Father Occupation</label>
      <input name="guardians[0][occupation]" value="{{ old('guardians.0.occupation') }}" class="sentence" disabled>
    </div>
    <div class="field">
      <label>Father Annual Income (₱)</label>
      <select name="guardians[0][annual_income]" disabled>
        <option value="">Select annual income range</option>
        <option value="50000" { old('guardians.0.annual_income') == '50000' ? 'selected' : '' }>₱0 – 50,000</option>
        <option value="100000" { old('guardians.0.annual_income') == '100000' ? 'selected' : '' }>₱50,001 – 100,000</option>
        <option value="150000" { old('guardians.0.annual_income') == '150000' ? 'selected' : '' }>₱100,001 – 150,000</option>
        <option value="200000" { old('guardians.0.annual_income') == '200000' ? 'selected' : '' }>₱150,001 – 200,000</option>
        <option value="250000" { old('guardians.0.annual_income') == '250000' ? 'selected' : '' }>₱200,001 – 250,000</option>
        <option value="300000" { old('guardians.0.annual_income') == '300000' ? 'selected' : '' }>₱250,001 – 300,000</option>
        <option value="350000" { old('guardians.0.annual_income') == '350000' ? 'selected' : '' }>₱300,001 – 350,000</option>
        <option value="400000" { old('guardians.0.annual_income') == '400000' ? 'selected' : '' }>₱350,001 – 400,000</option>
        <option value="500000" { old('guardians.0.annual_income') == '500000' ? 'selected' : '' }>₱400,001 – 500,000</option>
        <option value="500001" { old('guardians.0.annual_income') == '500001' ? 'selected' : '' }>₱500,001 and above</option>
      </select>
</div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label>Father Address</label>
      <input name="guardians[0][address]" value="{{ old('guardians.0.address') }}" class="sentence" placeholder="Textbox (free input)" disabled>
    </div>
    <div class="field">
      <label>Father Highest Educational Attainment</label>
      <input name="guardians[0][highest_education]" value="{{ old('guardians.0.highest_education') }}" class="sentence" placeholder="e.g. College Graduate" disabled>
    </div>
  </div>

  
  <div class="field" style="margin:8px 0 14px; text-align:left; width:100%; display:block;">
  <label for="cp_use_father"
         style="display:inline-flex; align-items:center; justify-content:flex-start; gap:4px; white-space:nowrap; margin:0; width:fit-content;">
    <input type="checkbox" id="cp_use_father" name="cp_use_father" value="1"
           style="transform:scale(0.40); margin:0;">
    <span style="font-weight:600; margin:0; padding:0;">Use Father as Contact Person</span>
  </label>
</div>

<h3 style="margin:18px 0 10px;">Mother Information</h3>
  <input type="hidden" name="guardians[1][relationship]" value="Mother" disabled>
  <div class="grid3">
    <div class="field">
      <label>Mother First Name</label>
      <input name="guardians[1][guardFNAME]" value="{{ old('guardians.1.guardFNAME') }}" required class="sentence" disabled>
    </div>
    <div class="field">
      <label>Mother Middle Name</label>
      <input name="guardians[1][guardMname]" value="{{ old('guardians.1.guardMname') }}" class="sentence" disabled>
    </div>
    <div class="field">
      <label>Mother Last Name</label>
      <input name="guardians[1][guardLname]" value="{{ old('guardians.1.guardLname') }}" required class="sentence" disabled>
    </div>

    <div class="field">
      <label>Mother Contact Number</label>
      <input name="guardians[1][contact_number]" value="{{ old('guardians.1.contact_number') }}" required disabled>
    </div>
    <div class="field">
      <label>Mother Occupation</label>
      <input name="guardians[1][occupation]" value="{{ old('guardians.1.occupation') }}" class="sentence" disabled>
    </div>
    <div class="field">
      <label>Mother Annual Income (₱)</label>
      <select name="guardians[1][annual_income]" disabled>
        <option value="">Select annual income range</option>
        <option value="50000" { old('guardians.1.annual_income') == '50000' ? 'selected' : '' }>₱0 – 50,000</option>
        <option value="100000" { old('guardians.1.annual_income') == '100000' ? 'selected' : '' }>₱50,001 – 100,000</option>
        <option value="150000" { old('guardians.1.annual_income') == '150000' ? 'selected' : '' }>₱100,001 – 150,000</option>
        <option value="200000" { old('guardians.1.annual_income') == '200000' ? 'selected' : '' }>₱150,001 – 200,000</option>
        <option value="250000" { old('guardians.1.annual_income') == '250000' ? 'selected' : '' }>₱200,001 – 250,000</option>
        <option value="300000" { old('guardians.1.annual_income') == '300000' ? 'selected' : '' }>₱250,001 – 300,000</option>
        <option value="350000" { old('guardians.1.annual_income') == '350000' ? 'selected' : '' }>₱300,001 – 350,000</option>
        <option value="400000" { old('guardians.1.annual_income') == '400000' ? 'selected' : '' }>₱350,001 – 400,000</option>
        <option value="500000" { old('guardians.1.annual_income') == '500000' ? 'selected' : '' }>₱400,001 – 500,000</option>
        <option value="500001" { old('guardians.1.annual_income') == '500001' ? 'selected' : '' }>₱500,001 and above</option>
      </select>
</div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label>Mother Address</label>
      <input name="guardians[1][address]" value="{{ old('guardians.1.address') }}" class="sentence" placeholder="Textbox (free input)" disabled>
    </div>
    <div class="field">
      <label>Mother Highest Educational Attainment</label>
      <input name="guardians[1][highest_education]" value="{{ old('guardians.1.highest_education') }}" class="sentence" placeholder="e.g. College Graduate" disabled>
    </div>
  </div>

      
  <div class="field" style="margin:8px 0 14px; text-align:left; width:100%; display:block;">
  <label for="cp_use_mother"
         style="display:inline-flex; align-items:center; justify-content:flex-start; gap:4px; white-space:nowrap; margin:0; width:fit-content;">
    <input type="checkbox" id="cp_use_mother" name="cp_use_mother" value="1"
           style="transform:scale(0.40); margin:0;">
    <span style="font-weight:600; margin:0; padding:0;">Use Mother as Contact Person</span>
  </label>
</div>

<h3 style="margin:18px 0 8px;">Contact Person (for Student ID / official records)</h3>
  <p class="hint" style="margin:0 0 12px; color:#6b7280; font-size:13px;">
    Choose Father or Mother if you want their info as the contact person on the student ID. If neither is chosen, you may enter a different contact person.
  </p>

    {{-- Relationship/Role for this row --}}
  <input type="hidden" name="guardians[2][relationship]" value="Contact Person" disabled>

  <div id="contact_person_block" style="padding:12px; border:1px solid rgba(118,0,188,.25); border-radius:12px;">
    <div class="grid3">
      <div class="field">
        <label>Contact Person First Name</label>
        <input name="guardians[2][guardFNAME]" value="{{ old('guardians.2.guardFNAME') }}" required class="sentence" disabled>
      </div>
      <div class="field">
        <label>Contact Person Middle Name</label>
        <input name="guardians[2][guardMname]" value="{{ old('guardians.2.guardMname') }}" class="sentence" disabled>
      </div>
      <div class="field">
        <label>Contact Person Last Name</label>
        <input name="guardians[2][guardLname]" value="{{ old('guardians.2.guardLname') }}" required class="sentence" disabled>
      </div>
    </div>

    <div class="grid2" style="margin-top:10px;">
      <div class="field">
        <label>Contact Person Contact Number</label>
        <input name="guardians[2][contact_number]" value="{{ old('guardians.2.contact_number') }}" required disabled>
      </div>
      <div class="field">
        <label>Contact Person Address</label>
        <input name="guardians[2][address]" value="{{ old('guardians.2.address') }}" class="sentence" placeholder="Textbox (free input)" disabled>
      </div>
    </div>
  </div>

<div class="actions between">
    <button type="button" id="back2" class="btn ghost">Back</button>
    <button type="button" id="next2" class="btn primary">Next</button>
  </div>
</section>


      
{{-- ===================== --}}
{{-- STEP 3 — EDUCATIONAL BACKGROUND --}}
{{-- (Saved into tbl_student_info) --}}
{{-- Fields:
     PrimarySchool
     PrimarySchool_Address
     YearGradPrimary
     SecondarySchool
     SecondarySchool_Address
     YearGradSecondary
     LastSchoolAttended
--}}
{{-- ===================== --}}
<section id="step3" class="step" style="display:none">
  <h2>Step 3: Educational Background</h2>


  <div class="field" style="margin: 0 0 14px;">
    <label style="font-weight:600; display:block; margin-bottom:6px;">Applicant Type</label>
    <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
      <label style="display:inline-flex; gap:8px; align-items:center;">
        <input type="radio" name="applicant_type" value="Freshman" {{ old('applicant_type') === 'Freshman' ? 'checked' : '' }}>
        <span>Freshman</span>
      </label>
      <label style="display:inline-flex; gap:8px; align-items:center;">
        <input type="radio" name="applicant_type" value="Transferee" {{ old('applicant_type') === 'Transferee' ? 'checked' : '' }}>
        <span>Transferee</span>
      </label>
    </div>
    @error('applicant_type') <small class="err">{{ $message }}</small> @enderror
  </div>

  {{-- Layout request: shorter inputs + arranged rows --}}
  <div class="grid2">
    <div class="field">
      <label for="PrimarySchool">Primary School</label>
      <input type="text" id="PrimarySchool" name="PrimarySchool"
             value="{{ old('PrimarySchool') }}" class="sentence" maxlength="255">
      @error('PrimarySchool') <small class="err">{{ $message }}</small> @enderror
    </div>

    <div class="field">
      <label for="PrimarySchool_Address">Primary School Address</label>
      <input type="text" id="PrimarySchool_Address" name="PrimarySchool_Address"
             value="{{ old('PrimarySchool_Address') }}" class="sentence" maxlength="255">
      @error('PrimarySchool_Address') <small class="err">{{ $message }}</small> @enderror
    </div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label for="YearGradPrimary">Year Graduated (Primary)</label>
      <input type="text" id="YearGradPrimary" name="YearGradPrimary" value="{{ old('YearGradPrimary') }}" maxlength="4" inputmode="numeric" pattern="\d{4}" placeholder="YYYY">
      @error('YearGradPrimary') <small class="err">{{ $message }}</small> @enderror
    </div>
    <div></div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label for="SecondarySchool">Secondary School</label>
      <input type="text" id="SecondarySchool" name="SecondarySchool"
             value="{{ old('SecondarySchool') }}" class="sentence" maxlength="255">
      @error('SecondarySchool') <small class="err">{{ $message }}</small> @enderror
    </div>

    <div class="field">
      <label for="SecondarySchool_Address">Secondary School Address</label>
      <input type="text" id="SecondarySchool_Address" name="SecondarySchool_Address"
             value="{{ old('SecondarySchool_Address') }}" class="sentence" maxlength="255">
      @error('SecondarySchool_Address') <small class="err">{{ $message }}</small> @enderror
    </div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label for="YearGradSecondary">Year Graduated (Secondary)</label>
      <input type="text" id="YearGradSecondary" name="YearGradSecondary" value="{{ old('YearGradSecondary') }}" maxlength="4" inputmode="numeric" pattern="\d{4}" placeholder="YYYY">
      @error('YearGradSecondary') <small class="err">{{ $message }}</small> @enderror
    </div>
    <div></div>
  </div>

  <div class="grid2" style="margin-top:14px;">
    <div class="field">
      <label for="LastSchoolAttended">Last School Attended</label>
      <input type="text" id="LastSchoolAttended" name="LastSchoolAttended"
             value="{{ old('LastSchoolAttended') }}" class="sentence" maxlength="255">
      @error('LastSchoolAttended') <small class="err">{{ $message }}</small> @enderror
    </div>
    <div></div>
  </div>

<div class="actions between">
    <button type="button" id="back3" class="btn ghost">Back</button>
    <button type="button" id="next3" class="btn primary">Next</button>
  </div>
</section>


{{-- ================= STEP 4 ================= --}}
      <section id="step4" style="display:none">
        <h2>Program Choices</h2>

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
    <button type="button" id="back4" class="btn ghost">Back</button>
    <button type="button" id="next4" class="btn primary">Next</button>
  </div>
      </section>

    

      {{-- ================= STEP 5 ================= --}}
      <section id="step5" style="display:none">
        <h2>Profile Photo (2x2) & Printable Copy</h2>

        <div class="grid2">
          <div class="field">
            <label for="profile_photo">Upload Profile Photo</label>
            <input type="file" id="profile_photo" accept="image/*">
            <small class="hint">Tip: Upload a clear headshot. You’ll crop it into a square (2x2 when printed).</small>
          </div>
          <div class="field">
            <label>Preview (Cropped)</label>
            <div style="display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap;">
              <img id="photoPreview" alt="Cropped preview"
                   style="width:120px; height:120px; object-fit:cover; border-radius:10px; border:1px solid rgba(0,0,0,.12); display:none;">
              <div>
                <button type="button" id="btnCropPhoto" class="btn primary" style="display:none;">Crop & Use Photo</button>
                <button type="button" id="btnClearPhoto" class="btn ghost" style="display:none; margin-left:8px;">Remove</button>
              </div>
            </div>
          </div>
        </div>

        {{-- Crop area --}}
        <div id="cropArea" style="display:none; margin-top:14px;">
          <div class="field">
            <label>Crop (drag to position, pinch/scroll to zoom)</label>
            <div style="max-width:520px;">
              <img id="photoToCrop" alt="To crop" style="max-width:100%; border-radius:12px; border:1px solid rgba(0,0,0,.12);">
            </div>
          </div>
        </div>

        {{-- Hidden field that will be submitted --}}
        <input type="hidden" name="profile_photo_cropped" id="profile_photo_cropped" value="{{ old('profile_photo_cropped') }}">

        

        <p style="margin:10px 0 0; color:#6b7280; font-size:13px;">
          After you submit, you’ll get a Success page where you can <strong>View / Download your PDF</strong>.
        </p>

<div class="actions between">
          <button type="button" id="back5" class="btn ghost">Back</button>

          <div style="display:flex; gap:10px; align-items:center;">
            <button type="submit" class="btn primary">Submit</button>
          </div>
        </div>
      </section>

</form>

  @endif {{-- ✅ end prereg_saved else --}}
</div>


  {{-- CropperJS (CDN) --}}
  <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
  <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
(function () {

  // =========================
  // Wizard (Steps 1-5)
  // =========================
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');
  const step4 = document.getElementById('step4');
  const step5 = document.getElementById('step5');

  const tab1 = document.getElementById('tab1');
  const tab2 = document.getElementById('tab2');
  const tab3 = document.getElementById('tab3');
  const tab4 = document.getElementById('tab4');
  const tab5 = document.getElementById('tab5');

  const next1 = document.getElementById('next1');
  const next2 = document.getElementById('next2');
  const next3 = document.getElementById('next3');
  const next4 = document.getElementById('next4');

  const back2 = document.getElementById('back2');
  const back3 = document.getElementById('back3');
  const back4 = document.getElementById('back4');
  const back5 = document.getElementById('back5');


  // ✅ Address dropdown elements (PSGC)
  const regionSel = document.getElementById('region_psgc');
  const provSel   = document.getElementById('province_psgc');
  const citySel   = document.getElementById('citymun_psgc');
  const brgySel   = document.getElementById('brgy_psgc');
  const zipInput  = document.getElementById('zip_code');


  // ---------
    // ✅ PSGC Cascading Dropdowns
    // ---------
    const resetSelect = (sel, placeholder) => {
      if (!sel) return;
      sel.innerHTML = `<option value="">${placeholder}</option>`;
    };

    const setDisabled = (sel, disabled) => {
      if (!sel) return;
      if (disabled) sel.setAttribute('disabled', 'disabled');
      else sel.removeAttribute('disabled');
    };

    async function loadProvinces(region) {
      resetSelect(provSel, 'Loading provinces...');
      resetSelect(citySel, 'Select city/municipality');
      resetSelect(brgySel, 'Select barangay');
      if (zipInput) zipInput.value = '';

      if (!region) {
        resetSelect(provSel, 'Select region first');
        setDisabled(provSel, true);
        setDisabled(citySel, true);
        setDisabled(brgySel, true);
        return;
      }

      const res = await fetch(`/geo/provinces/${region}`);
      const data = await res.json();

      resetSelect(provSel, 'Select province');
      data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.psgc_code;
        opt.textContent = p.name;
        provSel.appendChild(opt);
      });

      // province is optional (NCR), but enable selection
      setDisabled(provSel, false);

      // city requires either province (normal) or region (NCR fallback handled in controller? not yet)
      resetSelect(citySel, 'Select province first');
      setDisabled(citySel, true);
      resetSelect(brgySel, 'Select city/municipality first');
      setDisabled(brgySel, true);
    }

    async function loadCities(province) {
      resetSelect(citySel, 'Loading cities...');
      resetSelect(brgySel, 'Select barangay');
      if (zipInput) zipInput.value = '';

      if (!province) {
        // NCR case: province can be blank, but city is still required.
        // For NCR, cities are stored with province_psgc NULL; our endpoint currently needs province.
        // So we’ll keep it simple: require user to choose province unless NCR is handled via a separate endpoint.
        resetSelect(citySel, 'Select province (NCR handled later)');
        setDisabled(citySel, true);
        setDisabled(brgySel, true);
        return;
      }

      const res = await fetch(`/geo/cities/${province}`);
      const data = await res.json();

      resetSelect(citySel, 'Select city/municipality');
      data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.psgc_code;
        opt.textContent = `${c.name} (${c.geo_level})`;
        opt.dataset.zip = (c.zip_code ?? '');
        citySel.appendChild(opt);
      });

      setDisabled(citySel, false);
      resetSelect(brgySel, 'Select city/municipality first');
      setDisabled(brgySel, true);
    }

    async function loadBarangays(city) {
      resetSelect(brgySel, 'Loading barangays...');

      if (!city) {
        resetSelect(brgySel, 'Select city/municipality first');
        setDisabled(brgySel, true);
        if (zipInput) zipInput.value = '';
        return;
      }

      // zip auto-fill (will be blank until zip_code values exist)
      if (zipInput) {
        const opt = citySel.options[citySel.selectedIndex];
        zipInput.value = opt?.dataset?.zip || '';
      }

      const res = await fetch(`/geo/barangays/${city}`);
      const data = await res.json();

      resetSelect(brgySel, 'Select barangay');
      data.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.psgc_code;
        opt.textContent = b.name;
        brgySel.appendChild(opt);
      });

      setDisabled(brgySel, false);
    }

    if (regionSel && provSel && citySel && brgySel) {
      // Initial disable state (only province/city/brgy)
      setDisabled(provSel, !regionSel.value);
      setDisabled(citySel, true);
      setDisabled(brgySel, true);

      regionSel.addEventListener('change', async () => {
        await loadProvinces(regionSel.value);
      });

      provSel.addEventListener('change', async () => {
        await loadCities(provSel.value);
      });

      citySel.addEventListener('change', async () => {
        await loadBarangays(citySel.value);
      });

      // If old() values exist, try to restore chain (best effort)
      const oldRegion = @json(old('region_psgc'));
      const oldProv   = @json(old('province_psgc'));
      const oldCity   = @json(old('citymun_psgc'));
      const oldBrgy   = @json(old('brgy_psgc'));

      (async () => {
        if (oldRegion) {
          regionSel.value = oldRegion;
          await loadProvinces(oldRegion);

          if (oldProv) {
            provSel.value = oldProv;
            await loadCities(oldProv);

            if (oldCity) {
              citySel.value = oldCity;
              await loadBarangays(oldCity);

              if (oldBrgy) {
                brgySel.value = oldBrgy;
              }
            }
          }
        }
      })();
    }



  function setEnabled(section, enabled) {
    if (!section) return;
    section.querySelectorAll('input, select, textarea').forEach(el => {
      enabled ? el.removeAttribute('disabled') : el.setAttribute('disabled', true);
    });
  }

  function activateTab(n) {
    [tab1, tab2, tab3, tab4, tab5].forEach(t => t && t.classList.remove('active'));
    if (n === 1 && tab1) tab1.classList.add('active');
    if (n === 2 && tab2) tab2.classList.add('active');
    if (n === 3 && tab3) tab3.classList.add('active');
    if (n === 4 && tab4) tab4.classList.add('active');
    if (n === 5 && tab5) tab5.classList.add('active');
  }

  function showStep(n) {
    if (step1) step1.style.display = n === 1 ? 'block' : 'none';
    if (step2) step2.style.display = n === 2 ? 'block' : 'none';
    if (step3) step3.style.display = n === 3 ? 'block' : 'none';
    if (step4) step4.style.display = n === 4 ? 'block' : 'none';
    if (step5) step5.style.display = n === 5 ? 'block' : 'none';

    setEnabled(step1, n === 1);
    setEnabled(step2, n === 2);
    setEnabled(step3, n === 3);
    setEnabled(step4, n === 4);
    setEnabled(step5, n === 5);

    activateTab(n);
  }

  next1 && (next1.onclick = () => showStep(2));
  next2 && (next2.onclick = () => showStep(3));
  next3 && (next3.onclick = () => showStep(4));
  next4 && (next4.onclick = () => showStep(5));

  back2 && (back2.onclick = () => showStep(1));
  back3 && (back3.onclick = () => showStep(2));
  back4 && (back4.onclick = () => showStep(3));
  back5 && (back5.onclick = () => showStep(4));

  // =========================
  // Step 5: Photo upload + square crop (2x2 print)
  // =========================
  const photoInput  = document.getElementById('profile_photo');
  const cropArea    = document.getElementById('cropArea');
  const photoToCrop = document.getElementById('photoToCrop');
  const photoPrev   = document.getElementById('photoPreview');
  const btnCrop     = document.getElementById('btnCropPhoto');
  const btnClear    = document.getElementById('btnClearPhoto');
  const hiddenCrop  = document.getElementById('profile_photo_cropped');

  let cropper = null;

  function resetPhoto() {
    try {
      if (cropper) { cropper.destroy(); cropper = null; }
    } catch (e) {}
    if (photoInput) photoInput.value = '';
    if (photoToCrop) photoToCrop.src = '';
    if (photoPrev) {
      photoPrev.src = '';
      photoPrev.style.display = 'none';
    }
    if (hiddenCrop) hiddenCrop.value = '';
    if (cropArea) cropArea.style.display = 'none';
    if (btnCrop) btnCrop.style.display = 'none';
    if (btnClear) btnClear.style.display = 'none';
  }

  photoInput && photoInput.addEventListener('change', (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) return resetPhoto();

    if (!/^image\//.test(file.type)) {
      alert('Please upload an image file.');
      return resetPhoto();
    }

    const reader = new FileReader();
    reader.onload = () => {
      if (!photoToCrop) return;
      photoToCrop.src = reader.result;
      if (cropArea) cropArea.style.display = 'block';

      setTimeout(() => {
        try {
          if (cropper) { cropper.destroy(); cropper = null; }
          cropper = new Cropper(photoToCrop, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            background: false,
          });

          if (btnCrop) btnCrop.style.display = 'inline-block';
          if (btnClear) btnClear.style.display = 'inline-block';
        } catch (err) {
          console.error(err);
        }
      }, 50);
    };
    reader.readAsDataURL(file);
  });

  btnCrop && btnCrop.addEventListener('click', () => {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({
      width: 600,
      height: 600,
      imageSmoothingQuality: 'high'
    });
    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

    if (hiddenCrop) hiddenCrop.value = dataUrl;

    if (photoPrev) {
      photoPrev.src = dataUrl;
      photoPrev.style.display = 'block';
    }
  });

  btnClear && btnClear.addEventListener('click', resetPhoto);

  
  // =========================
  // Step 2: Contact Person autofill (Father/Mother checkboxes)
  // - Copies Father/Mother fields into Contact Person fields
  // - Greys out (locks) Contact Person fields when autofilled
  // - Disables the other checkbox while one is selected
  // =========================
  const cbFather = document.getElementById('cp_use_father');
  const cbMother = document.getElementById('cp_use_mother');

  const cpBlock = document.getElementById('contact_person_block');
  const cpFields = cpBlock ? cpBlock.querySelectorAll('input[name^="guardians[2]"]') : [];

  function q(name) {
    return document.querySelector(`[name="${name}"]`);
  }

  function setLocked(el, locked) {
    if (!el) return;
    if (locked) {
      // store previous value once
      if (el.dataset.prevValue === undefined) el.dataset.prevValue = el.value ?? '';
      el.setAttribute('readonly', 'readonly');
      el.style.background = '#f3f4f6';
      el.style.color = '#374151';
      el.style.borderColor = 'rgba(118,0,188,.25)';
    } else {
      el.removeAttribute('readonly');
      el.style.background = '';
      el.style.color = '';
      el.style.borderColor = '';
    }
  }

  function lockContactPerson(locked) {
    cpFields.forEach(el => setLocked(el, locked));
  }

  function fillFromGuardian(idx) {
    // map guardian idx -> contact person idx=2
    const map = [
      ['guardFNAME', 'guardFNAME'],
      ['guardMname', 'guardMname'],
      ['guardLname', 'guardLname'],
      ['contact_number', 'contact_number'],
      ['address', 'address'],
    ];
    map.forEach(([fromKey, toKey]) => {
      const from = q(`guardians[${idx}][${fromKey}]`);
      const to   = q(`guardians[2][${toKey}]`);
      if (!to) return;
      to.value = from ? (from.value ?? '') : '';
    });
  }

  function restoreContactPersonIfAny() {
    // If fields were previously locked, restore whatever user had typed before autofill
    cpFields.forEach(el => {
      if (el.dataset.prevValue !== undefined) {
        el.value = el.dataset.prevValue;
        delete el.dataset.prevValue;
      }
    });
  }

  function applyCheckboxState() {
    const useFather = !!(cbFather && cbFather.checked);
    const useMother = !!(cbMother && cbMother.checked);

    if (useFather && cbMother) {
      cbMother.checked = false;
      cbMother.disabled = true;
    } else if (!useFather && cbMother) {
      cbMother.disabled = false;
    }

    if (useMother && cbFather) {
      cbFather.checked = false;
      cbFather.disabled = true;
    } else if (!useMother && cbFather) {
      cbFather.disabled = false;
    }

    if (useFather) {
      fillFromGuardian(0);
      lockContactPerson(true);
      return;
    }

    if (useMother) {
      fillFromGuardian(1);
      lockContactPerson(true);
      return;
    }

    // none selected
    lockContactPerson(false);
    restoreContactPersonIfAny();
  }

  cbFather && cbFather.addEventListener('change', applyCheckboxState);
  cbMother && cbMother.addEventListener('change', applyCheckboxState);

  // Run once on load (handles old() state or back button)
  applyCheckboxState();


// =========================
  // Submit: ensure disabled fields are enabled before POST
  // =========================
  document.querySelector('form')?.addEventListener('submit', () => {
    // If user selected a photo but didn’t click "Crop", auto-crop to square before saving
    try {
      if (typeof cropper !== 'undefined' && cropper && hiddenCrop && !hiddenCrop.value) {
        const canvas = cropper.getCroppedCanvas({
          width: 600,
          height: 600,
          imageSmoothingQuality: 'high'
        });
        hiddenCrop.value = canvas.toDataURL('image/jpeg', 0.92);
      }
    } catch (e) {}

    document.querySelectorAll('input, select, textarea')
      .forEach(el => el.removeAttribute('disabled'));
  });

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
.field.short { max-width: 520px; }
.field.spacer { visibility: hidden; }

label { font-size: 12px; font-weight: 600; margin-bottom: 6px; color:#374151; }

input, select {
  width:100%; height: 40px; padding: 8px 10px; font-size: 14px;
  border-radius: 10px; border: 1px solid #d1d5db; background:#fff;
}

.hint { margin-top: 6px; font-size: 11px; color:#6b7280; }

h2 { margin: 16px 0 14px; font-size: 16px; font-weight: 700; }

.actions { margin-top: 18px; display:flex; justify-content:flex-end; }
.actions.between { justify-content: space-between; }

.btn { padding: 10px 16px; border-radius: 10px; border: none; cursor: pointer; text-decoration: none; display:inline-block; }
.btn.primary { background:#7c3aed; color:#fff; }
.btn.ghost { background:#e5e7eb; color:#111827; }

.alert { margin: 10px 0 16px; padding: 10px 14px; border-radius: 10px; }
.alert.success { background:#ecfdf5; color:#065f46; }
.alert.error { background:#fef2f2; color:#991b1b; }

.ack {
  margin: 16px 0 18px;
  padding: 16px;
  border-radius: 14px;
  border: 1px solid #c7d2fe;
  background: #eef2ff;
}
.ack-title { margin: 0 0 6px; font-size: 18px; font-weight: 800; }
.ack-sub { margin: 0 0 12px; color: #374151; }
.ack-no { font-weight: 800; color: #111827; }
.ack-actions { display: flex; gap: 10px; flex-wrap: wrap; }

@media (max-width: 900px) { .grid3 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .grid3, .grid2 { grid-template-columns: 1fr; } .field.short{max-width:100%;} }
</style>
@endsection
