<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>ASCEND Pre-registration PDF</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111827; }
    h1 { font-size: 16px; margin: 0 0 6px; }
    .muted { color:#6b7280; margin:0 0 14px; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 7px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
    .key { width: 34%; font-weight: 700; color:#374151; }
    .photo { width: 2in; height: 2in; border: 1px solid #d1d5db; border-radius: 10px; object-fit: cover; }
    .box { border:1px solid #e5e7eb; border-radius: 12px; padding: 10px; }
  </style>
</head>
<body>

  <h1>ASCEND Pre-registration</h1>
  <p class="muted">
    Applicant No: {{ $student->ApplicantNum ?? '—' }}
  </p>

  <table>
    <tr>
      <td class="key">2x2 Photo</td>
      <td>
        @if(!empty($photoFilePath))
          <img src="file://{{ $photoFilePath }}" class="photo" alt="2x2 Photo">
        @else
          <span class="muted">No photo provided</span>
        @endif
      </td>
    </tr>

    <tr><td class="key">Last Name</td><td>{{ $student->LastName }}</td></tr>
    <tr><td class="key">First Name</td><td>{{ $student->FirstName }}</td></tr>
    <tr><td class="key">Middle Name</td><td>{{ $student->MidName ?? '—' }}</td></tr>
    <tr><td class="key">Suffix</td><td>{{ $student->Suffix ?? '—' }}</td></tr>

    <tr><td class="key">Contact No</td><td>{{ $student->ContactNo }}</td></tr>
    <tr><td class="key">Email</td><td>{{ $student->email }}</td></tr>
    <tr><td class="key">Birthdate</td><td>{{ $student->Birthdate }}</td></tr>
    <tr><td class="key">Gender</td><td>{{ $student->Gender }}</td></tr>

    <tr><td class="key">Citizenship</td><td>{{ $student->Citizenship }}</td></tr>
    <tr><td class="key">Civil Status</td><td>{{ $student->CivilStatus }}</td></tr>
    <tr><td class="key">Religion</td><td>{{ $student->Religion }}</td></tr>

    <tr><td class="key">Primary School</td><td>{{ $student->PrimarySchool }}</td></tr>
    <tr><td class="key">Year Graduated (Primary)</td><td>{{ $student->YearGradPrimary }}</td></tr>
    <tr><td class="key">Secondary School</td><td>{{ $student->SecondarySchool }}</td></tr>
    <tr><td class="key">Year Graduated (Secondary)</td><td>{{ $student->YearGradSecondary }}</td></tr>

    <tr><td class="key">First Program Choice</td><td>{{ $student->FirstProgramChoice }}</td></tr>
    <tr><td class="key">Second Program Choice</td><td>{{ $student->SecondProgramChoice }}</td></tr>
  </table>

</body>
</html>
