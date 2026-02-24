<?php

namespace App\Http\Controllers\Utilities;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class CurriculumController extends Controller
{
    public function index()
    {
        $curriculums = DB::table('tbl_curriculum')
            ->orderBy('CurrName')
            ->get();

        // NOTE: We'll use specific Blade filenames (per your rule)
        return view('utilities.curriculum.CurriculumIndex', compact('curriculums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CurrName' => 'required|max:50|unique:tbl_curriculum,CurrName',
        ]);

        DB::table('tbl_curriculum')->insert([
            'CurrName'  => $request->CurrName,
            'is_active' => 1,
        ]);

        return redirect()
            ->route('utilities.curriculum.index')
            ->with('success', 'Curriculum added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'CurrName' => 'required|max:50|unique:tbl_curriculum,CurrName,' . $id . ',IDcurr',
        ]);

        DB::table('tbl_curriculum')
            ->where('IDcurr', $id)
            ->update([
                'CurrName' => $request->CurrName,
            ]);

        return redirect()
            ->route('utilities.curriculum.index')
            ->with('success', 'Curriculum updated successfully.');
    }

/**
 * Curriculum Map (tbl_currmap)
 * Assign subjects to curriculum by year level + semester.
 * NOTE: Subject description column is CourseDescription (not SubjectTitle).
 */
public function mapIndex(Request $request)
{
    $IDcurr = $request->query('IDcurr');
    $IDyearlvl = $request->query('IDyearlvl');
    $semester = $request->query('semester');

    $curriculums = DB::table('tbl_curriculum')
        ->select('IDcurr', 'CurrName', 'is_active')
        ->orderBy('is_active', 'desc')
        ->orderBy('CurrName')
        ->get();

    $yearlevels = DB::table('tbl_yearlevel')
        ->select('IDyearlvl', 'YearLevelName')
        ->orderBy('IDyearlvl')
        ->get();

    $subjects = DB::table('tbl_subjects')
        ->select('IDsubj', 'CourseCode', 'CourseDescription', 'Units')
        ->orderBy('CourseCode')
        ->get();

    $mapped = collect();
    if (!empty($IDcurr)) {
        $mapped = DB::table('tbl_currmap as cm')
            ->join('tbl_subjects as s', 's.IDsubj', '=', 'cm.IDsubj')
            ->leftJoin('tbl_yearlevel as yl', 'yl.IDyearlvl', '=', 'cm.IDyearlvl')

            // Prerequisites (tbl_subject_prerequisites): list prereq course codes per subject
            ->leftJoin('tbl_subject_prerequisites as sp', 'sp.IDsubj', '=', 'cm.IDsubj')
            ->leftJoin('tbl_subjects as ps', 'ps.IDsubj', '=', 'sp.PrereqIDsubj')

            ->where('cm.IDcurr', $IDcurr)
            ->when($IDyearlvl, fn ($q) => $q->where('cm.IDyearlvl', $IDyearlvl))
            ->when($semester, fn ($q) => $q->where('cm.semester', $semester))
            ->select(
                'cm.CurrMapID',
                'cm.IDcurr',
                'cm.IDsubj',
                'cm.IDyearlvl',
                'cm.semester',
                'cm.is_required',
                's.CourseCode',
                's.CourseDescription',
                's.Units',
                'yl.YearLevelName',
                DB::raw("GROUP_CONCAT(DISTINCT ps.CourseCode ORDER BY ps.CourseCode SEPARATOR ', ') as PrereqCodes")
            )
            ->groupBy(
                'cm.CurrMapID',
                'cm.IDcurr',
                'cm.IDsubj',
                'cm.IDyearlvl',
                'cm.semester',
                'cm.is_required',
                's.CourseCode',
                's.CourseDescription',
                's.Units',
                'yl.YearLevelName'
            )
            ->orderBy('cm.IDyearlvl')
            ->orderByRaw("FIELD(cm.semester, 1, 2, 3)")
            ->orderBy('s.CourseCode')
            ->get();
    }

    return view('utilities.curriculum.CurriculumMap', compact(
        'curriculums', 'yearlevels', 'subjects', 'mapped',
        'IDcurr', 'IDyearlvl', 'semester'
    ));
}

public function mapStore(Request $request)
{
    $data = $request->validate([
        'IDcurr' => ['required', 'integer'],
        'IDsubj' => ['required', 'integer'],
        'IDyearlvl' => ['required', 'integer'],
        'semester' => ['required', 'integer'],
        'is_required' => ['nullable'],
    ]);

    $IDcurr = (int) $data['IDcurr'];
    $IDsubj = (int) $data['IDsubj'];
    $IDyearlvl = (int) $data['IDyearlvl'];
    $semester = (int) $data['semester'];
    $is_required = 1; // all subjects are required (UI toggle removed)

    $existing = DB::table('tbl_currmap as cm')
        ->leftJoin('tbl_yearlevel as yl', 'yl.IDyearlvl', '=', 'cm.IDyearlvl')
        ->where('cm.IDcurr', $IDcurr)
        ->where('cm.IDsubj', $IDsubj)
        ->select('cm.IDyearlvl', 'cm.semester', 'yl.YearLevelName')
        ->get();

    // Restriction: a subject can only appear once per curriculum (regardless of year/semester)
    if ($existing->count() > 0) {
        $locations = $existing->map(function ($r) {
            $yl = $r->YearLevelName ?? ('ID: '.$r->IDyearlvl);
            $sem = ((string)$r->semester === '1') ? '1st Semester' : (((string)$r->semester === '2') ? '2nd Semester' : 'Midyear');
            return $yl.' — '.$sem;
        })->unique()->values()->implode(', ');

        return back()->with('error', 'Subject already added in this curriculum. Found in: '.$locations.'.');
    }
    DB::table('tbl_currmap')->insert([
            'IDcurr' => $IDcurr,
            'IDsubj' => $IDsubj,
            'IDyearlvl' => $IDyearlvl,
            'semester' => $semester,
            'is_required' => $is_required,
        ]);

    return back()->with('success', 'Subject added to curriculum map.');
}

public function mapDelete(Request $request, $CurrMapID)
{
    DB::table('tbl_currmap')
        ->where('CurrMapID', $CurrMapID)
        ->delete();

    return back()->with('success', 'Mapping removed.');
}
}
