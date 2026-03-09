<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // 1) Check if section already exists for active term + program + year + section name
        $section = DB::table('tbl_sections')
            ->select('section_id')
            ->where('term_id', $activeTerm->term_id)
            ->where('program_id', $program_id)
            ->where('year_level', $year_level)
            ->where('section_name', $section_name)
            ->first();

        $subjectsTable = $this->resolveSubjectsTable();

        if ($section) {
            // EDIT MODE: Load existing offerings
            $rows = DB::table('tbl_section_offerings as o')
                ->join($subjectsTable . ' as s', 's.IDsubj', '=', 'o.subject_id')
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
        $IDcurr = DB::table('tbl_program')
            ->where('IDProgram', $program_id)
            ->value('IDcurr');

        if (!$IDcurr) {
            return response()->json([
                'ok' => false,
                'message' => 'Selected program has no curriculum (IDcurr) assigned.',
            ], 422);
        }

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
                $section = DB::table('tbl_sections')
                    ->where('term_id', $activeTerm->term_id)
                    ->where('program_id', $program_id)
                    ->where('year_level', $year_level)
                    ->where('section_name', $section_name)
                    ->first();
            }

            // Create if missing
            if (!$section) {
                $section_id = DB::table('tbl_sections')->insertGetId([
                    'term_id'      => $activeTerm->term_id,
                    'program_id'   => $program_id,
                    'year_level'   => $year_level,
                    'section_name' => $section_name,
                    'is_active'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                $section_id = $section->section_id;

                DB::table('tbl_sections')
                    ->where('section_id', $section_id)
                    ->update(['updated_at' => now()]);
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
    }}
