<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            ->leftJoin('tbl_program as p', function($join) {
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
                // student snapshot (only what we know exists)
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
                // term (keep generic; may be null if columns differ)
                DB::raw('t.term_id as term_term_id'),
            ])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            abort(404);
        }

            $student = $enrollment; // ✅ same snapshot object, reused by the dashboard blade

        return view('admission.enrollment.EnrollmentWorkspace', compact('enrollment', 'student'));
    }


    // ✅ Enrollment Form (Subject Loading - read only for now)
    public function showForm(Request $request, int $enrollmentId)
    {
        // Active term row (we need the active semester later)
        $activeTerm = DB::table('tbl_terms')->where('is_active', 1)->first();
        $activeTermId = $activeTerm->term_id ?? null;

        // Enrollment + Student snapshot
        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->leftJoin('tbl_program as p', function($join) {
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
                // student basics
                's.ApplicantNum',
                's.LastName',
                's.FirstName',
                DB::raw('s.MidName as MiddleName'),
                's.FirstProgramChoice',
                's.stud_number',
                's.profile_photo_path',
                // optional fields (guarded below)
                DB::raw('NULL as IDcurr'),
                DB::raw('NULL as IDyearlvl'),
                'c.college_name',
                'c.college_code',
            ])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            abort(404);
        }

        // Pull optional columns safely (no assumptions)
        $hasIDcurr = Schema::hasColumn('tbl_student_info', 'IDcurr');
        $hasIDyearlvl = Schema::hasColumn('tbl_student_info', 'IDyearlvl');

        if ($hasIDcurr || $hasIDyearlvl) {
            $extra = DB::table('tbl_student_info')
                ->select(array_filter([
                    $hasIDcurr ? 'IDcurr' : null,
                    $hasIDyearlvl ? 'IDyearlvl' : null,
                ]))
                ->where('studID', $enrollment->studID)
                ->first();

            if ($hasIDcurr && $extra && isset($extra->IDcurr)) {
                $enrollment->IDcurr = $extra->IDcurr;
            }
            if ($hasIDyearlvl && $extra && isset($extra->IDyearlvl)) {
                $enrollment->IDyearlvl = $extra->IDyearlvl;
            }
        }

        // Determine active semester value (1/2) if available; fallback to 1 (safe default for UI only)
        $activeSemester = 1;
        if ($activeTerm) {
            if (isset($activeTerm->semester) && in_array((int)$activeTerm->semester, [1,2], true)) {
                $activeSemester = (int)$activeTerm->semester;
            } elseif (isset($activeTerm->term_name)) {
                $name = strtolower((string)$activeTerm->term_name);
                if (str_contains($name, '2')) { $activeSemester = 2; }
            }
        }

        // Offered subjects (best-effort, curriculum-bound)
        $offeredSubjects = collect();

        // If this enrollment already has a saved load, display from tbl_enrollment_subjects
        // (we keep the same $offeredSubjects variable to avoid breaking the Blade view)
        $hasSavedLoad = false;
        if (Schema::hasTable('tbl_enrollment_subjects')
            && Schema::hasColumn('tbl_enrollment_subjects', 'enroll_subj_id')
            && Schema::hasColumn('tbl_enrollment_subjects', 'enrollment_id')
            && Schema::hasColumn('tbl_enrollment_subjects', 'subject_id')
        ) {
            $hasSavedLoad = DB::table('tbl_enrollment_subjects')->where('enrollment_id', $enrollmentId)->exists();
            if ($hasSavedLoad) {
                $offeredSubjects = DB::table('tbl_enrollment_subjects as es')
                    ->join('tbl_subjects as s', 's.IDsubj', '=', 'es.subject_id')
                    ->select([
                        'es.enroll_subj_id',
                        DB::raw('es.subject_id as IDsubj'),
                        's.CourseCode',
                        's.CourseDescription',
                        's.Units',
                        DB::raw("'—' as YearLevelName"),
                        DB::raw('NULL as semester'),
                    ])
                    ->orderBy('s.CourseCode', 'asc')
                    ->get();
            }
        }


        $canQueryOfferings = !empty($enrollment->IDcurr) && !empty($enrollment->IDyearlvl);

$programOptions = collect();
$selectedProgramId = null;

// Program dropdown options (must include IDprogram + IDcurr)
if (Schema::hasTable('tbl_program')
    && Schema::hasColumn('tbl_program', 'IDprogram')
    && Schema::hasColumn('tbl_program', 'program_name')
    && Schema::hasColumn('tbl_program', 'program_code')
    && Schema::hasColumn('tbl_program', 'IDcurr')
) {
    $programOptions = DB::table('tbl_program')
        ->select(['IDprogram', 'program_name', 'program_code', 'IDcurr'])
        ->orderBy('program_name', 'asc')
        ->get()
        ->map(function ($p) {
            $label = trim(($p->program_name ?? '') . ' (' . ($p->program_code ?? '') . ')');
            return (object)[
                'id'    => (int)$p->IDprogram,
                'IDcurr'=> $p->IDcurr,
                'label' => $label !== '' ? $label : (string)($p->program_name ?? $p->program_code ?? '—'),
            ];
        });

    // Default selected program: derive from student's IDcurr (if present)
    if (!empty($enrollment->IDcurr)) {
        $match = $programOptions->firstWhere('IDcurr', $enrollment->IDcurr);
        if ($match && isset($match->id)) {
            $selectedProgramId = (string)$match->id;
        }
    }
}

// Year Level table could be tbl_yearlevel (your schema) or tbl_yearlevels (older)
$yearLevelTable = Schema::hasTable('tbl_yearlevel') ? 'tbl_yearlevel' : (Schema::hasTable('tbl_yearlevels') ? 'tbl_yearlevels' : null);

$yearLevelOptions = collect();
if ($yearLevelTable
    && Schema::hasColumn($yearLevelTable, 'IDyearlvl')
    && Schema::hasColumn($yearLevelTable, 'YearLevelName')
) {
    $yearLevelOptions = DB::table($yearLevelTable)
        ->select(['IDyearlvl', 'YearLevelName'])
        ->orderBy('IDyearlvl', 'asc')
        ->get()
        ->map(function ($y) {
            return (object)[
                'id'    => (string)$y->IDyearlvl,
                'label' => (string)$y->YearLevelName,
            ];
        });
}

// Selected values (UI state)
$selectedProgram = (string) $request->old('IDprogram', $request->query('IDprogram', (string)($selectedProgramId ?? '')));
$selectedYearLevel = (string) $request->old('IDyearlvl', $request->query('IDyearlvl', (string)($enrollment->IDyearlvl ?? '')));

if (Schema::hasTable('tbl_yearlevels')
    && Schema::hasColumn('tbl_yearlevels', 'IDyearlvl')
    && Schema::hasColumn('tbl_yearlevels', 'YearLevelName')
) {
    $yearLevelOptions = DB::table('tbl_yearlevels')
        ->select(['IDyearlvl', 'YearLevelName'])
        ->orderBy('IDyearlvl', 'asc')
        ->get()
        ->map(function ($y) {
            return (object)[
                'value' => (string)$y->IDyearlvl,
                'label' => (string)$y->YearLevelName,
            ];
        });
}


        if ($canQueryOfferings
            && Schema::hasTable('tbl_currmap')
            && Schema::hasTable('tbl_subjects')
            && Schema::hasColumn('tbl_currmap', 'IDcurr')
            && Schema::hasColumn('tbl_currmap', 'IDsubj')
            && Schema::hasColumn('tbl_currmap', 'IDyearlvl')
            && Schema::hasColumn('tbl_currmap', 'semester')
        ) {
            $q = DB::table('tbl_currmap as m')
                ->join('tbl_subjects as s', 's.IDsubj', '=', 'm.IDsubj')
                ->where('m.IDcurr', $enrollment->IDcurr)
                ->where('m.IDyearlvl', $enrollment->IDyearlvl)
                ->where('m.semester', $activeSemester);

            if ($yearLevelTable) {
                $q->leftJoin($yearLevelTable.' as y', 'y.IDyearlvl', '=', 'm.IDyearlvl');
            }

            $offeredSubjects = $q->select([
                    'm.IDsubj',
                    's.CourseCode',
                    's.CourseDescription',
                    's.Units',
                    $yearLevelTable ? 'y.YearLevelName' : DB::raw("'—' as YearLevelName"),
                    'm.semester',
                ])
                ->orderBy('s.CourseCode', 'asc')
                ->get();
        }

        return view('admission.enrollment.EnrollmentForm', [
            'enrollment'       => $enrollment,
            'activeTermId'     => $activeTermId,
            'activeSemester'   => $activeSemester,
            'offeredSubjects'  => $offeredSubjects,
            'canQueryOfferings'=> $canQueryOfferings,
            'programOptions'   => $programOptions,
            'yearLevelOptions' => $yearLevelOptions,
            'selectedProgram'  => $selectedProgram,
            'selectedYearLevel'=> $selectedYearLevel,
        ]);
    }

    // ✅ Apply Academic Filters (Program + Year Level) to Student Info
    // This is the official "finalize identity" step before subject loading.
    public function applyAcademic(Request $request, int $enrollmentId)
    {
        $request->validate([
            'IDprogram' => ['required', 'integer'],
            'IDyearlvl' => ['required', 'integer'],
        ]);

        // Fetch enrollment to get studID
        $enrollment = DB::table('tbl_enrollments')->select(['enrollment_id','studID'])->where('enrollment_id', $enrollmentId)->first();
        if (!$enrollment) {
            abort(404);
        }

        // Resolve program => IDcurr
        $program = DB::table('tbl_program')
            ->select(['IDprogram','IDcurr'])
            ->where('IDprogram', (int)$request->input('IDprogram'))
            ->first();

        if (!$program || empty($program->IDcurr)) {
            return back()->withErrors(['IDprogram' => 'Invalid program selection.'])->withInput();
        }

        // Ensure year level exists (table name varies)
        $yearLevelTable = Schema::hasTable('tbl_yearlevel') ? 'tbl_yearlevel' : (Schema::hasTable('tbl_yearlevels') ? 'tbl_yearlevels' : null);
        if (!$yearLevelTable) {
            return back()->withErrors(['IDyearlvl' => 'Year level table not found.'])->withInput();
        }

        $year = DB::table($yearLevelTable)
            ->select(['IDyearlvl'])
            ->where('IDyearlvl', (int)$request->input('IDyearlvl'))
            ->first();

        if (!$year) {
            return back()->withErrors(['IDyearlvl' => 'Invalid year level selection.'])->withInput();
        }

        // Update student academic state
        DB::table('tbl_student_info')
            ->where('studID', $enrollment->studID)
            ->update([
                'IDcurr'    => $program->IDcurr,
                'IDyearlvl' => (int)$request->input('IDyearlvl'),
            ]);

        return redirect()
            ->route('admission.enrollment.form', ['enrollmentId' => $enrollmentId])
            ->with('success', 'Academic filters applied.');
    }



    
    /**
     * ✅ Student Dashboard (canonical room)
     * Rule: Dashboard identity is studID. Enrollment is optional context.
     * This is intentionally kept inside EnrollmentController for now (surgical, no new files required).
     */
    public function studentDashboard(Request $request, int $studID)
    {
        // 1) Always load the student by studID (canonical identity)
        $student = DB::table('tbl_student_info as s')
            ->leftJoin('tbl_program as p', function($join) {
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
                ->leftJoin('tbl_program as p', function($join) {
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
                    // student snapshot (only what we know exists)
                    's.ApplicantNum',
                    's.LastName',
                    's.FirstName',
                    DB::raw('s.MidName as MiddleName'),
                    's.stud_number',
                    's.profile_photo_path',
                's.profile_photo_path',
                    's.FirstProgramChoice',
                    's.application_status',
                    // term (keep generic; may be null if columns differ)
                    DB::raw('t.term_id as term_term_id'),
                ])
                ->where('e.enrollment_id', $enrollmentId)
                ->where('e.studID', $studID) // ✅ safety: prevent mismatched context
                ->first();
        }

        // NOTE: We reuse the existing workspace blade (now "Student Overview" room).
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

        // ✅ Option A: go straight to the Enrollment Workspace
        return redirect()
            ->route('admission.enrollment.show', $enrollmentId)
            ->with('enroll_ok', 'Draft enrollment opened.');
    }


    // ===========================
    // Add Subject Modal Endpoints
    // ===========================

    /**
     * Returns offered subjects for the enrollment's current curriculum + year level + active semester.
     * Used by the "Add Subject" modal (active search).
     */
    public function offeringsSearch(Request $request, int $enrollmentId)
    {
        $q = trim((string) $request->get('q', ''));
        $limit = (int) $request->get('limit', 25);
        if ($limit < 5) $limit = 5;
        if ($limit > 50) $limit = 50;

        // Enrollment + student academic snapshot (IDcurr + IDyearlvl)
        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->select(['e.enrollment_id', 'e.term_id', 's.studID'])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json(['ok' => false, 'message' => 'Enrollment not found.'], 404);
        }

        // Pull IDcurr + IDyearlvl from student_info (if present)
        $hasIDcurr = Schema::hasColumn('tbl_student_info', 'IDcurr');
        $hasIDyearlvl = Schema::hasColumn('tbl_student_info', 'IDyearlvl');

        $studExtra = DB::table('tbl_student_info')
            ->select(array_filter([
                $hasIDcurr ? 'IDcurr' : null,
                $hasIDyearlvl ? 'IDyearlvl' : null,
            ]))
            ->where('studID', $enrollment->studID)
            ->first();

        $IDcurr = ($studExtra && isset($studExtra->IDcurr)) ? (int)$studExtra->IDcurr : null;
        $IDyearlvl = ($studExtra && isset($studExtra->IDyearlvl)) ? (int)$studExtra->IDyearlvl : null;

        if (!$IDcurr || !$IDyearlvl) {
            return response()->json([
                'ok' => false,
                'message' => 'Program/Year Level not finalized yet. Click Apply first.',
                'data' => [],
            ]);
        }

        // Determine active semester (same logic as showForm; keep safe defaults)
        $activeTerm = DB::table('tbl_terms')->where('is_active', 1)->first();
        $activeSemester = 1;
        if ($activeTerm) {
            if (isset($activeTerm->semester) && in_array((int)$activeTerm->semester, [1,2], true)) {
                $activeSemester = (int)$activeTerm->semester;
            } elseif (isset($activeTerm->term_name)) {
                $name = strtolower((string)$activeTerm->term_name);
                if (str_contains($name, '2')) { $activeSemester = 2; }
            }
        }

        // Offerings source table: prefer `currmap`, fallback to `tbl_currmap`
        $mapTable = null;
        if (Schema::hasTable('tbl_currmap')) {
            $mapTable = 'tbl_currmap';
        } elseif (Schema::hasTable('tbl_currmap')) {
            $mapTable = 'tbl_currmap';
        }

        if (!$mapTable) {
            return response()->json(['ok' => false, 'message' => 'Curriculum map table not found.'], 500);
        }

        $query = DB::table($mapTable . ' as m')
            ->join('tbl_subjects as s', 's.IDsubj', '=', 'm.IDsubj')
            ->select([
                'm.IDsubj',
                's.CourseCode',
                's.CourseDescription',
                's.Units',
                DB::raw('COALESCE(s.PrereqSubj, "") as PrereqSubj'),
            ])
            ->where('m.IDcurr', $IDcurr)
            ->where('m.IDyearlvl', $IDyearlvl);

        // Semester column might be `semester` or `Semester`
        if (Schema::hasColumn($mapTable, 'semester')) {
            $query->where('m.semester', $activeSemester);
        } elseif (Schema::hasColumn($mapTable, 'Semester')) {
            $query->where('m.Semester', $activeSemester);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('s.CourseCode', 'like', "%{$q}%")
                  ->orWhere('s.CourseDescription', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderBy('s.CourseCode', 'asc')
            ->limit($limit)
            ->get();

        return response()->json(['ok' => true, 'data' => $rows]);
    }

    /**
     * Adds a subject to the student's current load (manual add).
     * Strict prerequisite policy (A): prereqs must be PASSED already.
     */
    public function offeringsAdd(Request $request, int $enrollmentId)
    {
        $request->validate([
            'IDsubj' => 'required|integer',
        ]);

        $IDsubj = (int) $request->input('IDsubj');

        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->select(['e.enrollment_id', 'e.term_id', 's.studID'])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return back()->with('toast_error', 'Enrollment not found.');
        }

        // Make sure table exists
        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return back()->with('toast_error', 'Enrollment subjects table is missing.');
        }

        // Hard block: prerequisite must be passed already
        $subj = DB::table('tbl_subjects')->select(['IDsubj', 'CourseCode', 'PrereqSubj'])->where('IDsubj', $IDsubj)->first();
        if (!$subj) {
            return back()->with('toast_error', 'Subject not found.');
        }

        $prereqCodes = [];
        if (!empty($subj->PrereqSubj)) {
            $prereqCodes = collect(explode(',', (string)$subj->PrereqSubj))
                ->map(fn($c) => strtoupper(trim($c)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        // If there are prereqs, we need a grades/history table to validate
        if (count($prereqCodes) > 0) {
            // You may already have a table for grades. We will check the most likely ones safely.
            $gradeTable = null;
            foreach (['tbl_student_grades', 'tbl_grades', 'tbl_grade_records'] as $t) {
                if (Schema::hasTable($t)) { $gradeTable = $t; break; }
            }

            if (!$gradeTable) {
                return back()->with('toast_error', 'Cannot add subject: prerequisite validation is not configured yet.');
            }

            // Determine passing: try common column names (FinalGrade/grade/remarks). This is defensive.
            // If your actual grade schema differs, we'll wire it exactly later.
            $passedCodes = collect();

            if (Schema::hasColumn($gradeTable, 'studID') && Schema::hasColumn($gradeTable, 'CourseCode')) {
                $gq = DB::table($gradeTable)
                    ->where('studID', $enrollment->studID)
                    ->whereIn('CourseCode', $prereqCodes);

                if (Schema::hasColumn($gradeTable, 'remarks')) {
                    $gq->whereRaw("LOWER(remarks) IN ('passed','pass')");
                }

                $passedCodes = $gq->pluck('CourseCode')->map(fn($c)=>strtoupper(trim((string)$c)))->unique();
            } else {
                return back()->with('toast_error', 'Cannot add subject: grade table schema not wired yet.');
            }

            $missing = array_values(array_diff($prereqCodes, $passedCodes->all()));
            if (count($missing) > 0) {
                return back()->with('toast_error', 'Cannot add subject: missing prerequisite(s) ' . implode(', ', $missing) . '.');
            }
        }

        // Prevent duplicates for this enrollment
        $exists = DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->where('IDsubj', $IDsubj)
            ->exists();

        if ($exists) {
            return back()->with('toast_success', 'Subject already added.');
        }

        DB::table('tbl_enrollment_subjects')->insert([
            'enrollment_id' => $enrollmentId,
            'IDsubj'        => $IDsubj,
            'source'        => 'manual',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('toast_success', 'Subject added successfully!');
    }
    /**
     * Remove a subject from the current enrollment load (tbl_enrollment_subjects).
     * NOTE: This is intentionally simple and safe: it only deletes rows belonging to this enrollment_id.
     */
    public function subjectRemove(Request $request, int $enrollmentId, int $enrollSubjId)
    {
        // Guard table/columns (no assumptions beyond what you confirmed exists)
        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return redirect()->back()->with('error', 'Enrollment load table not found.');
        }

        // Delete only if it belongs to this enrollment
        DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->where('enroll_subj_id', $enrollSubjId)
            ->delete();

        return redirect()->back()->with('success', 'Subject removed.');
    }

}
