<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreRegistrationController extends Controller
{
    public function create()
    {
        return view('admission.pre_registration.manual');
    }

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
            // Create first so we get studID
            $student = StudentInfo::create($validated);

            // Generate Applicant Number based on studID
            $appNo = 'APP-' . now()->format('Y') . '-' . str_pad($student->studID, 6, '0', STR_PAD_LEFT);

            $student->ApplicantNum = $appNo;

            // TEMP: stud_number mirrors ApplicantNum for now
            $student->stud_number = $appNo;

            $student->save();

            return $student;
        });

        // ✅ Redirect to the manual prereg page, but show the acknowledgement screen
        return redirect()
            ->route('admission.prereg.manual')
            ->with('prereg_saved', true)
            ->with('applicant_no', $student->ApplicantNum);
    }
}
