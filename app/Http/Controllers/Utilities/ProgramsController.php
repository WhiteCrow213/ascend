<?php

namespace App\Http\Controllers\Utilities;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class ProgramsController extends Controller
{
    public function index()
    {
        $programs = DB::table('tbl_program')
            ->orderBy('program_code')
            ->get();

        // Default curriculum options (active only)
        $curriculums = DB::table('tbl_curriculum')
            ->where('is_active', 1)
            ->orderBy('CurrName')
            ->get();

        return view('utilities.programs.index', compact('programs', 'curriculums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_code' => 'required|max:20|unique:tbl_program,program_code',
            'program_name' => 'required|max:150',
            'IDcurr'       => 'nullable|integer|exists:tbl_curriculum,IDcurr',
        ]);

        DB::table('tbl_program')->insert([
            'program_code' => $request->program_code,
            'program_name' => $request->program_name,
            'department'   => $request->department,
            'IDcurr'       => $request->filled('IDcurr') ? (int) $request->IDcurr : null,
            'is_active'    => 1,
        ]);

        return redirect()->route('utilities.programs.index')->with('success', 'Program added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'program_code' => 'required|max:20|unique:tbl_program,program_code,' . $id . ',IDProgram',
            'program_name' => 'required|max:150',
            'IDcurr'       => 'nullable|integer|exists:tbl_curriculum,IDcurr',
        ]);

        DB::table('tbl_program')
            ->where('IDProgram', $id)
            ->update([
                'program_code' => $request->program_code,
                'program_name' => $request->program_name,
                'department'   => $request->department,
                'IDcurr'       => $request->filled('IDcurr') ? (int) $request->IDcurr : null,
            ]);

        return redirect()->route('utilities.programs.index')->with('success', 'Program updated successfully.');
    }
}
