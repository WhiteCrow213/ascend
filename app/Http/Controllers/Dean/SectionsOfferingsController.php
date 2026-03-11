<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SectionsOfferingsController extends Controller
{
    /**
     * Show Sections & Offerings page
     */
    public function index()
    {
        $programs = DB::table('tbl_program')
            ->select('IDProgram', 'program_code', 'program_name')
            ->where('is_active', 1)
            ->orderBy('program_code')
            ->get();

        $yearLevels = DB::table('tbl_yearlevel')
            ->select('IDyearlvl', 'YearLevelName')
            ->orderBy('IDyearlvl')
            ->get();

        return view('dean.SectionsOfferingsForm', [
            'programs'   => $programs,
            'yearLevels' => $yearLevels,
        ]);
    }

    /**
     * Load subjects for scheduling.
     * - If section exists (active term + program + year level + section name): load existing offerings for editing
     * - If section does not exist: load curriculum subjects (TBA schedules)
     */
    public function loadSubjects(Request $request)
    {
        $program_id   = $request->input('program_id');
        $year_level   = $request->input('year_level');
        $section_name = trim((string) $request->input('section_name'));

        if (!$program_id || !$year_level || $section_name === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Program, Year Level, and Section Name are required.',
            ], 422);
        }

        // Active term (ASCEND rule: scheduling is only for active term)
        $activeTerm = DB::table('tbl_terms')
            ->select('term_id', 'semester', 'is_active')
            ->where('is_active', 1)
            ->first();

        if (!$activeTerm) {
            return response()->json([
                'ok' => false,
                'message' => 'No active term found. Please set an active term in Utilities.',
            ], 422);
        }

        $semester = (string) $activeTerm->semester; // expected: '1', '2', or 'summer'

        $IDcurr = DB::table('tbl_program')
            ->where('IDProgram', $program_id)
            ->value('IDcurr');

        if (!$IDcurr) {
            return response()->json([
                'ok' => false,
                'message' => 'Selected program has no curriculum (IDcurr) assigned.',
            ], 422);
        }

        // 1) Check if section already exists for active term + program + year + section name
        $sectionQuery = DB::table('tbl_sections')
            ->select('section_id')
            ->where('term_id', $activeTerm->term_id)
            ->where('program_id', $program_id)
            ->where('year_level', $year_level)
            ->where('section_name', $section_name);

        if (Schema::hasColumn('tbl_sections', 'IDcurr')) {
            $sectionQuery->where('IDcurr', $IDcurr);
        }

        $section = $sectionQuery->first();

        $subjectsTable = $this->resolveSubjectsTable();

        if ($section) {
            // EDIT MODE: Load existing offerings
            $rows = DB::table('tbl_section_offerings as o')
                ->join($subjectsTable . ' as s', 's.IDsubj', '=', 'o.subject_id')
                ->leftJoin('tbl_employees as e', 'e.IDemployees', '=', 'o.instructor_id')
                ->select([
                    'o.subject_id',
                    's.CourseCode as subject_code',
                    's.CourseDescription as subject_title',
                    's.Units as units',
                    'o.day_pattern as day',
                    'o.time_start',
                    'o.time_end',
                    'o.room',
                    'o.student_limit as seat_limit',
                    'o.instructor_id',
                    DB::raw("CASE 
                        WHEN e.IDemployees IS NULL THEN ''
                        WHEN e.FacultyFirstName IS NULL OR e.FacultyFirstName = '' THEN e.FacultyLastName
                        ELSE CONCAT(UPPER(LEFT(e.FacultyFirstName, 1)), '.', e.FacultyLastName)
                    END as instructor_display"),
                    DB::raw("(SELECT COUNT(*) 
                              FROM tbl_student_studyload sl 
                              WHERE sl.offering_id = o." . $this->resolveSectionOfferingsPrimaryKey() . " 
                                AND sl.term_id = " . (int) $activeTerm->term_id . ") as enrolled_count"),
                ])
                ->where('o.section_id', $section->section_id)
                ->orderBy('s.CourseCode')
                ->get();

            return response()->json([
                'ok' => true,
                'mode' => 'edit',
                'term_id' => $activeTerm->term_id,
                'section_id' => $section->section_id,
                'rows' => $rows,
            ]);
        }

        // CREATE MODE: Load curriculum subjects (TBA schedules)
        $rows = DB::table('tbl_currmap as cm')
            ->join($subjectsTable . ' as s', 's.IDsubj', '=', 'cm.IDsubj')
            ->select([
                'cm.IDsubj as subject_id',
                's.CourseCode as subject_code',
                's.CourseDescription as subject_title',
                's.Units as units',
                DB::raw('NULL as day'),
                DB::raw('NULL as time_start'),
                DB::raw('NULL as time_end'),
                DB::raw('NULL as room'),
                DB::raw('NULL as seat_limit'),
                DB::raw('NULL as instructor_id'),
                DB::raw("'' as instructor_display"),
                DB::raw('0 as enrolled_count'),
            ])
            ->where('cm.IDcurr', $IDcurr)
            ->where('cm.IDyearlvl', $year_level)
            ->where('cm.semester', $semester)
            ->orderBy('s.CourseCode')
            ->get();

        return response()->json([
            'ok' => true,
            'mode' => 'create',
            'term_id' => $activeTerm->term_id,
            'section_id' => null,
            'rows' => $rows,
        ]);
    }

    /**
     * Resolve the subjects master table name without assuming it.
     * We look for a table that contains: IDsubj, CourseCode, CourseDescription, Units.
     */
    private function resolveSubjectsTable(): string
    {
        static $cached = null;
        if ($cached) {
            return $cached;
        }

        $dbName = DB::getDatabaseName();

        $sql = <<<SQL
SELECT TABLE_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = ?
  AND COLUMN_NAME IN ('IDsubj','CourseCode','CourseDescription','Units')
GROUP BY TABLE_NAME
HAVING SUM(CASE WHEN COLUMN_NAME = 'IDsubj' THEN 1 ELSE 0 END) = 1
   AND SUM(CASE WHEN COLUMN_NAME = 'CourseCode' THEN 1 ELSE 0 END) = 1
   AND SUM(CASE WHEN COLUMN_NAME = 'CourseDescription' THEN 1 ELSE 0 END) = 1
   AND SUM(CASE WHEN COLUMN_NAME = 'Units' THEN 1 ELSE 0 END) = 1
ORDER BY TABLE_NAME
LIMIT 1
SQL;

        $rows = DB::select($sql, [$dbName]);

        if (!$rows || !isset($rows[0]->TABLE_NAME)) {
            abort(500, 'Subjects table not found. Expected columns: IDsubj, CourseCode, CourseDescription, Units.');
        }

        $cached = $rows[0]->TABLE_NAME;
        return $cached;

    }

    /**
     * Resolve the primary key column of tbl_section_offerings without assuming it.
     */
    private function resolveSectionOfferingsPrimaryKey(): string
    {
        static $cachedPrimaryKey = null;
        if ($cachedPrimaryKey) {
            return $cachedPrimaryKey;
        }

        $dbName = DB::getDatabaseName();

        $sql = <<<SQL
SELECT k.COLUMN_NAME
FROM information_schema.TABLE_CONSTRAINTS tc
JOIN information_schema.KEY_COLUMN_USAGE k
  ON tc.CONSTRAINT_NAME = k.CONSTRAINT_NAME
 AND tc.TABLE_SCHEMA = k.TABLE_SCHEMA
 AND tc.TABLE_NAME = k.TABLE_NAME
WHERE tc.TABLE_SCHEMA = ?
  AND tc.TABLE_NAME = 'tbl_section_offerings'
  AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
ORDER BY k.ORDINAL_POSITION
LIMIT 1
SQL;

        $rows = DB::select($sql, [$dbName]);

        if (!$rows || !isset($rows[0]->COLUMN_NAME)) {
            abort(500, 'Primary key for tbl_section_offerings was not found.');
        }

        $cachedPrimaryKey = $rows[0]->COLUMN_NAME;
        return $cachedPrimaryKey;
    }

        /**
     * Save / update schedules.
     * - Creates section if not existing yet (active term + program + year_level + section_name)
     * - Updates existing offerings if section already exists
     * - Allows TBA schedule values (NULL day/time/room/limit)
     */
    public function save(Request $request)
    {
        $program_id   = $request->input('program_id');
        $year_level   = $request->input('year_level');
        $section_name = trim((string) $request->input('section_name'));
        $section_id   = $request->input('section_id');
        $rows         = $request->input('rows', []);

        if (!$program_id || !$year_level || $section_name === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Program, Year Level, and Section Name are required.',
            ], 422);
        }

        if (!is_array($rows) || count($rows) === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'No subjects to save. Click Load Subjects first.',
            ], 422);
        }

        // Active term only
        $activeTerm = DB::table('tbl_terms')
            ->select('term_id', 'semester', 'is_active')
            ->where('is_active', 1)
            ->first();

        if (!$activeTerm) {
            return response()->json([
                'ok' => false,
                'message' => 'No active term found. Please set an active term in Utilities.',
            ], 422);
        }

        $IDcurr = DB::table('tbl_program')
            ->where('IDProgram', $program_id)
            ->value('IDcurr');

        if (!$IDcurr) {
            return response()->json([
                'ok' => false,
                'message' => 'Selected program has no curriculum (IDcurr) assigned.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $section = null;

            // If UI already has section_id (edit mode), validate it
            if ($section_id) {
                $section = DB::table('tbl_sections')
                    ->where('section_id', $section_id)
                    ->first();
            }

            // Otherwise, find by natural key in active term
            if (!$section) {
                $sectionQuery = DB::table('tbl_sections')
                    ->where('term_id', $activeTerm->term_id)
                    ->where('program_id', $program_id)
                    ->where('year_level', $year_level)
                    ->where('section_name', $section_name);

                if (Schema::hasColumn('tbl_sections', 'IDcurr')) {
                    $sectionQuery->where('IDcurr', $IDcurr);
                }

                $section = $sectionQuery->first();
            }

            // Create if missing
            if (!$section) {
                $insertSection = [
                    'term_id'      => $activeTerm->term_id,
                    'program_id'   => $program_id,
                    'year_level'   => $year_level,
                    'section_name' => $section_name,
                    'is_active'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                if (Schema::hasColumn('tbl_sections', 'IDcurr')) {
                    $insertSection['IDcurr'] = $IDcurr;
                }

                $section_id = DB::table('tbl_sections')->insertGetId($insertSection);
            } else {
                $section_id = $section->section_id;

                $sectionUpdate = [
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('tbl_sections', 'IDcurr')) {
                    $sectionUpdate['IDcurr'] = $IDcurr;
                }

                DB::table('tbl_sections')
                    ->where('section_id', $section_id)
                    ->update($sectionUpdate);
            }

            // Upsert offerings per subject
            foreach ($rows as $row) {
                $subject_id = $row['subject_id'] ?? null;
                if (!$subject_id) {
                    continue;
                }

                $day        = isset($row['day']) && trim((string) $row['day']) !== '' ? trim((string) $row['day']) : null;
                $time_start = isset($row['time_start']) && trim((string) $row['time_start']) !== '' ? trim((string) $row['time_start']) : null;
                $time_end   = isset($row['time_end']) && trim((string) $row['time_end']) !== '' ? trim((string) $row['time_end']) : null;
                $room       = isset($row['room']) && trim((string) $row['room']) !== '' ? trim((string) $row['room']) : null;
                $instructor_id = isset($row['instructor_id']) && trim((string) $row['instructor_id']) !== ''
                    ? (int) $row['instructor_id']
                    : null;
                $instructor_display = isset($row['instructor_display']) && trim((string) $row['instructor_display']) !== ''
                    ? trim((string) $row['instructor_display'])
                    : null;

                if (!$instructor_id && $instructor_display) {
                    $resolvedInstructor = DB::table('tbl_employees')
                        ->select('IDemployees')
                        ->where('position', 'Instructor')
                        ->where(function ($query) use ($instructor_display) {
                            $query->where(DB::raw("CONCAT(UPPER(LEFT(FacultyFirstName, 1)), '.', FacultyLastName)"), $instructor_display)
                                  ->orWhere('FacultyLastName', $instructor_display);
                        })
                        ->first();

                    if ($resolvedInstructor) {
                        $instructor_id = (int) $resolvedInstructor->IDemployees;
                    }
                }

                if ($instructor_id) {
                    $instructorExists = DB::table('tbl_employees')
                        ->where('IDemployees', $instructor_id)
                        ->where('position', 'Instructor')
                        ->exists();

                    if (!$instructorExists) {
                        throw new \RuntimeException('Selected instructor is invalid or no longer available.');
                    }
                }

                if ($instructor_display && !$instructor_id) {
                    throw new \RuntimeException('Instructor was typed but could not be matched. Please select a valid instructor.');
                }

                $seat_limit = null;
                if (isset($row['seat_limit']) && trim((string) $row['seat_limit']) !== '') {
                    $seatLimitRaw = trim((string) $row['seat_limit']);

                    if (!ctype_digit($seatLimitRaw)) {
                        throw new \RuntimeException('Limit must contain numbers only.');
                    }

                    $seat_limit = (int) $seatLimitRaw;

                    if ($seat_limit <= 0) {
                        throw new \RuntimeException('Limit must be greater than 0.');
                    }
                }

                $offeringPrimaryKey = $this->resolveSectionOfferingsPrimaryKey();

                $existingOffering = DB::table('tbl_section_offerings')
                    ->select($offeringPrimaryKey)
                    ->where('section_id', $section_id)
                    ->where('subject_id', $subject_id)
                    ->first();

                if ($existingOffering && $seat_limit !== null) {
                    $offeringId = $existingOffering->{$offeringPrimaryKey};

                    $currentEnrolled = DB::table('tbl_student_studyload')
                        ->where('offering_id', $offeringId)
                        ->where('term_id', $activeTerm->term_id)
                        ->count();

                    if ($currentEnrolled > $seat_limit) {
                        throw new \RuntimeException(
                            'Limit cannot be set to ' . $seat_limit .
                            ' because ' . $currentEnrolled . ' student(s) are already officially enrolled in this subject.'
                        );
                    }
                }

                // Prevent instructor schedule conflicts in the same active term.
                if ($instructor_id && $day && $time_start && $time_end) {
                    $instructorConflict = DB::table('tbl_section_offerings as o')
                        ->join('tbl_sections as s', 's.section_id', '=', 'o.section_id')
                        ->where('s.term_id', $activeTerm->term_id)
                        ->where('o.instructor_id', $instructor_id)
                        ->where('o.day_pattern', $day)
                        ->where('o.time_start', '<', $time_end)
                        ->where('o.time_end', '>', $time_start)
                        ->where(function ($q) use ($section_id, $subject_id) {
                            $q->where('o.section_id', '!=', $section_id)
                              ->orWhere('o.subject_id', '!=', $subject_id);
                        })
                        ->select('o.time_start', 'o.time_end')
                        ->first();

                    if ($instructorConflict) {
                        throw new \RuntimeException(
                            'Instructor conflict: this instructor already has a class scheduled on ' . $day .
                            ' from ' . $instructorConflict->time_start . ' to ' . $instructorConflict->time_end . '.'
                        );
                    }
                }

                // Prevent room/schedule conflicts in the same active term.
                // Rule requested: no two schedules should exist on the same room at the same time.
                if ($day && $room && $time_start && $time_end) {
                    $conflict = DB::table('tbl_section_offerings as o')
                        ->join('tbl_sections as s', 's.section_id', '=', 'o.section_id')
                        ->where('s.term_id', $activeTerm->term_id)
                        ->where('o.room', $room)
                        ->where('o.day_pattern', $day)
                        ->where('o.time_start', '<', $time_end)
                        ->where('o.time_end', '>', $time_start)
                        ->where(function ($q) use ($section_id, $subject_id) {
                            $q->where('o.section_id', '!=', $section_id)
                              ->orWhere('o.subject_id', '!=', $subject_id);
                        })
                        ->select('s.section_name', 'o.subject_id', 'o.time_start', 'o.time_end', 'o.room', 'o.day_pattern')
                        ->first();

                    if ($conflict) {
                        throw new \RuntimeException(
                            'Schedule conflict: room ' . $room .
                            ' is already used on ' . $day .
                            ' from ' . $conflict->time_start . ' to ' . $conflict->time_end . '.'
                        );
                    }
                }

                DB::table('tbl_section_offerings')->updateOrInsert(
                    [
                        'section_id' => $section_id,
                        'subject_id' => $subject_id,
                    ],
                    [
                        'day_pattern'   => $day,
                        'time_start'    => $time_start,
                        'time_end'      => $time_end,
                        'room'          => $room,
                        'instructor_id' => $instructor_id,
                        'student_limit' => $seat_limit,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Saved.',
                'section_id' => $section_id,
                'term_id' => $activeTerm->term_id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'message' => 'Save failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchInstructor(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rows = DB::table('tbl_employees')
            ->where('position', 'Instructor')
            ->where(function ($query) use ($q) {
                $query->where('FacultyLastName', 'like', "%{$q}%")
                      ->orWhere('FacultyFirstName', 'like', "%{$q}%");
            })
            ->orderBy('FacultyLastName')
            ->orderBy('FacultyFirstName')
            ->limit(10)
            ->get();

        return response()->json(
            $rows->map(function ($r) {
                $firstInitial = !empty($r->FacultyFirstName)
                    ? strtoupper(substr($r->FacultyFirstName, 0, 1)) . '.'
                    : '';

                return [
                    'id' => $r->IDemployees,
                    'display' => $firstInitial . $r->FacultyLastName,
                ];
            })->values()
        );
    }

}