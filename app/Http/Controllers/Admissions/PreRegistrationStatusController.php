<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentInfo;
use Illuminate\Support\Facades\DB;

class PreRegistrationStatusController extends Controller
{
    /**
     * Update application status (Approve / Reject)
     *
     * NOTE:
     * - Route still passes {studID} for UI compatibility, but it now represents prereg_id.
     * - On APPROVE:
     *     1) Copy prereg -> tbl_student_info (creates studID)
     *     2) Generate OFFICIAL stud_number based on active AY (tbl_terms.school_year) + sequence table
     *     3) Link back prereg.studID + guardians.studID
     */
    public function updateStatus(Request $request, $studID)
    {
        $request->validate([
            'application_status' => 'required|in:approved,rejected',
        ]);

        $preregId = (int) $studID;

        $prereg = DB::table('tbl_prereg_applicants')->where('prereg_id', $preregId)->first();
        abort_if(!$prereg, 404);

        $newStatus = $request->application_status;

        // Always update prereg status
        DB::table('tbl_prereg_applicants')->where('prereg_id', $preregId)->update([
            'application_status' => $newStatus,
            'updated_at' => now(),
        ]);

        // If rejected: no student record created
        if ($newStatus === 'rejected') {
            return redirect()->back()->with('status_updated', true);
        }

        // APPROVED: if already linked to student, do not duplicate.
        if (!empty($prereg->studID)) {
            StudentInfo::where('studID', $prereg->studID)->update([
                'application_status' => 'approved',
            ]);

            return redirect()->back()->with('status_updated', true);
        }

        DB::transaction(function () use ($prereg, $preregId) {

            // =========================
            // Get ACTIVE school year (tbl_terms)
            // =========================
            $activeTerm = DB::table('tbl_terms')
                ->where('is_active', 1)
                ->orderByDesc('term_id')
                ->first();

            abort_if(!$activeTerm || empty($activeTerm->school_year), 500, 'No active term / school year found. Please set an active term in Utilities.');

            // school_year is like "2025-2026"
            $sy = (string) $activeTerm->school_year;
            $m = [];
            if (!preg_match('/^(\d{4})\s*-\s*(\d{4})$/', $sy, $m)) {
                abort(500, 'Invalid school_year format in tbl_terms. Expected "YYYY-YYYY". Found: ' . $sy);
            }
            $startYY = substr($m[1], -2);
            $endYY   = substr($m[2], -2);
            $ayPrefix = $startYY . $endYY; // e.g. 2526

            // =========================
            // Allocate next running number (race-safe)
            // =========================
            $seqRow = DB::table('tbl_student_number_sequences')
                ->where('ay_prefix', $ayPrefix)
                ->lockForUpdate()
                ->first();

            if (!$seqRow) {
                DB::table('tbl_student_number_sequences')->insert([
                    'ay_prefix' => $ayPrefix,
                    'last_number' => 0,
                    'school_year_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $seqRow = DB::table('tbl_student_number_sequences')
                    ->where('ay_prefix', $ayPrefix)
                    ->lockForUpdate()
                    ->first();
            }

            $next = ((int) $seqRow->last_number) + 1;

            DB::table('tbl_student_number_sequences')
                ->where('ay_prefix', $ayPrefix)
                ->update([
                    'last_number' => $next,
                    'updated_at'  => now(),
                ]);

            $studNumber = $ayPrefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT); // e.g. 25260001

            // =========================
            // Create StudentInfo by copying prereg fields
            // =========================
            $payload = [
                // Official student number issued on approval
                'stud_number'            => $studNumber,

                // Keep ApplicantNum for traceability
                'ApplicantNum'           => $prereg->ApplicantNum ?? null,

                'FirstName'              => $prereg->FirstName ?? null,
                'MidName'                => $prereg->MidName ?? null,
                'LastName'               => $prereg->LastName ?? null,
                'Suffix'                 => $prereg->Suffix ?? null,
                'ContactNo'              => $prereg->ContactNo ?? null,
                'Birthdate'              => $prereg->Birthdate ?? null,
                'place_of_birth'         => $prereg->place_of_birth ?? null,
                'email'                  => $prereg->email ?? null,
                'Gender'                 => $prereg->Gender ?? null,
                'Citizenship'            => $prereg->Citizenship ?? null,
                'CivilStatus'            => $prereg->CivilStatus ?? null,
                'Religion'               => $prereg->Religion ?? null,
                'Bloodtype'              => $prereg->Bloodtype ?? null,
                'Height'                 => $prereg->Height ?? null,
                'Weight'                 => $prereg->Weight ?? null,

                'PrimarySchool'          => $prereg->PrimarySchool ?? null,
                'PrimarySchool_Address'  => $prereg->PrimarySchool_Address ?? null,
                'YearGradPrimary'        => $prereg->YearGradPrimary ?? null,
                'SecondarySchool'        => $prereg->SecondarySchool ?? null,
                'SecondarySchool_Address'=> $prereg->SecondarySchool_Address ?? null,
                'YearGradSecondary'      => $prereg->YearGradSecondary ?? null,
                'LastSchoolAttended'     => $prereg->LastSchoolAttended ?? null,

                'FirstProgramChoice'     => $prereg->FirstProgramChoice ?? null,
                'SecondProgramChoice'    => $prereg->SecondProgramChoice ?? null,
                'applicant_type'         => $prereg->applicant_type ?? null,

                'region_psgc'            => $prereg->region_psgc ?? null,
                'province_psgc'          => $prereg->province_psgc ?? null,
                'citymun_psgc'           => $prereg->citymun_psgc ?? null,
                'brgy_psgc'              => $prereg->brgy_psgc ?? null,

                'profile_photo_path'     => $prereg->profile_photo_path ?? null,

                'application_status'     => 'approved',
            ];

            $student = StudentInfo::create($payload);

            // Link prereg -> student
            DB::table('tbl_prereg_applicants')->where('prereg_id', $preregId)->update([
                'studID' => $student->studID,
                'updated_at' => now(),
            ]);

            // Link guardians -> student
            DB::table('tbl_guardian')
                ->where('prereg_id', $preregId)
                ->whereNull('studID')
                ->update(['studID' => $student->studID]);
        });

        return redirect()->back()->with('status_updated', true);
    }
}
