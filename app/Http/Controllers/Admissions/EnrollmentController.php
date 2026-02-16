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

    // ✅ Start Enrollment (creates harmless draft)
    public function start(Request $request, int $studID)
    {
        $activeTermId = DB::table('tbl_terms')->where('is_active', 1)->value('term_id');

        if (empty($activeTermId)) {
            return back()->with('enroll_err', 'No active term set. Please activate a term first.');
        }

        $exists = DB::table('tbl_enrollments')
            ->where('studID', $studID)
            ->where('term_id', $activeTermId)
            ->exists();

        if (!$exists) {
            DB::table('tbl_enrollments')->insert([
                'studID'       => $studID,
                'term_id'      => $activeTermId,
                'status'       => 'draft',
                'finalized_at' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return back()->with('enroll_ok', 'Draft enrollment started (not counted as enrolled).');
    }
}
