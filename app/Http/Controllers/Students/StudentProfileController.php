<?php

namespace App\Http\Controllers\Students;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentProfileController extends Controller
{
    public function show(Request $request, string $studentNo)
    {
        // ✅ UI-first build:
        // Replace this sample array with real DB data once you confirm your table sources.
        // Keep it safe: do not touch enrollment/admissions logic here.

        $student = [
            'student_no' => $studentNo,
            'full_name' => 'Student Name',
            'college' => 'College / Department',
            'program' => 'Program',
            'year_level' => 'Year Level',
            'photo_url' => asset('images/default-student.png'), // put a placeholder image in public/images/
            'status_label' => 'ENROLLED',
            'status_badge' => 'ENROLLED',
            'active_term' => '2nd Semester',
            'active_term_id' => '15',
            'account_balance' => 12500.00,
            'admission_date' => 'Jun 10, 2024',
            'last_status_update' => 'Mar 15, 2024',
        ];

        $tab = $request->query('tab', 'overview');
        $allowedTabs = ['overview', 'admission', 'enrollment', 'grades', 'billing'];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'overview';
        }

        return view('students.profile.show', compact('student', 'tab'));
    }
}
