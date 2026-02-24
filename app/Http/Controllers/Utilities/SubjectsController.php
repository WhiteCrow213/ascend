<?php

namespace App\Http\Controllers\Utilities;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class SubjectsController extends Controller
{
    public function index()
    {
        $subjects = DB::table('tbl_subjects as s')
            ->leftJoin('tbl_subject_prerequisites as sp', 's.IDsubj', '=', 'sp.IDsubj')
            ->leftJoin('tbl_subjects as p', 'sp.PrereqIDsubj', '=', 'p.IDsubj')
            ->select(
                's.*',
                DB::raw("GROUP_CONCAT(p.CourseCode ORDER BY p.CourseCode SEPARATOR ', ') as PrereqList")
            )
            ->groupBy(
                's.IDsubj',
                's.CourseCode',
                's.CourseDescription',
                's.Units',
                's.LectureUnits',
                's.LabUnits',
                's.PrereqSubj'
            )
            ->orderBy('s.CourseCode')
            ->get();

        // For prerequisite dropdown options (ID + label fields only)
        $subjectOptions = DB::table('tbl_subjects')
            ->select('IDsubj', 'CourseCode', 'CourseDescription')
            ->orderBy('CourseCode')
            ->get();

        return view('utilities.subjects.SubjectsIndex', compact('subjects', 'subjectOptions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'CourseCode'        => 'required|max:20|unique:tbl_subjects,CourseCode',
            'CourseDescription' => 'required|max:255',
            'Units'             => 'required|numeric|min:0',
            'LectureUnits'      => 'required|numeric|min:0',
            'LabUnits'          => 'required|numeric|min:0',
            'PrereqIDs'         => 'nullable|array',
            'PrereqIDs.*'       => 'integer',
        ]);

        // Insert subject first
        $newId = DB::table('tbl_subjects')->insertGetId([
            'CourseCode'        => $request->CourseCode,
            'CourseDescription' => $request->CourseDescription,
            'Units'             => $request->Units,
            'LectureUnits'      => $request->LectureUnits,
            'LabUnits'          => $request->LabUnits,
        ], 'IDsubj');

        // Insert prerequisite mappings (if any)
        $prereqIds = $request->input('PrereqIDs', []);
        if (is_array($prereqIds) && count($prereqIds) > 0) {
            $rows = [];
            foreach ($prereqIds as $pid) {
                // Prevent self-reference just in case
                if ((int)$pid === (int)$newId) continue;

                $rows[] = [
                    'IDsubj'        => (int)$newId,
                    'PrereqIDsubj'  => (int)$pid,
                    'created_at'    => now(),
                ];
            }
            if (count($rows) > 0) {
                DB::table('tbl_subject_prerequisites')->insert($rows);
            }
        }

        return redirect()
            ->route('utilities.subjects.index')
            ->with('success', 'Subject added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'CourseCode'        => 'required|max:20|unique:tbl_subjects,CourseCode,' . $id . ',IDsubj',
            'CourseDescription' => 'required|max:255',
            'Units'             => 'required|numeric|min:0',
            'LectureUnits'      => 'required|numeric|min:0',
            'LabUnits'          => 'required|numeric|min:0',
            'PrereqIDs'         => 'nullable|array',
            'PrereqIDs.*'       => 'integer',
        ]);

        DB::table('tbl_subjects')
            ->where('IDsubj', $id)
            ->update([
                'CourseCode'        => $request->CourseCode,
                'CourseDescription' => $request->CourseDescription,
                'Units'             => $request->Units,
                'LectureUnits'      => $request->LectureUnits,
                'LabUnits'          => $request->LabUnits,
            ]);

        // Refresh prerequisite mappings: delete then insert
        DB::table('tbl_subject_prerequisites')
            ->where('IDsubj', $id)
            ->delete();

        $prereqIds = $request->input('PrereqIDs', []);
        if (is_array($prereqIds) && count($prereqIds) > 0) {
            $rows = [];
            foreach ($prereqIds as $pid) {
                if ((int)$pid === (int)$id) continue;
                $rows[] = [
                    'IDsubj'        => (int)$id,
                    'PrereqIDsubj'  => (int)$pid,
                    'created_at'    => now(),
                ];
            }
            if (count($rows) > 0) {
                DB::table('tbl_subject_prerequisites')->insert($rows);
            }
        }

        return redirect()
            ->route('utilities.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }
}
