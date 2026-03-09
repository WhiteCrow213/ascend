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
use Carbon\Carbon;

class PreRegistrationController extends Controller
{
    // ✅ GRID / INBOX (now reads from tbl_prereg_applicants)
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $query = DB::table('tbl_prereg_applicants')
            // Keep Blade compatibility: use prereg_id as "studID" for routes/modals
            ->selectRaw('tbl_prereg_applicants.*, prereg_id as studID');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ApplicantNum', 'like', "%{$search}%")
                  ->orWhere('LastName', 'like', "%{$search}%")
                  ->orWhere('FirstName', 'like', "%{$search}%")
                  ->orWhere('FirstProgramChoice', 'like', "%{$search}%");
            });
        }


        if ($status !== '' && $status !== 'all') {
            $query->where('application_status', $status);
        }

        $applicants = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admission.pre_registration.pre-reg_grid', compact('applicants', 'search'));
    }

    // ✅ MANUAL PRE-REGISTRATION FORM
    public function create()
    {
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

    // ✅ STORE PRE-REGISTRATION (writes ONLY to tbl_prereg_applicants)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'FirstName'   => ['required', 'string', 'max:50'],
            'MidName'     => ['nullable', 'string', 'max:50'],
            'LastName'    => ['required', 'string', 'max:50'],
            'Suffix'      => ['nullable', 'string', 'max:10'],

            'ContactNo'   => ['required', 'string', 'max:20'],
            'email'       => ['required', 'email', 'max:50'],
            'Birthdate'   => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(16)->format('Y-m-d')],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'Gender'      => ['required', 'string', 'max:10'],
            'Citizenship' => ['required', 'string', 'max:20'],
            'CivilStatus' => ['required', 'string', 'max:20'],
            'Religion'      => ['required', 'string', 'max:50'],
            'ReligionOther' => ['nullable', 'string', 'max:50'],

            'Bloodtype'   => ['nullable', 'string', 'max:10'],
            'Height'      => ['nullable', 'numeric', 'min:0'],
            'Weight'      => ['nullable', 'numeric', 'min:0'],

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

            // Step 3 (Academic)
            'PrimarySchool'           => ['required', 'string', 'max:100'],
            'PrimarySchool_Address'   => ['required', 'string', 'max:100'],
            'YearGradPrimary'         => ['required', 'string', 'max:4'],

            'SecondarySchool'         => ['required', 'string', 'max:100'],
            'SecondarySchool_Address' => ['required', 'string', 'max:100'],
            'YearGradSecondary'       => ['required', 'string', 'max:4'],

            'LastSchoolAttended'      => ['required', 'string', 'max:100'],

            // Step 4 (Programs + Applicant type)
            'FirstProgramChoice'      => ['required', 'string', 'max:150'],
            'SecondProgramChoice'     => ['required', 'string', 'max:150', 'different:FirstProgramChoice'],
            'applicant_type'          => ['required', 'in:Freshman,Transferee'],

            // Address PSGC codes
            'region_psgc'   => ['required', 'string', 'max:10'],
            'province_psgc' => ['nullable', 'string', 'max:10'], // NCR may be NULL
            'citymun_psgc'  => ['required', 'string', 'max:10'],
            'brgy_psgc'     => ['required', 'string', 'max:10'],

            // Step 5 photo (optional) - cropped photo data URL (base64)
            'profile_photo_cropped' => ['nullable', 'string'],
        ], [
            'Birthdate.before_or_equal' => 'Applicant must be at least 16 years old.',
        ]);
        // Enforce Religion "Others" behavior
        if (($validated['Religion'] ?? null) === 'Others') {
            $other = trim((string) $request->input('ReligionOther', ''));
            if ($other === '') {
                return back()
                    ->withErrors(['ReligionOther' => 'Please specify religion.'])
                    ->withInput();
            }
            $validated['Religion'] = $other;
        }

        // ✅ IMPORTANT: tbl_prereg_applicants does NOT have a ReligionOther column.
        // We store the final value in Religion (either selected value, or the "Others" text).
        // So we must drop ReligionOther before insert to avoid SQL "Unknown column" errors.
        unset($validated['ReligionOther']);

        // Auto-set Year Level on prereg
        // Freshman => First Year (IDyearlvl = 1), Transferee => NULL
        $appType = $validated['applicant_type'] ?? null;
        if ($appType === 'Freshman') {
            $validated['IDyearlvl'] = 1;
        } elseif ($appType === 'Transferee') {
            $validated['IDyearlvl'] = null;
        }


        $photoDataUrl = $validated['profile_photo_cropped'] ?? null;
        unset($validated['profile_photo_cropped']);

        // guardians are stored in tbl_guardian, not in prereg table directly
        $guardians = $validated['guardians'];
        unset($validated['guardians']);

        $preregId = DB::transaction(function () use ($validated, $guardians, $photoDataUrl) {

            // ApplicantNum is NOT NULL + UNIQUE, so create a temporary one first, then replace after we get prereg_id.
            $tmpApplicantNum = 'TMP-' . strtoupper(Str::random(12));

            $insert = array_merge($validated, [
                'ApplicantNum'        => $tmpApplicantNum,
                'application_status'  => 'pending',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $preregId = DB::table('tbl_prereg_applicants')->insertGetId($insert, 'prereg_id');

            // Build final applicant number based on prereg_id
            $appNo = 'APP-' . now()->format('Y') . '-' . str_pad((string) $preregId, 6, '0', STR_PAD_LEFT);

            DB::table('tbl_prereg_applicants')
                ->where('prereg_id', $preregId)
                ->update([
                    'ApplicantNum' => $appNo,
                    'updated_at'   => now(),
                ]);

            // Save profile photo if provided
            if (!empty($photoDataUrl) && str_starts_with($photoDataUrl, 'data:image')) {
                [$meta, $content] = explode(',', $photoDataUrl, 2);
                $ext = str_contains($meta, 'image/png') ? 'png' : 'jpg';

                $bin = base64_decode($content);
                if ($bin !== false) {
                    $dir  = 'profile_photos/' . now()->format('Y/m');
                    $name = 'prereg_' . $preregId . '_' . Str::random(10) . '.' . $ext;
                    $path = $dir . '/' . $name;

                    Storage::disk('public')->put($path, $bin);

                    DB::table('tbl_prereg_applicants')
                        ->where('prereg_id', $preregId)
                        ->update([
                            'profile_photo_path' => $path,
                            'updated_at'         => now(),
                        ]);
                }
            }

            // Save guardians linked to prereg_id (studID is NULL at prereg stage)
            DB::table('tbl_guardian')->where('prereg_id', $preregId)->delete();

            foreach ($guardians as $g) {
                DB::table('tbl_guardian')->insert([
                    'studID'            => null,
                    'prereg_id'         => $preregId,
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

            return $preregId;
        });

        // Keep route compatibility: pass prereg_id as "studID" param
        return redirect()->route('admission.prereg.success', ['studID' => $preregId]);
    }

    // ✅ Success page (now reads from prereg; $studID param carries prereg_id for route compatibility)
    public function success($studID)
    {
        $preregId = (int) $studID;

        $student = DB::table('tbl_prereg_applicants')
            ->selectRaw('tbl_prereg_applicants.*, prereg_id as studID')
            ->where('prereg_id', $preregId)
            ->first();

        abort_if(!$student, 404);

        return view('admission.pre_registration.success', compact('student'));
    }

    /**
     * =========================
     * VIEWER (HTML, for modal iframe)
     * URL: GET /admission/prereg/{studID}/viewer
     * NOTE: {studID} is prereg_id for prereg stage (compat).
     * =========================
     */
    public function viewer($studID)
    {
        $preregId = (int) $studID;

        $student = DB::table('tbl_prereg_applicants')
            ->selectRaw('tbl_prereg_applicants.*, prereg_id as studID')
            ->where('prereg_id', $preregId)
            ->first();

        abort_if(!$student, 404);

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
            ->where('prereg_id', $preregId)
            ->get();

        $father = $guardians->firstWhere('relationship', 'Father');
        $mother = $guardians->firstWhere('relationship', 'Mother');

        $fatherName = $father
            ? trim(implode(' ', array_filter([$father->guardFNAME, $father->guardMname, $father->guardLname])))
            : null;

        $motherName = $mother
            ? trim(implode(' ', array_filter([$mother->guardFNAME, $mother->guardMname, $mother->guardLname])))
            : null;

        // -------- Photo
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
        $preregId = (int) $studID;

        $student = DB::table('tbl_prereg_applicants')
            ->selectRaw('tbl_prereg_applicants.*, prereg_id as studID')
            ->where('prereg_id', $preregId)
            ->first();

        abort_if(!$student, 404);

        // Address (PSGC -> Names)
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

        // Guardians (Father/Mother)
        $guardians = DB::table('tbl_guardian')
            ->where('prereg_id', $preregId)
            ->get();

        $father = $guardians->firstWhere('relationship', 'Father');
        $mother = $guardians->firstWhere('relationship', 'Mother');

        $fatherName = $father ? trim(implode(' ', array_filter([$father->guardFNAME, $father->guardMname, $father->guardLname]))) : null;
        $motherName = $mother ? trim(implode(' ', array_filter([$mother->guardFNAME, $mother->guardMname, $mother->guardLname]))) : null;

        // Photo
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

        return $pdf->stream("prereg_{$preregId}.pdf");
    }
}
