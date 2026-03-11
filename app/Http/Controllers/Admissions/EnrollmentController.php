<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    // ✅ Enrollment Candidates Grid (Approved students)
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        // Active term (needed to prevent duplicates later)
        $activeTermId = DB::table('tbl_terms')->where('is_active', 1)->value('term_id');

        $query = StudentInfo::query()
            ->where('application_status', 'approved');

        // SEARCH FILTER (mirror prereg style)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ApplicantNum', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%")
                    ->orWhere('FirstName', 'like', "%{$search}%")
                    ->orWhere('FirstProgramChoice', 'like', "%{$search}%");
            });
        }

        // ✅ Exclude students already FINALIZED in the active term
        if (!empty($activeTermId)) {
            $query->whereNotExists(function ($q) use ($activeTermId) {
                $q->select(DB::raw(1))
                    ->from('tbl_enrollments as e')
                    ->whereColumn('e.studID', 'tbl_student_info.studID')
                    ->where('e.term_id', $activeTermId)
                    ->whereNotNull('e.finalized_at');
            });
        }

        $students = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admission.enrollment.index', compact('students', 'search', 'activeTermId'));
    }

    // ✅ Enrollment Workspace (Draft view)
    public function show(int $enrollmentId)
    {
        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->leftJoin('tbl_program as p', function ($join) {
                $join->on('p.program_name', '=', 's.FirstProgramChoice')
                    ->orOn('p.program_code', '=', 's.FirstProgramChoice');
            })
            ->leftJoin('tbl_colleges as c', 'c.collegeID', '=', 'p.collegeID')
            ->leftJoin('tbl_terms as t', 't.term_id', '=', 'e.term_id')
            ->select([
                'e.enrollment_id',
                'e.studID',
                'e.term_id',
                'e.status',
                'e.finalized_at',
                'e.created_at',
                'e.updated_at',

                // student snapshot
                's.ApplicantNum',
                's.LastName',
                's.FirstName',
                DB::raw('s.MidName as MiddleName'),
                's.FirstProgramChoice',
                's.application_status',
                's.stud_number',
                's.profile_photo_path',

                'c.college_name',
                'c.college_code',

                // term
                DB::raw('t.term_id as term_term_id'),
            ])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            abort(404);
        }

        $student = $enrollment; // same snapshot object, reused by the workspace blade

        return view('admission.enrollment.EnrollmentWorkspace', compact('enrollment', 'student'));
    }

    /**
     * ✅ Student Dashboard (canonical room)
     * Rule: Dashboard identity is studID. Enrollment is optional context.
     */
    public function studentDashboard(Request $request, int $studID)
    {
        // 1) Always load the student by studID (canonical identity)
        $student = DB::table('tbl_student_info as s')
            ->leftJoin('tbl_program as p', function ($join) {
                $join->on('p.program_name', '=', 's.FirstProgramChoice')
                    ->orOn('p.program_code', '=', 's.FirstProgramChoice');
            })
            ->leftJoin('tbl_colleges as c', 'c.collegeID', '=', 'p.collegeID')
            ->select('s.*', 'c.college_name', 'c.college_code')
            ->where('s.studID', $studID)
            ->first();

        if (!$student) {
            abort(404);
        }

        // 2) Optional enrollment context (from querystring)
        $enrollment = null;
        $enrollmentId = (int) $request->query('enrollment_id', 0);

        if ($enrollmentId > 0) {
            $enrollment = DB::table('tbl_enrollments as e')
                ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
                ->leftJoin('tbl_program as p', function ($join) {
                    $join->on('p.program_name', '=', 's.FirstProgramChoice')
                        ->orOn('p.program_code', '=', 's.FirstProgramChoice');
                })
                ->leftJoin('tbl_colleges as c', 'c.collegeID', '=', 'p.collegeID')
                ->leftJoin('tbl_terms as t', 't.term_id', '=', 'e.term_id')
                ->select([
                    'e.enrollment_id',
                    'e.studID',
                    'e.term_id',
                    'e.status',
                    'e.finalized_at',
                    'e.created_at',
                    'e.updated_at',

                    // student snapshot
                    's.ApplicantNum',
                    's.LastName',
                    's.FirstName',
                    DB::raw('s.MidName as MiddleName'),
                    's.stud_number',
                    's.profile_photo_path',
                    's.FirstProgramChoice',
                    's.application_status',

                    // term
                    DB::raw('t.term_id as term_term_id'),
                ])
                ->where('e.enrollment_id', $enrollmentId)
                ->where('e.studID', $studID) // safety: prevent mismatched context
                ->first();
        }

        return view('admission.enrollment.EnrollmentWorkspace', compact('student', 'enrollment'));
    }

    // ✅ Start Enrollment (creates harmless draft) + opens Enrollment Workspace
    public function start(Request $request, int $studID)
    {
        $activeTermId = DB::table('tbl_terms')->where('is_active', 1)->value('term_id');

        if (empty($activeTermId)) {
            return back()->with('enroll_err', 'No active term set. Please activate a term first.');
        }

        // Find existing draft/enrollment for this student + active term
        $enrollmentId = DB::table('tbl_enrollments')
            ->where('studID', $studID)
            ->where('term_id', $activeTermId)
            ->value('enrollment_id');

        if (empty($enrollmentId)) {
            $enrollmentId = DB::table('tbl_enrollments')->insertGetId([
                'studID'       => $studID,
                'term_id'      => $activeTermId,
                'status'       => 'draft',
                'finalized_at' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return redirect()
            ->route('admission.enrollment.show', $enrollmentId)
            ->with('enroll_ok', 'Draft enrollment opened.');
    }
}