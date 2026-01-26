<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use App\Models\Region;
use App\Models\Province;
use App\Models\CityMunicipality;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PreRegistrationController extends Controller
{
    // ✅ GRID / INBOX
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = StudentInfo::query();

        // SEARCH FILTER
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ApplicantNum', 'like', "%{$search}%")
                  ->orWhere('LastName', 'like', "%{$search}%")
                  ->orWhere('FirstName', 'like', "%{$search}%")
                  ->orWhere('FirstProgramChoice', 'like', "%{$search}%");
            });
        }

        // ORDER: newest first
        $applicants = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view(
            'admission.pre_registration.pre-reg_grid',
            compact('applicants', 'search')
        );
    }

    // ✅ MANUAL PRE-REGISTRATION FORM
    public function create()
    {
        // Load regions for the first dropdown
        $regions = Region::orderBy('name')->get(['psgc_code', 'name']);

        return view('admission.pre_registration.manual', compact('regions'));
    }

    // ✅ AJAX: Provinces by Region
    public function provinces(string $region_psgc)
    {
        return Province::where('region_psgc', $region_psgc)
            ->orderBy('name')
            ->get(['psgc_code', 'name']);
    }

    // ✅ AJAX: Cities/Municipalities by Province
    public function cities(string $province_psgc)
    {
        return CityMunicipality::where('province_psgc', $province_psgc)
            ->orderBy('name')
            ->get(['psgc_code', 'name', 'geo_level', 'zip_code']);
    }

    // ✅ AJAX: Barangays by City/Municipality
    public function barangays(string $citymun_psgc)
    {
        return Barangay::where('citymun_psgc', $citymun_psgc)
            ->orderBy('name')
            ->get(['psgc_code', 'name']);
    }

    // ✅ STORE PRE-REGISTRATION
    public function store(Request $request)
    {
        $validated = $request->validate([
            'FirstName'   => ['required', 'string', 'max:50'],
            'MidName'     => ['nullable', 'string', 'max:50'],
            'LastName'    => ['required', 'string', 'max:50'],
            'Suffix'      => ['nullable', 'string', 'max:10'],

            'ContactNo'   => ['required', 'string', 'max:20'],
            'email'       => ['required', 'email', 'max:50'],
            'Birthdate'   => ['required', 'date'],
            'Gender'      => ['required', 'string', 'max:10'],
            'Citizenship' => ['required', 'string', 'max:20'],
            'CivilStatus' => ['required', 'string', 'max:20'],
            'Religion'    => ['required', 'string', 'max:50'],

            'Bloodtype'   => ['nullable', 'string', 'max:10'],
            'Height'      => ['nullable', 'string', 'max:10'],
            'Weight'      => ['nullable', 'string', 'max:10'],

            
// ✅ Step 2 (Parent / Guardian) — array-based (3 rows)
'guardians' => ['required', 'array', 'size:3'],

// Father (index 0)
'guardians.0.relationship'      => ['required', 'in:Father'],
'guardians.0.guardFNAME'        => ['required', 'string', 'max:50'],
'guardians.0.guardMname'        => ['nullable', 'string', 'max:50'],
'guardians.0.guardLname'        => ['required', 'string', 'max:50'],
'guardians.0.contact_number'    => ['required', 'string', 'max:20'],
'guardians.0.occupation'        => ['nullable', 'string', 'max:100'],
'guardians.0.address'           => ['nullable', 'string', 'max:255'],
'guardians.0.annual_income'     => ['nullable', 'numeric', 'min:0'],
'guardians.0.highest_education' => ['nullable', 'string', 'max:100'],

// Mother (index 1)
'guardians.1.relationship'      => ['required', 'in:Mother'],
'guardians.1.guardFNAME'        => ['required', 'string', 'max:50'],
'guardians.1.guardMname'        => ['nullable', 'string', 'max:50'],
'guardians.1.guardLname'        => ['required', 'string', 'max:50'],
'guardians.1.contact_number'    => ['required', 'string', 'max:20'],
'guardians.1.occupation'        => ['nullable', 'string', 'max:100'],
'guardians.1.address'           => ['nullable', 'string', 'max:255'],
'guardians.1.annual_income'     => ['nullable', 'numeric', 'min:0'],
'guardians.1.highest_education' => ['nullable', 'string', 'max:100'],

// Emergency Contact (index 2)
'guardians.2.relationship'      => ['required', 'string', 'max:50'],
'guardians.2.guardFNAME'        => ['required', 'string', 'max:50'],
'guardians.2.guardMname'        => ['nullable', 'string', 'max:50'],
'guardians.2.guardLname'        => ['required', 'string', 'max:50'],
'guardians.2.contact_number'    => ['required', 'string', 'max:20'],
'guardians.2.address'           => ['nullable', 'string', 'max:255'],

'PrimarySchool'           => ['required', 'string', 'max:100'],
            'PrimarySchool_Address'   => ['required', 'string', 'max:100'],
            'YearGradPrimary'         => ['required', 'string', 'max:4'],

            'SecondarySchool'         => ['required', 'string', 'max:100'],
            'SecondarySchool_Address' => ['required', 'string', 'max:100'],
            'YearGradSecondary'       => ['required', 'string', 'max:4'],

            'LastSchoolAttended'      => ['required', 'string', 'max:100'],

            'FirstProgramChoice'      => ['required', 'string', 'max:150'],
            'SecondProgramChoice'     => ['required', 'string', 'max:150', 'different:FirstProgramChoice'],

            // ✅ Address dropdown PSGC codes (add these fields in your manual.blade.php form)
            'region_psgc'   => ['required', 'size:10'],
            'province_psgc' => ['nullable', 'size:10'], // NCR may be NULL
            'citymun_psgc'  => ['required', 'size:10'],
            'brgy_psgc'     => ['required', 'size:10'],

            // ✅ Applicant Type (Step 4)
            'applicant_type' => ['required', 'in:Freshman,Transferee'],

            // ✅ Step 5 (optional) - cropped photo data URL (base64)
            'profile_photo_cropped' => ['nullable', 'string'],
        ]);

        

        
        // ✅ IMPORTANT: guardians[] is NOT a column in tbl_student_info
        // Remove it before creating the student record.
        $studentData = $validated;
        unset($studentData['guardians']);
        $photoDataUrl = $studentData['profile_photo_cropped'] ?? null;
        unset($studentData['profile_photo_cropped']);

$student = DB::transaction(function () use ($studentData, $validated) {
            $student = StudentInfo::create(
                array_merge($studentData, [
                    'application_status' => 'pending',
                ])
                
        );

        

            // =========================
            // STEP 5 — Save profile photo (cropped square)
            // =========================
            if (!empty($photoDataUrl) && str_starts_with($photoDataUrl, 'data:image')) {
                // Expected format: data:image/jpeg;base64,....
                [$meta, $content] = explode(',', $photoDataUrl, 2);
                $ext = str_contains($meta, 'image/png') ? 'png' : 'jpg';

                $bin = base64_decode($content);
                if ($bin !== false) {
                    $dir = 'profile_photos/' . now()->format('Y/m');
                    $name = 'prereg_' . $student->studID . '_' . Str::random(10) . '.' . $ext;
                    $path = $dir . '/' . $name;

                    Storage::disk('public')->put($path, $bin);
                    $student->profile_photo_path = $path;
                    $student->save();
                }
            }

            $appNo = 'APP-' . now()->format('Y') . '-' . str_pad($student->studID, 6, '0', STR_PAD_LEFT);
            $student->ApplicantNum = $appNo;
            $student->stud_number = $appNo;
            $student->save();

            
// =========================
// STEP 2 — Save guardians (Father, Mother, Emergency Contact)
// Array-based: guardians[0], guardians[1], guardians[2]
// =========================

// Prevent duplicates if the form is resubmitted
DB::table('tbl_guardian')->where('studID', $student->studID)->delete();

foreach ($validated['guardians'] as $g) {
    DB::table('tbl_guardian')->insert([
        'studID'            => $student->studID,
        'guardFNAME'        => $g['guardFNAME'],
        'guardMname'        => $g['guardMname'] ?? null,
        'guardLname'        => $g['guardLname'],
        'contact_number'    => $g['contact_number'],
        'relationship'      => $g['relationship'],
        'occupation'        => $g['occupation'] ?? null,
        'address'           => $g['address'] ?? null,
        'annual_income'     => $g['annual_income'] ?? null,
        'highest_education' => $g['highest_education'] ?? null,
    ]);
}




            return $student;
        });

        return redirect()->route('admission.prereg.success', ['studID' => $student->studID]);}

    // ✅ SUCCESS page (after submission)
    public function success($studID)
    {
        $student = StudentInfo::where('studID', $studID)->firstOrFail();
        return view('admission.pre_registration.success', compact('student'));
    }

    // ✅ PDF (view in browser or download)
    public function pdf($studID)
    {
        $student = StudentInfo::where('studID', $studID)->firstOrFail();

        // Locate photo file for DomPDF (use file:// path for best reliability)
        $photoFilePath = null;
        if (!empty($student->profile_photo_path)) {
            $fullPath = storage_path('app/public/' . $student->profile_photo_path);
            if (file_exists($fullPath)) {
                $photoFilePath = $fullPath;
            }
        }

        $pdf = Pdf::loadView('admission.pre_registration.prereg_pdf', [
            'student' => $student,
            'photoFilePath' => $photoFilePath,
        ])->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

        // stream = opens in browser (best for mobile)
        return $pdf->stream("prereg_{$student->studID}.pdf");

        // or download:
        // return $pdf->download("prereg_{$student->studID}.pdf");
    }

}
