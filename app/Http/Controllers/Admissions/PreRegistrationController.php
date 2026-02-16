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
            'place_of_birth' => ['nullable', 'string', 'max:255'],
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
        $studentData = $validated;
        unset($studentData['guardians']);

        // ✅ Step 5 (photo) comes in as base64 data URL
        $photoDataUrl = $studentData['profile_photo_cropped'] ?? null;
        unset($studentData['profile_photo_cropped']);

        $student = DB::transaction(function () use ($studentData, $validated, $photoDataUrl) {
            // Create student record (force status = pending)
            $student = StudentInfo::create(array_merge($studentData, [
                'application_status' => 'pending',
            ]));

            // =========================
            // STEP 5 — Save profile photo (cropped square)
            // =========================
            if (!empty($photoDataUrl) && str_starts_with($photoDataUrl, 'data:image')) {
                [$meta, $content] = explode(',', $photoDataUrl, 2);
                $ext = str_contains($meta, 'image/png') ? 'png' : 'jpg';

                $bin = base64_decode($content);
                if ($bin !== false) {
                    $dir  = 'profile_photos/' . now()->format('Y/m');
                    $name = 'prereg_' . $student->studID . '_' . Str::random(10) . '.' . $ext;
                    $path = $dir . '/' . $name;

                    Storage::disk('public')->put($path, $bin);
                    $student->profile_photo_path = $path;
                    $student->save();
                }
            }

            // Generate applicant number
            $appNo = 'APP-' . now()->format('Y') . '-' . str_pad($student->studID, 6, '0', STR_PAD_LEFT);
            $student->ApplicantNum = $appNo;
            $student->stud_number  = $appNo;
            $student->save();

            // =========================
            // STEP 2 — Save guardians (Father, Mother, Emergency Contact)
            // =========================
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

        return redirect()->route('admission.prereg.success', ['studID' => $student->studID]);
    }
public function success($studID)
    {
        $student = StudentInfo::where('studID', $studID)->firstOrFail();
        return view('admission.pre_registration.success', compact('student'));
    }

    
    // ✅ PDF (view in browser or download)
    /**
     * =========================
     * VIEWER (HTML, for modal iframe)
     * URL: GET /admission/prereg/{studID}/viewer
     * =========================
     */
    public function viewer($studID)
    {
        $student = StudentInfo::where('studID', $studID)->firstOrFail();

        // -------- Address resolution (PSGC -> names)
        $regionName   = !empty($student->region_psgc)
            ? Region::where('psgc_code', $student->region_psgc)->value('name')
            : null;

        $provinceName = !empty($student->province_psgc)
            ? Province::where('psgc_code', $student->province_psgc)->value('name')
            : null;

        $citymunName  = !empty($student->citymun_psgc)
            ? CityMunicipality::where('psgc_code', $student->citymun_psgc)->value('name')
            : null;

        $brgyName     = !empty($student->brgy_psgc)
            ? Barangay::where('psgc_code', $student->brgy_psgc)->value('name')
            : null;

        $presentAddress = trim(implode(', ', array_filter([
            $student->address_line ?? null,
            $brgyName,
            $citymunName,
            $provinceName,
            $regionName,
        ])));

        // -------- Guardians (Father / Mother)
        $guardians = DB::table('tbl_guardian')
            ->where('studID', $student->studID)
            ->get();

        $father = $guardians->firstWhere('relationship', 'Father');
        $mother = $guardians->firstWhere('relationship', 'Mother');

        $fatherName = $father
            ? trim(implode(' ', array_filter([$father->guardFNAME, $father->guardMname, $father->guardLname])))
            : null;

        $motherName = $mother
            ? trim(implode(' ', array_filter([$mother->guardFNAME, $mother->guardMname, $mother->guardLname])))
            : null;

        // -------- Photo (base64 so it shows in browser + dompdf-style layouts)
        $photoDataUri = null;
        if (!empty($student->profile_photo_path)) {
            $fullPath = storage_path('app/public/' . $student->profile_photo_path);
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $data = base64_encode(file_get_contents($fullPath));
                if (!empty($data)) {
                    $photoDataUri = "data:{$mime};base64,{$data}";
                }
            }
        }

        return view('admission.pre_registration.prereg_viewer', [
            'student'        => $student,
            'photoDataUri'   => $photoDataUri,
            'presentAddress' => $presentAddress,
            'fatherName'     => $fatherName,
            'motherName'     => $motherName,
        ]);
    }


    public function pdf($studID)
    {
        $student = StudentInfo::where('studID', $studID)->firstOrFail();

        // =========================
        // Address display (PSGC -> Names)
        // =========================
        $regionName   = null;
        $provinceName = null;
        $citymunName  = null;
        $brgyName     = null;

        if (!empty($student->region_psgc)) {
            $regionName = Region::where('psgc_code', $student->region_psgc)->value('name');
        }
        if (!empty($student->province_psgc)) {
            $provinceName = Province::where('psgc_code', $student->province_psgc)->value('name');
        }
        if (!empty($student->citymun_psgc)) {
            $citymunName = CityMunicipality::where('psgc_code', $student->citymun_psgc)->value('name');
        }
        if (!empty($student->brgy_psgc)) {
            $brgyName = Barangay::where('psgc_code', $student->brgy_psgc)->value('name');
        }

        $presentAddress = trim(implode(', ', array_filter([
            $student->address_line ?? null,
            $brgyName,
            $citymunName,
            $provinceName,
            $regionName,
        ])));

        // =========================
        // Guardians (Father/Mother display)
        // =========================
        $guardians = DB::table('tbl_guardian')
            ->where('studID', $student->studID)
            ->get();

        $father = $guardians->firstWhere('relationship', 'Father');
        $mother = $guardians->firstWhere('relationship', 'Mother');

        $fatherName = $father ? trim(implode(' ', array_filter([$father->guardFNAME, $father->guardMname, $father->guardLname]))) : null;
        $motherName = $mother ? trim(implode(' ', array_filter([$mother->guardFNAME, $mother->guardMname, $mother->guardLname]))) : null;

        // =========================
        // Photo: embed as base64 Data URI for DomPDF reliability
        // =========================
        $photoDataUri = null;
        if (!empty($student->profile_photo_path)) {
            $fullPath = storage_path('app/public/' . $student->profile_photo_path);
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $data = base64_encode(file_get_contents($fullPath));
                if (!empty($data)) {
                    $photoDataUri = "data:{$mime};base64,{$data}";
                }
            }
        }

        $pdf = Pdf::loadView('admission.pre_registration.prereg_pdf', [
            'student'        => $student,
            'photoDataUri'   => $photoDataUri,
            'presentAddress' => $presentAddress,
            'fatherName'     => $fatherName,
            'motherName'     => $motherName,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("prereg_{$student->studID}.pdf");
        // return $pdf->download("prereg_{$student->studID}.pdf");
    }


}
