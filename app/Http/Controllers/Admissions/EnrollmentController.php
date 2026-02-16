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
            ->leftJoin('tbl_terms as t', 't.term_id', '=', 'e.term_id')
            ->select([
                'e.enrollment_id',
                'e.studID',
                'e.term_id',
                'e.status',
                'e.finalized_at',
                'e.created_at',
                'e.updated_at',
                // student snapshot (only what we know exists)
                's.ApplicantNum',
                's.LastName',
                's.FirstName',
                DB::raw('s.MidName as MiddleName'),
                's.FirstProgramChoice',
                's.application_status',
                // term (keep generic; may be null if columns differ)
                DB::raw('t.term_id as term_term_id'),
            ])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            abort(404);
        }

        return view('admission.enrollment.show', compact('enrollment'));
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

        // ✅ Option A: go straight to the Enrollment Workspace
        return redirect()
            ->route('admission.enrollment.show', $enrollmentId)
            ->with('enroll_ok', 'Draft enrollment opened.');
    }
}
