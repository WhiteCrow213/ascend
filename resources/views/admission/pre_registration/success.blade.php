@extends('layouts.app')

@section('content')
<div class="wrap">

  <h1>Pre-registration Submitted ✅</h1>
  <p style="color:#6b7280; margin-top:6px;">
    Your application has been saved. You can download or view your printable PDF copy below.
  </p>

  <div style="margin-top:18px; padding:16px; border:1px solid rgba(0,0,0,.08); border-radius:14px; background:#fff;">
    <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:center; justify-content:space-between;">
      <div>
        <div style="font-weight:700; font-size:16px;">
          Applicant No: {{ $student->ApplicantNum ?? '—' }}
        </div>
        <div style="color:#6b7280; margin-top:4px;">
          Student ID: {{ $student->studID }}
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn primary" href="{{ route('admission.prereg.pdf', ['studID' => $student->studID]) }}" target="_blank">
          View PDF
        </a>

        <a class="btn" href="{{ route('admission.prereg.pdf', ['studID' => $student->studID]) }}" download>
          Download PDF
        </a>

        <a class="btn ghost" href="{{ route('admission.prereg.manual') }}">
          New Registration
        </a>
      </div>
    </div>
  </div>

  <p style="margin-top:14px; color:#6b7280; font-size:13px;">
    Tip: On phones, tap <strong>View PDF</strong> then use your browser’s download/share button.
  </p>

</div>
@endsection
