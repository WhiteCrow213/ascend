<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>College Admissions and Testing Application Form</title>
  <style>

/* ===== Screen Viewer Tweaks ===== */
.screen-wrap{
  max-width: 1100px;
  margin: 0 auto;
  padding: 16px;
  background: #f6f7fb;
}
.viewer-card{
  background:#ffffff;
  border-radius: 14px;
  box-shadow: 0 12px 30px rgba(0,0,0,.10);
  overflow:hidden;
}

    @page { margin: 14mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color:#111827; }

    .topbar{
      background:#0f9d58;
      height: 16px;
      border-radius: 2px;
    }
    .header{
      border: 1px solid #0f9d58;
      padding: 10px 10px 8px;
      margin-top: 8px;
    }
    .header h1{
      margin: 0;
      font-size: 15px;
      letter-spacing: .3px;
      text-align: center;
      font-weight: 800;
    }
    .school{
      text-align:center;
      margin-top: 2px;
      font-weight: 700;
      font-size: 12px;
    }
    .addr{
      text-align:center;
      margin-top: 1px;
      font-size: 10px;
      color:#374151;
    }

    .grid{ width:100%; border-collapse:collapse; margin-top: 8px; }
    .grid td{ vertical-align: top; }

    .box{
      border:1px solid #111827;
      padding: 8px;
      border-radius: 2px;
    }
    .box-tight{ padding: 6px; }

    .section-title{
      background:#0f9d58;
      color:#fff;
      font-weight:800;
      padding: 4px 6px;
      font-size: 11px;
      letter-spacing:.2px;
      margin: 0 0 6px;
    }

    .line-row{ width:100%; border-collapse:collapse; }
    .line-row td{ padding: 2px 6px; }
    .label{ width: 30%; font-weight:700; }
    .line{
      border-bottom: 1px solid #111827;
      height: 14px;
      padding-left: 6px;
    }
    .line.small{ height: 12px; }
    .hint{ font-size: 9px; color:#374151; margin-top: 1px; }

    .rightcol{ width: 34%; padding-left: 8px; }
    .leftcol{ width: 66%; }

    .photo-box{
      border: 1px dashed #111827;
      height: 170px;
      display: block;
      position: relative;
      text-align: center;
      padding-top: 6px;
    }
    .photo-img{
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 1px solid #111827;
      display: inline-block;
      margin-top: 6px;
    }
    .photo-note{ font-size: 10px; font-weight:700; margin-top: 2px; }
    .muted{ color:#6b7280; font-size: 10px; }

    ul{ margin: 6px 0 0 16px; padding: 0; }
    li{ margin: 0 0 3px; }

    .footer{
      margin-top: 8px;
      font-size: 10px;
    }
    .cutline{
      margin: 10px 0;
      border-top: 1px dashed #111827;
      text-align:center;
      font-size: 9px;
      color:#374151;
      padding-top: 3px;
    }
    .sigrow{
      width:100%;
      border-collapse:collapse;
      margin-top: 6px;
    }
    .sigrow td{ width:50%; padding: 2px 6px; vertical-align: bottom; }
    .sigline{ border-bottom: 1px solid #111827; height: 16px; }
  </style>
</head>
<body>
<div class="screen-wrap">
  <div class="viewer-card">

  <div class="topbar"></div>

  <div class="header">
    <h1>COLLEGE ADMISSIONS AND TESTING APPLICATION FORM</h1>
    <div class="school">DON CARLOS POLYTECHNIC COLLEGE</div>
    <div class="addr">Purok 2, Poblacion Norte, Don Carlos, Bukidnon</div>
  </div>

  <table class="grid">
    <tr>
      <!-- LEFT -->
      <td class="leftcol">

        <table class="line-row">
          <tr>
            <td class="label">Examinee Number :</td>
            <td class="line"></td>
          </tr>
          <tr>
            <td class="label">Examination Time :</td>
            <td class="line"></td>
          </tr>
          <tr>
            <td class="label">Examination Date :</td>
            <td class="line"></td>
          </tr>
          <tr>
            <td class="label">Room Assignment :</td>
            <td class="line"></td>
          </tr>
        </table>

        <!-- PERSONAL DATA -->
        <div class="box" style="margin-top:8px;">
          <div class="section-title">PERSONAL DATA</div>

          @php
            $fullName = trim(($student->LastName ?? '').', '.($student->FirstName ?? '').' '.($student->MidName ?? ''));
            $age = null;
            try {
              if (!empty($student->Birthdate)) {
                $age = \Carbon\Carbon::parse($student->Birthdate)->age;
              }
            } catch (\Exception $e) { $age = null; }
          @endphp

          <table class="line-row">
            <tr>
              <td class="label">Name :</td>
              <td class="line">{{ $fullName }}</td>
            </tr>
            <tr>
              <td></td>
              <td class="hint">(Last Name) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Given Name) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Middle Name)</td>
            </tr>

            <tr>
              <td class="label">Age :</td>
              <td class="line small">{{ $age ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Sex :</td>
              <td class="line small">{{ $student->Gender ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Date of Birth :</td>
              <td class="line small">{{ $student->Birthdate ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Place of Birth :</td>
              <td class="line small">{{ $student->place_of_birth ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Nationality :</td>
              <td class="line small">{{ $student->Citizenship ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Religion :</td>
              <td class="line small">{{ $student->Religion ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Contact Number :</td>
              <td class="line small">{{ $student->ContactNo ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Email Address :</td>
              <td class="line small">{{ $student->email ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Present Address :</td>
              <td class="line small">{{ $presentAddress ?? '' }}</td>
            </tr>

            <tr>
              <td class="label">Permanent Address :</td>
              <td class="line small"></td>
            </tr>

            <tr>
              <td class="label">Parent/ Guardian Name :</td>
              <td class="line small">
                @php
                  $pg = trim(implode(' / ', array_filter([$fatherName ?? null, $motherName ?? null])));
                @endphp
                {{ $pg }}
              </td>
            </tr>
          </table>
        </div>

        <!-- EDUCATIONAL INFORMATION -->
        <div class="box" style="margin-top:8px;">
          <div class="section-title">EDUCATIONAL INFORMATION</div>

          <div style="font-weight:800; margin: 2px 0 4px;">SECONDARY</div>
          <table class="line-row">
            <tr>
              <td class="label">Name of School :</td>
              <td class="line">{{ $student->SecondarySchool ?? '' }}</td>
            </tr>
            <tr>
              <td class="label">School Address :</td>
              <td class="line">{{ $student->SecondarySchool_Address ?? '' }}</td>
            </tr>
            <tr>
              <td class="label">Date Graduated :</td>
              <td class="line">{{ $student->YearGradSecondary ?? '' }}</td>
            </tr>
          </table>

          <div style="font-weight:800; margin: 10px 0 4px;">TERTIARY (for transferees)</div>
          <table class="line-row">
            <tr>
              <td class="label">Name of School :</td>
              <td class="line">{{ $student->LastSchoolAttended ?? '' }}</td>
            </tr>
            <tr>
              <td class="label">School Address :</td>
              <td class="line"></td>
            </tr>
            <tr>
              <td class="label">Date Last Attended :</td>
              <td class="line"></td>
            </tr>
          </table>
        </div>

        <!-- PREFERRED DEGREE PROGRAM -->
        <div class="box" style="margin-top:8px;">
          <div class="section-title">PREFERRED DEGREE PROGRAM</div>
          <table class="line-row">
            <tr>
              <td class="label">First Choice :</td>
              <td class="line">{{ $student->FirstProgramChoice ?? '' }}</td>
            </tr>
            <tr>
              <td class="label">Second Choice :</td>
              <td class="line">{{ $student->SecondProgramChoice ?? '' }}</td>
            </tr>
          </table>
        </div>

        <div class="footer">
          I affirm that all the information supplied in this admissions and testing application form are true,
          complete and accurate and I am aware that giving false information will disqualify me from admission.
        </div>

        <table class="sigrow">
          <tr>
            <td>
              <div class="sigline"></div>
              <div style="font-size:10px;">Signature over printed name of examinee</div>
            </td>
            <td>
              <div class="sigline"></div>
              <div style="font-size:10px;">Date of application</div>
            </td>
          </tr>
        </table>

        <div class="cutline">To be cut by admission and testing personnel</div>

        <div class="box box-tight">
          <div style="font-weight:800; margin-bottom: 6px;">Examinee’s Copy</div>
          <table class="line-row">
            <tr><td class="label">Name of Examinee :</td><td class="line"></td></tr>
            <tr><td class="label">Examinee Number :</td><td class="line"></td></tr>
            <tr><td class="label">Examination Date :</td><td class="line"></td></tr>
            <tr><td class="label">Room Assignment :</td><td class="line"></td></tr>
          </table>

          <table class="sigrow">
            <tr>
              <td>
                <div class="sigline"></div>
                <div style="font-size:10px;">Signature of Examinee</div>
              </td>
              <td>
                <div class="sigline"></div>
                <div style="font-size:10px; text-align:right;">Director, Admissions and Testing</div>
              </td>
            </tr>
          </table>
        </div>

      </td>

      <!-- RIGHT -->
      <td class="rightcol">

        <div class="box box-tight">
          <div style="font-weight:800; margin-bottom: 4px;">DCPC-CAT Result</div>
          <table class="line-row">
            <tr><td class="label">Score :</td><td class="line"></td></tr>
            <tr><td class="label">Percentage Rating :</td><td class="line"></td></tr>
            <tr><td class="label">Remarks :</td><td class="line"></td></tr>
          </table>
          <div style="margin-top:8px;">
            <div class="sigline"></div>
            <div style="font-size:10px; text-align:right;">Director, Admission and Testing</div>
          </div>
        </div>

        <div class="photo-box" style="margin-top: 8px;">
          <div class="photo-note">2x2 Photo</div>
          <div class="muted">In White Background With Collar</div>

          @if(!empty($photoDataUri))
            <img src="{{ $photoDataUri }}" class="photo-img" alt="2x2 Photo">
          @else
            <div style="margin-top: 18px;" class="muted">No photo provided</div>
          @endif
        </div>

        <div class="box" style="margin-top: 8px;">
          <div style="font-weight:800; margin-bottom: 6px; text-align:center;">PROGRAMS OFFERED<br>DEGREE PROGRAMS</div>
          <ul>
            <li>Bachelor of Science in Criminology</li>
            <li>Bachelor of Secondary Education<br><span style="font-weight:700;">English, Filipino, Mathematics, Science</span></li>
            <li>Bachelor of Elementary Education</li>
          </ul>
        </div>

        <div class="box" style="margin-top: 8px;">
          <div style="font-weight:800; margin-bottom: 6px; text-align:center;">REQUIREMENTS</div>
          <ul>
            <li>Accomplished admissions and testing application form.</li>
            <li>One recent 2X2 Photo.</li>
            <li>One recent 1x1 photo.</li>
            <li>One valid ID with Signature</li>
            <li>One Black ballpen</li>
            <li>One pencil</li>
          </ul>
        </div>

        <div class="photo-box" style="margin-top: 8px; height: 120px;">
          <div class="photo-note">1x1 Photo</div>
          <div class="muted">In White Background With Collar</div>
        </div>

      </td>
    </tr>
  </table>

  </div>
</div>
</body>
</html>
