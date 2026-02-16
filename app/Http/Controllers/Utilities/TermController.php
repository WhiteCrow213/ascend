<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TermController extends Controller
{
    public function index()
    {
        $terms = DB::table('tbl_terms')
            ->orderByDesc('school_year')
            ->orderByRaw("FIELD(semester, '1','2','summer')")
            ->get();

        $activeTermId = DB::table('tbl_terms')->where('is_active', 1)->value('term_id');

        return view('utilities.terms', compact('terms', 'activeTermId'));
    }

    public function store(Request $request)
    {
                // Support BOTH:
        // 1) Old multi-term UI: semesters[] + start_date_1 / end_date_1 ...
        // 2) New single-term UI: semester + start_date + end_date
        if ($request->filled('semester') && !$request->filled('semesters')) {
            $data = $request->validate([
                'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
                'semester'    => ['required', 'in:1,2,summer'],
                'start_date'  => ['nullable', 'date'],
                'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            ]);

            // Normalize to the existing insert logic (expects semesters[] and per-sem keys)
            $data['semesters'] = [$data['semester']];

            if ($data['semester'] === 'summer') {
                $data['start_date_summer'] = $data['start_date'] ?? null;
                $data['end_date_summer']   = $data['end_date'] ?? null;
            } else {
                $data['start_date_' . $data['semester']] = $data['start_date'] ?? null;
                $data['end_date_' . $data['semester']]   = $data['end_date'] ?? null;
            }
        } else {
            $data = $request->validate([
                'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
                'semesters'   => ['required', 'array', 'min:1'],
                'semesters.*' => ['in:1,2,summer'],

                // Optional dates (nullable and harmless)
                'start_date_1'      => ['nullable', 'date'],
                'end_date_1'        => ['nullable', 'date'],
                'start_date_2'      => ['nullable', 'date'],
                'end_date_2'        => ['nullable', 'date'],
                'start_date_summer' => ['nullable', 'date'],
                'end_date_summer'   => ['nullable', 'date'],
            ]);
        }

        $sy   = $data['school_year'];
        $sems = $data['semesters'];

        DB::transaction(function () use ($sy, $sems, $data) {
            $now = now();

            // 1) Ensure School Year exists (if table is present)
            if (Schema::hasTable('tbl_school_year') && Schema::hasColumn('tbl_school_year', 'school_year')) {
                $existsSy = DB::table('tbl_school_year')->where('school_year', $sy)->exists();

                if (!$existsSy) {
                    $insert = ['school_year' => $sy];

                    // Only add timestamps if the columns exist
                    if (Schema::hasColumn('tbl_school_year', 'created_at')) $insert['created_at'] = $now;
                    if (Schema::hasColumn('tbl_school_year', 'updated_at')) $insert['updated_at'] = $now;

                    DB::table('tbl_school_year')->insert($insert);
                }
            }

            // 2) Prevent duplicates in tbl_terms (SY + semester)
            $existing = DB::table('tbl_terms')
                ->where('school_year', $sy)
                ->pluck('semester')
                ->all();

            $toInsert = [];

            foreach ($sems as $sem) {
                if (in_array($sem, $existing, true)) continue;

                $sdKey = $sem === 'summer' ? 'start_date_summer' : "start_date_{$sem}";
                $edKey = $sem === 'summer' ? 'end_date_summer'   : "end_date_{$sem}";

                $toInsert[] = [
                    'school_year' => $sy,
                    'semester'    => $sem,
                    'start_date'  => $data[$sdKey] ?? null,
                    'end_date'    => $data[$edKey] ?? null,
                    'is_active'   => 0,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            if (!empty($toInsert)) {
                DB::table('tbl_terms')->insert($toInsert);
            }
        });

        return back()->with('ok', 'School year / terms saved.');
    }


    public function update(Request $request, int $termId)
    {
        $data = $request->validate([
            'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'semester'    => ['required', 'in:1,2,summer'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Enforce consecutive school year (e.g. 2026-2027)
        [$y1, $y2] = array_pad(explode('-', $data['school_year']), 2, null);
        if (!$y1 || !$y2 || ((int) $y2) !== ((int) $y1) + 1) {
            return back()
                ->withErrors(['school_year' => 'School year must be consecutive (e.g. 2026-2027).'])
                ->withInput();
        }

        // Prevent duplicates (same SY + semester, excluding current row)
        $dup = DB::table('tbl_terms')
            ->where('school_year', $data['school_year'])
            ->where('semester', $data['semester'])
            ->where('term_id', '!=', $termId)
            ->exists();

        if ($dup) {
            return back()
                ->withErrors(['semester' => 'This term already exists for the selected school year.'])
                ->withInput();
        }

        $update = [
            'school_year' => $data['school_year'],
            'semester'    => $data['semester'],
            'start_date'  => $data['start_date'] ?? null,
            'end_date'    => $data['end_date'] ?? null,
        ];

        if (Schema::hasColumn('tbl_terms', 'updated_at')) {
            $update['updated_at'] = now();
        }

        DB::table('tbl_terms')->where('term_id', $termId)->update($update);

        return back()->with('ok', 'Term updated.');
    }

    public function setActive(int $termId)
    {
        DB::transaction(function () use ($termId) {
            DB::table('tbl_terms')->update(['is_active' => 0]);
            DB::table('tbl_terms')->where('term_id', $termId)->update(['is_active' => 1]);
        });

        return back()->with('ok', 'Active term updated.');
    }
}
