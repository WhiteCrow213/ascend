<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tbl_employees');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('FacultyLastName', 'like', "%{$search}%")
                  ->orWhere('FacultyFirstName', 'like', "%{$search}%")
                  ->orWhere('FacultyMiddleName', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('employment_type', 'like', "%{$search}%")
                  ->orWhere('employment_status', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allowedSorts = [
            'FacultyLastName',
            'FacultyFirstName',
            'employee_number',
            'position',
            'employment_type',
            'employment_status',
            'contact_number',
            'created_at',
        ];

        $sort = $request->get('sort', 'FacultyLastName');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'FacultyLastName';
        }

        $employees = $query
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->appends($request->query());

        $stats = [
            'total' => DB::table('tbl_employees')->count(),
            'full_time' => DB::table('tbl_employees')->where('employment_status', 'Full time')->count(),
            'part_time' => DB::table('tbl_employees')->where('employment_status', 'Part time')->count(),
            'instructors' => DB::table('tbl_employees')->where('position', 'Instructor')->count(),
        ];

        return view('faculty.FacultyIndex', compact('employees', 'stats', 'sort', 'direction'));
    }

    public function create()
    {
        $colleges = DB::table('tbl_colleges')->orderBy('college_name')->get();
        return view('faculty.FacultyCreate', compact('colleges'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'FacultyLastName'        => ['required', 'string', 'max:100'],
            'FacultyFirstName'       => ['required', 'string', 'max:100'],
            'FacultyMiddleName'      => ['nullable', 'string', 'max:100'],
            'FacultySuffixName'      => ['nullable', 'string', 'max:20'],
            'birthdate'              => ['nullable', 'date', 'before_or_equal:today'],
            'Religion'               => ['nullable', 'string', 'max:100'],
            'civil_status'           => ['nullable', 'string', 'max:30'],
            'contact_number'         => ['nullable', 'string', 'max:20'],
            'email'                  => ['nullable', 'email', 'max:150'],
            'home_address'           => ['nullable', 'string', 'max:1000'],
            'prc_license_number'     => ['nullable', 'string', 'max:50'],
            'pagibig_number'         => ['nullable', 'string', 'max:50'],
            'tin_number'             => ['nullable', 'string', 'max:50'],
            'gsis_number'            => ['nullable', 'string', 'max:50'],
            'employee_number'        => ['nullable', 'string', 'max:50'],
            'position'               => ['nullable', 'string', 'max:100'],
            'employment_type'        => ['nullable', 'string', 'max:50'],
            'employment_status'      => ['nullable', 'string', 'max:50'],
            'collegeID'              => ['nullable','exists:tbl_colleges,collegeID'],
            'undergraduate_degree'   => ['nullable', 'string', 'max:255'],
            'masters_degree'         => ['nullable', 'string', 'max:255'],
            'doctoral_degree'        => ['nullable', 'string', 'max:255'],
            'photo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if (! empty($validated['contact_number'])) {
            $digits = preg_replace('/\D+/', '', $validated['contact_number']);

            if (strlen($digits) === 11) {
                $validated['contact_number'] = substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7, 4);
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('faculty-photos', 'public');
        }

        DB::table('tbl_employees')->insert([
            'FacultyLastName'      => $validated['FacultyLastName'],
            'FacultyFirstName'     => $validated['FacultyFirstName'],
            'FacultyMiddleName'    => $validated['FacultyMiddleName'] ?? null,
            'FacultySuffixName'    => $validated['FacultySuffixName'] ?? null,
            'birthdate'            => $validated['birthdate'] ?? null,
            'Religion'             => $validated['Religion'] ?? null,
            'contact_number'       => $validated['contact_number'] ?? null,
            'email'                => $validated['email'] ?? null,
            'home_address'         => $validated['home_address'] ?? null,
            'civil_status'         => $validated['civil_status'] ?? null,
            'prc_license_number'   => $validated['prc_license_number'] ?? null,
            'pagibig_number'       => $validated['pagibig_number'] ?? null,
            'tin_number'           => $validated['tin_number'] ?? null,
            'gsis_number'          => $validated['gsis_number'] ?? null,
            'employee_number'      => $validated['employee_number'] ?? null,
            'position'             => $validated['position'] ?? null,
            'employment_type'      => $validated['employment_type'] ?? null,
            'employment_status'    => $validated['employment_status'] ?? null,
            'collegeID'            => $validated['collegeID'] ?? null,
            'undergraduate_degree' => $validated['undergraduate_degree'] ?? null,
            'masters_degree'       => $validated['masters_degree'] ?? null,
            'doctoral_degree'      => $validated['doctoral_degree'] ?? null,
            'photo_path'           => $photoPath,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()
            ->route('faculty.index')
            ->with('success', 'Faculty record created successfully.');
    }

    public function show($id)
    {
        $employee = DB::table('tbl_employees')->where('IDemployees', $id)->first();

        abort_if(! $employee, 404);

        return view('faculty.FacultyShow', compact('employee'));
    }

    public function edit($id)
    {
        $employee = DB::table('tbl_employees')->where('IDemployees', $id)->first();

        abort_if(! $employee, 404);

        return view('faculty.FacultyEdit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        return back()->with('info', 'Update logic is not implemented yet.');
    }

    public function destroy($id)
    {
        return back()->with('info', 'Delete logic is not implemented yet.');
    }
}
