<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use Illuminate\Http\Request;

class PreRegistrationController extends Controller
{
    public function create()
    {
        return view('admission.pre_registration.manual');

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // STEP 1 Personal Info
            'FirstName' => ['required','string','max:50'],
            'MidName' => ['nullable','string','max:50'],
            'LastName' => ['required','string','max:50'],
            'Suffix' => ['nullable','string','max:10'],

            'ContactNo' => ['required','string','max:20'],
            'Birthdate' => ['required','date'],
            'email' => ['required','email','max:50'],
            'Gender' => ['required','string','max:10'],
            'Citizenship' => ['required','string','max:20'],
            'CivilStatus' => ['required','string','max:20'],
            'Religion' => ['required','string','max:50'],

            'Height' => ['nullable','integer'],
            'Weight' => ['nullable','integer'],
            'Bloodtype' => ['nullable','string','max:10'],

            // STEP 2 Educational Background
            'PrimarySchool' => ['required','string','max:40'],
            'PrimarySchool_Address' => ['required','string','max:100'],
            'YearGradPrimary' => ['required','string','max:4'],

            'SecondarySchool' => ['required','string','max:100'],
            'SecondarySchool_Address' => ['required','string','max:100'],
            'YearGradSecondary' => ['required','string','max:4'],

            'LastSchoolAttended' => ['required','string','max:100'],

            // new program choices
'FirstProgramChoice' => ['required','string','max:150'],
'SecondProgramChoice' => ['required','string','max:150','different:FirstProgramChoice'],
        ]);

        // Generate ApplicantNum (auto)
        $nextId = (StudentInfo::max('id') ?? 0) + 1;
        $validated['ApplicantNum'] = 'APP-' . date('Y') . '-' . str_pad((string)$nextId, 6, '0', STR_PAD_LEFT);

        StudentInfo::create($validated);

        return redirect()
            ->route('admission.prereg.manual')
            ->with('success', 'Saved! Applicant No: ' . $validated['ApplicantNum']);
    }
}
