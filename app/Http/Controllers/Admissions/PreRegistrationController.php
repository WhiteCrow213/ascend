<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('admission.pre_registration.manual');
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

            'PrimarySchool'           => ['required', 'string', 'max:100'],
            'PrimarySchool_Address'   => ['required', 'string', 'max:100'],
            'YearGradPrimary'         => ['required', 'string', 'max:4'],

            'SecondarySchool'         => ['required', 'string', 'max:100'],
            'SecondarySchool_Address' => ['required', 'string', 'max:100'],
            'YearGradSecondary'       => ['required', 'string', 'max:4'],

            'LastSchoolAttended'      => ['required', 'string', 'max:100'],

            'FirstProgramChoice'      => ['required', 'string', 'max:150'],
            'SecondProgramChoice'     => ['required', 'string', 'max:150', 'different:FirstProgramChoice'],
        ]);

        $student = DB::transaction(function () use ($validated) {
            $student = StudentInfo::create(
    array_merge($validated, [
        'application_status' => 'pending',
    ])
);


            $appNo = 'APP-' . now()->format('Y') . '-' . str_pad($student->studID, 6, '0', STR_PAD_LEFT);
            $student->ApplicantNum = $appNo;
            $student->stud_number = $appNo;
            $student->save();

            return $student;
        });

        return redirect()
            ->route('admission.prereg.manual')
            ->with('prereg_saved', true)
            ->with('applicant_no', $student->ApplicantNum);
    }
}
