<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentTemplateController extends Controller
{
    public function create()
    {
        $programs = DB::table('tbl_program')
            ->select('IDProgram', 'program_code', 'program_name')
            ->where('is_active', 1)
            ->orderBy('program_code')
            ->get();

        return view('utilities.assessment.AssessmentTemplateCreate', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name'      => ['required', 'string', 'max:255'],
            'program_code'       => ['nullable', 'integer', 'exists:tbl_program,IDProgram'],
            'year_level'         => ['nullable', 'integer', 'min:1', 'max:4'],
            'term_code'          => ['nullable', 'in:1,2,summer'],
            'student_status'     => ['nullable', 'in:OS,TR'],
            'athlete_status'     => ['nullable', 'in:AT,NA'],

            'tuition_fee'        => ['nullable', 'numeric', 'min:0'],
            'athletic_fee'       => ['nullable', 'numeric', 'min:0'],
            'computer_fee'       => ['nullable', 'numeric', 'min:0'],
            'sociocultural_fee'  => ['nullable', 'numeric', 'min:0'],
            'guidance_fee'       => ['nullable', 'numeric', 'min:0'],
            'library_fee'        => ['nullable', 'numeric', 'min:0'],
            'medical_dental_fee' => ['nullable', 'numeric', 'min:0'],
            'development_fee'    => ['nullable', 'numeric', 'min:0'],
            'registration_fee'   => ['nullable', 'numeric', 'min:0'],
            'laboratory_units'   => ['nullable', 'numeric', 'min:0'],
            'laboratory_fee'     => ['nullable', 'numeric', 'min:0'],
            'entrance_exam_fee'  => ['nullable', 'numeric', 'min:0'],
            'admission_fee'      => ['nullable', 'numeric', 'min:0'],
            'handbook_fee'       => ['nullable', 'numeric', 'min:0'],
            'school_id_fee'      => ['nullable', 'numeric', 'min:0'],
            'total_fees'         => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::table('tbl_assessment_templates')->insert([
            'template_name'      => $validated['template_name'],
            'program_id'         => $this->nullableInteger($request->input('program_code')),
            'year_level_id'      => $this->nullableInteger($request->input('year_level')),
            'term_id'            => $this->nullableInteger($request->input('term_code')),

            'units'              => 0.00,
            'tuition_fee'        => $this->money($request->input('tuition_fee')),
            'total_tuition'      => 0.00,
            'nstp_units'         => 0.00,
            'nstp_fee'           => 0.00,
            'athletic_fee'       => $this->money($request->input('athletic_fee')),
            'computer_fee'       => $this->money($request->input('computer_fee')),
            'sociocultural_fee'  => $this->money($request->input('sociocultural_fee')),
            'guidance_fee'       => $this->money($request->input('guidance_fee')),
            'library_fee'        => $this->money($request->input('library_fee')),
            'medical_dental_fee' => $this->money($request->input('medical_dental_fee')),
            'development_fee'    => $this->money($request->input('development_fee')),
            'registration_fee'   => $this->money($request->input('registration_fee')),
            'laboratory_units'   => $this->money($request->input('laboratory_units')),
            'laboratory_fee'     => $this->money($request->input('laboratory_fee')),
            'entrance_exam_fee'  => $this->money($request->input('entrance_exam_fee')),
            'admission_fee'      => $this->money($request->input('admission_fee')),
            'handbook_fee'       => $this->money($request->input('handbook_fee')),
            'school_id_fee'      => $this->money($request->input('school_id_fee')),
            'total_fees'         => $this->money($request->input('total_fees')),
            'is_active'          => 1,
            'remarks'            => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return redirect()
            ->route('utilities.assessment.create')
            ->with('success', 'Assessment template saved successfully.');
    }

    private function money($value): float
    {
        if ($value === null || $value === '') {
            return 0.00;
        }

        return round((float) $value, 2);
    }

    private function nullableInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }
}
