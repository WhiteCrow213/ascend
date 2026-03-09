<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyController extends Controller
{
    /**
     * Faculty index
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = DB::table('tbl_employees')
            ->select([
                'IDemployees',
                'FacultyLastName',
                'FacultyFirstName',
                'FacultyMiddleName',
                'FacultySuffixName',
                'employment_type',
                'employment_status',
                'contact_number',
                'email',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('FacultyLastName', 'like', "%{$search}%")
                  ->orWhere('FacultyFirstName', 'like', "%{$search}%")
                  ->orWhere('FacultyMiddleName', 'like', "%{$search}%")
                  ->orWhere('FacultySuffixName', 'like', "%{$search}%")
                  ->orWhere('employment_type', 'like', "%{$search}%")
                  ->orWhere('employment_status', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query
            ->orderBy('FacultyLastName')
            ->orderBy('FacultyFirstName')
            ->paginate(10)
            ->appends($request->query());

        return view('faculty.FacultyIndex', compact('employees', 'search'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('faculty.FacultyCreate');
    }

    /**
     * Store faculty
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show faculty details
     */
    public function show($id)
    {
        $employee = DB::table('tbl_employees')
            ->where('IDemployees', $id)
            ->first();

        abort_if(!$employee, 404);

        return view('faculty.FacultyShow', compact('employee'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $employee = DB::table('tbl_employees')
            ->where('IDemployees', $id)
            ->first();

        abort_if(!$employee, 404);

        return view('faculty.FacultyEdit', compact('employee'));
    }

    /**
     * Update faculty
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Delete faculty
     */
    public function destroy($id)
    {
        //
    }
}
