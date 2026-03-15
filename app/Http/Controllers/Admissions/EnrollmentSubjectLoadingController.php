<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnrollmentSubjectLoadingController extends Controller
{
    /**
     * Enrollment Form (Subject Loading)
     */
    public function showForm(Request $request, int $enrollmentId)
    {
        // Active term row
        $activeTerm = DB::table('tbl_terms')->where('is_active', 1)->first();
        $activeTermId = $activeTerm->term_id ?? null;

        // Enrollment + Student snapshot
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

                's.ApplicantNum',
                's.LastName',
                's.FirstName',
                DB::raw('s.MidName as MiddleName'),
                's.FirstProgramChoice',
                's.stud_number',
                's.profile_photo_path',

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

        // Pull optional columns safely
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
        $offeredSubjects = collect();
        $studentLoadSubjects = collect();

        // If enrollment already has saved load, display from tbl_enrollment_subjects
        $hasSavedLoad = false;
        if (
            Schema::hasTable('tbl_enrollment_subjects') &&
            Schema::hasColumn('tbl_enrollment_subjects', 'enroll_subj_id') &&
            Schema::hasColumn('tbl_enrollment_subjects', 'enrollment_id')
        ) {
            $hasSavedLoad = DB::table('tbl_enrollment_subjects')
                ->where('enrollment_id', $enrollmentId)
                ->exists();

            if ($hasSavedLoad) {
                if (Schema::hasColumn('tbl_enrollment_subjects', 'offering_id')) {
                    $studentLoadSubjects = DB::table('tbl_enrollment_subjects as es')
                        ->join('tbl_section_offerings as o', 'o.offering_id', '=', 'es.offering_id')
                        ->join('tbl_subjects as s', 's.IDsubj', '=', 'o.subject_id')
                        ->leftJoin('tbl_sections as sec', 'sec.section_id', '=', 'o.section_id')
                        ->leftJoin('tbl_employees as f', 'o.instructor_id', '=', 'f.IDemployees')
                        ->select([
                            'es.enroll_subj_id',
                            DB::raw('o.subject_id as IDsubj'),
                            'o.offering_id',
                            's.CourseCode',
                            's.CourseDescription',
                            's.Units',
                            DB::raw("'—' as YearLevelName"),
                            DB::raw('NULL as semester'),
                            DB::raw("COALESCE(sec.section_name, '—') as SectionName"),
                            DB::raw("COALESCE(o.day_pattern, '—') as Day"),
                            DB::raw("CASE
                                WHEN o.time_start IS NOT NULL AND o.time_end IS NOT NULL THEN
                                    CONCAT(
                                        TIME_FORMAT(o.time_start, '%h:%i %p'),
                                        ' - ',
                                        TIME_FORMAT(o.time_end, '%h:%i %p')
                                    )
                                ELSE '—'
                            END as Time"),
                            DB::raw("CASE
                                WHEN f.IDemployees IS NOT NULL THEN
                                    TRIM(CONCAT(
                                        COALESCE(f.FacultyFirstName, ''),
                                        ' ',
                                        COALESCE(f.FacultyMiddleName, ''),
                                        ' ',
                                        COALESCE(f.FacultyLastName, '')
                                    ))
                                ELSE '—'
                            END as InstructorName"),
                        ])
                        ->where('es.enrollment_id', $enrollmentId)
                        ->orderBy('s.CourseCode', 'asc')
                        ->get();
                } elseif (Schema::hasColumn('tbl_enrollment_subjects', 'subject_id')) {
                    $studentLoadSubjects = DB::table('tbl_enrollment_subjects as es')
                        ->join('tbl_subjects as s', 's.IDsubj', '=', 'es.subject_id')
                        ->select([
                            'es.enroll_subj_id',
                            DB::raw('es.subject_id as IDsubj'),
                            's.CourseCode',
                            's.CourseDescription',
                            's.Units',
                            DB::raw("'—' as YearLevelName"),
                            DB::raw('NULL as semester'),
                            DB::raw("'—' as SectionName"),
                            DB::raw("'—' as Day"),
                            DB::raw("'—' as Time"),
                            DB::raw("'—' as InstructorName"),
                        ])
                        ->where('es.enrollment_id', $enrollmentId)
                        ->orderBy('s.CourseCode', 'asc')
                        ->get();
                }
            }
        }

        $canQueryOfferings = !empty($enrollment->IDcurr) && !empty($enrollment->IDyearlvl);

        $programOptions = collect();
        $selectedProgramId = null;

        // Program dropdown options
        if (
            Schema::hasTable('tbl_program') &&
            Schema::hasColumn('tbl_program', 'IDprogram') &&
            Schema::hasColumn('tbl_program', 'program_name') &&
            Schema::hasColumn('tbl_program', 'program_code') &&
            Schema::hasColumn('tbl_program', 'IDcurr')
        ) {
            $programOptions = DB::table('tbl_program')
                ->select(['IDprogram', 'program_name', 'program_code', 'IDcurr'])
                ->orderBy('program_name', 'asc')
                ->get()
                ->map(function ($p) {
                    $label = trim(($p->program_name ?? '') . ' (' . ($p->program_code ?? '') . ')');

                    return (object) [
                        'id'     => (int) $p->IDprogram,
                        'IDcurr' => $p->IDcurr,
                        'label'  => $label !== '' ? $label : (string) ($p->program_name ?? $p->program_code ?? '—'),
                    ];
                });

            if (!empty($enrollment->IDcurr)) {
                $match = $programOptions->firstWhere('IDcurr', $enrollment->IDcurr);
                if ($match && isset($match->id)) {
                    $selectedProgramId = (string) $match->id;
                }
            }
        }

        // Year Level table
        $yearLevelTable = Schema::hasTable('tbl_yearlevel')
            ? 'tbl_yearlevel'
            : (Schema::hasTable('tbl_yearlevels') ? 'tbl_yearlevels' : null);

        $yearLevelOptions = collect();
        if (
            $yearLevelTable &&
            Schema::hasColumn($yearLevelTable, 'IDyearlvl') &&
            Schema::hasColumn($yearLevelTable, 'YearLevelName')
        ) {
            $yearLevelOptions = DB::table($yearLevelTable)
                ->select(['IDyearlvl', 'YearLevelName'])
                ->orderBy('IDyearlvl', 'asc')
                ->get()
                ->map(function ($y) {
                    return (object) [
                        'id'    => (string) $y->IDyearlvl,
                        'label' => (string) $y->YearLevelName,
                    ];
                });
        }

        $selectedProgram = (string) $request->old(
            'IDprogram',
            $request->query('IDprogram', (string) ($selectedProgramId ?? ''))
        );

        $selectedYearLevel = (string) $request->old(
            'IDyearlvl',
            $request->query('IDyearlvl', (string) ($enrollment->IDyearlvl ?? ''))
        );
        return view('admission.enrollment.EnrollmentForm', [
            'enrollment'        => $enrollment,
            'activeTermId'      => $activeTermId,
                        'offeredSubjects'   => $offeredSubjects,
            'studentLoadSubjects' => $studentLoadSubjects,
            'canQueryOfferings' => $canQueryOfferings,
            'programOptions'    => $programOptions,
            'yearLevelOptions'  => $yearLevelOptions,
            'selectedProgram'   => $selectedProgram,
            'selectedYearLevel' => $selectedYearLevel,
        ]);
    }

    /**
     * Apply Academic Filters (Program + Year Level)
     * Official academic-state step before subject loading.
     */
    public function applyAcademic(Request $request, int $enrollmentId)
    {
        $request->validate([
            'IDprogram' => ['required', 'integer'],
            'IDyearlvl' => ['required', 'integer'],
        ]);

        $enrollment = DB::table('tbl_enrollments')
            ->select(['enrollment_id', 'studID'])
            ->where('enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            abort(404);
        }

        $program = DB::table('tbl_program')
            ->select(['IDprogram', 'IDcurr'])
            ->where('IDprogram', (int) $request->input('IDprogram'))
            ->first();

        if (!$program || empty($program->IDcurr)) {
            return back()->withErrors(['IDprogram' => 'Invalid program selection.'])->withInput();
        }

        $yearLevelTable = Schema::hasTable('tbl_yearlevel')
            ? 'tbl_yearlevel'
            : (Schema::hasTable('tbl_yearlevels') ? 'tbl_yearlevels' : null);

        if (!$yearLevelTable) {
            return back()->withErrors(['IDyearlvl' => 'Year level table not found.'])->withInput();
        }

        $year = DB::table($yearLevelTable)
            ->select(['IDyearlvl'])
            ->where('IDyearlvl', (int) $request->input('IDyearlvl'))
            ->first();

        if (!$year) {
            return back()->withErrors(['IDyearlvl' => 'Invalid year level selection.'])->withInput();
        }

        DB::table('tbl_student_info')
            ->where('studID', $enrollment->studID)
            ->update([
                'IDcurr'    => $program->IDcurr,
                'IDyearlvl' => (int) $request->input('IDyearlvl'),
            ]);

        return redirect()
            ->route('admission.enrollment.form', ['enrollmentId' => $enrollmentId])
            ->with('success', 'Academic filters applied.');
    }

    /**
     * Load sections for selected year level using the student's finalized curriculum/program.
     */
    public function getSections(Request $request, int $enrollmentId)
    {
        $yearLevel = (int) $request->get('year_level');

        if ($yearLevel <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Year level is required.',
                'data' => [],
            ], 422);
        }

        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->select([
                'e.enrollment_id',
                'e.term_id',
                's.studID',
                's.IDcurr',
                's.IDyearlvl',
            ])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'ok' => false,
                'message' => 'Enrollment not found.',
                'data' => [],
            ], 404);
        }

        if (empty($enrollment->IDcurr)) {
            return response()->json([
                'ok' => false,
                'message' => 'Program/Curriculum not finalized yet. Click Apply first.',
                'data' => [],
            ], 422);
        }

        $activeTermId = DB::table('tbl_terms')
            ->where('is_active', 1)
            ->value('term_id');

        $termIdToUse = $activeTermId ?: $enrollment->term_id;

        $sectionsQuery = DB::table('tbl_sections as sec')
            ->where('sec.IDcurr', $enrollment->IDcurr)
            ->where('sec.year_level', $yearLevel)
            ->where('sec.term_id', $termIdToUse);

        if (Schema::hasColumn('tbl_sections', 'is_active')) {
            $sectionsQuery->where('sec.is_active', 1);
        }

        $sections = $sectionsQuery
            ->select([
                'sec.section_id',
                'sec.section_name',
            ])
            ->distinct()
            ->orderBy('sec.section_name', 'asc')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $sections,
        ]);
    }

    /**
     * Load offerings for selected section.
     */
    public function getOfferings(Request $request, int $enrollmentId)
    {
        $sectionId = (int) $request->get('section_id');

        if ($sectionId <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Section is required.',
                'data' => [],
            ], 422);
        }

        $enrollment = DB::table('tbl_enrollments')
            ->select(['enrollment_id', 'term_id'])
            ->where('enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'ok' => false,
                'message' => 'Enrollment not found.',
                'data' => [],
            ], 404);
        }

        $activeTermId = DB::table('tbl_terms')
            ->where('is_active', 1)
            ->value('term_id');

        $termIdToUse = $activeTermId ?: $enrollment->term_id;

        $section = DB::table('tbl_sections')
            ->where('section_id', $sectionId)
            ->where('term_id', $termIdToUse)
            ->first();

        if (!$section) {
            return response()->json([
                'ok' => false,
                'message' => 'Section not found in active term.',
                'data' => [],
            ], 404);
        }

        $offerings = DB::table('tbl_section_offerings as o')
            ->join('tbl_subjects as s', 'o.subject_id', '=', 's.IDsubj')
            ->leftJoin('tbl_employees as f', 'o.instructor_id', '=', 'f.IDemployees')
            ->select([
                'o.offering_id',
                'o.section_id',
                'o.subject_id',
                'o.day_pattern',
                'o.time_start',
                'o.time_end',
                'o.room',
                'o.instructor_id',
                'o.student_limit',
                's.CourseCode',
                's.CourseDescription',
                's.Units',
                DB::raw("'" . addslashes($section->section_name) . "' as SectionName"),
                DB::raw('o.day_pattern as Day'),
                DB::raw("CONCAT(
                    COALESCE(TIME_FORMAT(o.time_start, '%h:%i %p'), ''),
                    CASE
                        WHEN o.time_start IS NOT NULL AND o.time_end IS NOT NULL THEN ' - '
                        ELSE ''
                    END,
                    COALESCE(TIME_FORMAT(o.time_end, '%h:%i %p'), '')
                ) as Time"),
                DB::raw("
                    CASE
                        WHEN f.IDemployees IS NOT NULL THEN
                            TRIM(CONCAT(
                                COALESCE(f.FacultyFirstName, ''), ' ',
                                COALESCE(f.FacultyMiddleName, ''), ' ',
                                COALESCE(f.FacultyLastName, '')
                            ))
                        ELSE NULL
                    END as InstructorName
                "),
            ])
            ->where('o.section_id', $sectionId)
            ->orderBy('s.CourseCode', 'asc')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $offerings,
        ]);
    }

    /**
     * Offerings search for active modal/grid loading
     */
    public function offeringsSearch(Request $request, int $enrollmentId)
    {
        $q = trim((string) $request->get('q', ''));
        $limit = (int) $request->get('limit', 25);

        if ($limit < 5) {
            $limit = 5;
        }
        if ($limit > 50) {
            $limit = 50;
        }

        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->select(['e.enrollment_id', 'e.term_id', 's.studID'])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json(['ok' => false, 'message' => 'Enrollment not found.'], 404);
        }

        $hasIDcurr = Schema::hasColumn('tbl_student_info', 'IDcurr');
        $hasIDyearlvl = Schema::hasColumn('tbl_student_info', 'IDyearlvl');

        $studExtra = DB::table('tbl_student_info')
            ->select(array_filter([
                $hasIDcurr ? 'IDcurr' : null,
                $hasIDyearlvl ? 'IDyearlvl' : null,
            ]))
            ->where('studID', $enrollment->studID)
            ->first();

        $IDcurr = ($studExtra && isset($studExtra->IDcurr)) ? (int) $studExtra->IDcurr : null;
        $IDyearlvl = ($studExtra && isset($studExtra->IDyearlvl)) ? (int) $studExtra->IDyearlvl : null;

        if (!$IDcurr || !$IDyearlvl) {
            return response()->json([
                'ok'      => false,
                'message' => 'Program/Year Level not finalized yet. Click Apply first.',
                'data'    => [],
            ]);
        }

        $activeTerm = DB::table('tbl_terms')->where('is_active', 1)->first();

        $activeSemester = 1;
        if ($activeTerm) {
            if (isset($activeTerm->semester) && in_array((int) $activeTerm->semester, [1, 2], true)) {
                $activeSemester = (int) $activeTerm->semester;
            } elseif (isset($activeTerm->term_name)) {
                $name = strtolower((string) $activeTerm->term_name);
                if (str_contains($name, '2')) {
                    $activeSemester = 2;
                }
            }
        }

        $mapTable = null;
        if (Schema::hasTable('tbl_currmap')) {
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
     * Add offering to current enrollment load
     */
    public function offeringsAdd(Request $request, int $enrollmentId)
    {
        $request->validate([
            'offering_id' => 'required|integer',
        ]);

        $offeringId = (int) $request->input('offering_id');

        $enrollment = DB::table('tbl_enrollments as e')
            ->join('tbl_student_info as s', 's.studID', '=', 'e.studID')
            ->select(['e.enrollment_id', 'e.term_id', 's.studID'])
            ->where('e.enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return back()->with('toast_error', 'Enrollment not found.');
        }

        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return back()->with('toast_error', 'Enrollment subjects table is missing.');
        }

        $offering = DB::table('tbl_section_offerings as o')
            ->join('tbl_subjects as s', 's.IDsubj', '=', 'o.subject_id')
            ->select([
                'o.offering_id',
                'o.section_id',
                'o.subject_id',
                'o.student_limit',
                's.CourseCode',
                's.PrereqSubj',
            ])
            ->where('o.offering_id', $offeringId)
            ->first();

        if (!$offering) {
            return back()->with('toast_error', 'Subject offering not found.');
        }

        $prereqCodes = [];
        if (!empty($offering->PrereqSubj)) {
            $prereqCodes = collect(explode(',', (string) $offering->PrereqSubj))
                ->map(fn ($c) => strtoupper(trim($c)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (count($prereqCodes) > 0) {
            $gradeTable = null;
            foreach (['tbl_student_grades', 'tbl_grades', 'tbl_grade_records'] as $t) {
                if (Schema::hasTable($t)) {
                    $gradeTable = $t;
                    break;
                }
            }

            if (!$gradeTable) {
                return back()->with('toast_error', 'Cannot add subject: prerequisite validation is not configured yet.');
            }

            $passedCodes = collect();

            if (Schema::hasColumn($gradeTable, 'studID') && Schema::hasColumn($gradeTable, 'CourseCode')) {
                $gq = DB::table($gradeTable)
                    ->where('studID', $enrollment->studID)
                    ->whereIn('CourseCode', $prereqCodes);

                if (Schema::hasColumn($gradeTable, 'remarks')) {
                    $gq->whereRaw("LOWER(remarks) IN ('passed','pass')");
                }

                $passedCodes = $gq->pluck('CourseCode')
                    ->map(fn ($c) => strtoupper(trim((string) $c)))
                    ->unique();
            } else {
                return back()->with('toast_error', 'Cannot add subject: grade table schema not wired yet.');
            }

            $missing = array_values(array_diff($prereqCodes, $passedCodes->all()));
            if (count($missing) > 0) {
                return back()->with('toast_error', 'Cannot add subject: missing prerequisite(s) ' . implode(', ', $missing) . '.');
            }
        }

        $exists = DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->where('offering_id', $offeringId)
            ->exists();

        if ($exists) {
            return back()->with('toast_success', 'Subject already added.');
        }

        $studentLimit = is_null($offering->student_limit) ? null : (int) $offering->student_limit;
        if (!is_null($studentLimit) && $studentLimit > 0) {
            $officialEnrolledCount = DB::table('tbl_student_studyload')
                ->where('offering_id', $offeringId)
                ->where('term_id', $enrollment->term_id)
                ->count();

            if ($officialEnrolledCount >= $studentLimit) {
                return back()->with('toast_error', 'Class is already full.');
            }
        }

        $insert = [
            'enrollment_id' => $enrollmentId,
            'offering_id'   => $offeringId,
            'subject_id'    => $offering->subject_id,
            'section_id'    => $offering->section_id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        if (Schema::hasColumn('tbl_enrollment_subjects', 'remarks')) {
            $insert['remarks'] = null;
        }

        DB::table('tbl_enrollment_subjects')->insert($insert);

        return back()->with('toast_success', 'Subject added successfully!');
    }

    /**
     * Remove subject from current enrollment load
     */
    public function subjectRemove(Request $request, int $enrollmentId, int $enrollSubjId)
    {
        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return redirect()->back()->with('error', 'Enrollment load table not found.');
        }

        DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->where('enroll_subj_id', $enrollSubjId)
            ->delete();

        return redirect()->back()->with('success', 'Subject removed.');
    }


    /**
     * Remove multiple subjects from current enrollment load
     */
    public function removeSelected(Request $request, int $enrollmentId)
    {
        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return response()->json([
                'ok' => false,
                'message' => 'Enrollment load table not found.'
            ], 500);
        }

        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'ok' => false,
                'message' => 'No subjects selected.'
            ], 422);
        }

        $ids = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json([
                'ok' => false,
                'message' => 'No valid subjects selected.'
            ], 422);
        }

        DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->whereIn('enroll_subj_id', $ids)
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Selected subjects removed successfully.'
        ]);
    }



    /**
     * Save current enrollment load into official student studyload table
     */
    public function saveStudyLoad(Request $request, int $enrollmentId)
    {
        if (!Schema::hasTable('tbl_enrollment_subjects')) {
            return response()->json([
                'ok' => false,
                'message' => 'Enrollment subjects table not found.'
            ], 500);
        }

        if (!Schema::hasTable('tbl_student_studyload')) {
            return response()->json([
                'ok' => false,
                'message' => 'Student studyload table not found.'
            ], 500);
        }

        $enrollment = DB::table('tbl_enrollments')
            ->select(['enrollment_id', 'studID', 'term_id'])
            ->where('enrollment_id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'ok' => false,
                'message' => 'Enrollment not found.'
            ], 404);
        }

        $activeTermId = DB::table('tbl_terms')
            ->where('is_active', 1)
            ->value('term_id');

        $termIdToUse = $activeTermId ?: $enrollment->term_id;

        if (empty($termIdToUse)) {
            return response()->json([
                'ok' => false,
                'message' => 'No active term found for saving study load.'
            ], 422);
        }

        $loadRows = DB::table('tbl_enrollment_subjects')
            ->where('enrollment_id', $enrollmentId)
            ->whereNotNull('offering_id')
            ->select(['offering_id'])
            ->get();

        if ($loadRows->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'No subjects found in Student Load.'
            ], 422);
        }

        $offeringIds = $loadRows
            ->pluck('offering_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($offeringIds)) {
            return response()->json([
                'ok' => false,
                'message' => 'No valid subject offerings found in Student Load.'
            ], 422);
        }

        DB::transaction(function () use ($enrollment, $termIdToUse, $offeringIds) {
            DB::table('tbl_student_studyload')
                ->where('student_id', $enrollment->studID)
                ->where('term_id', $termIdToUse)
                ->delete();

            $now = now();
            $insertRows = collect($offeringIds)
                ->map(function ($offeringId) use ($enrollment, $termIdToUse, $now) {
                    return [
                        'student_id' => $enrollment->studID,
                        'offering_id' => $offeringId,
                        'term_id' => $termIdToUse,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->values()
                ->all();

            DB::table('tbl_student_studyload')->insert($insertRows);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Student load saved successfully.',
            'saved_count' => count($offeringIds)
        ]);
    }

}
