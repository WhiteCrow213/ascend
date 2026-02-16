<?php

namespace App\Http\Controllers\Admissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentInfo;

class PreRegistrationStatusController extends Controller
{
    /**
     * Update application status (Approve / Reject)
     *
     * URL: PUT /admission/prereg/{studID}/status
     */
    public function updateStatus(Request $request, $studID)
    {
        // 1. Validate input
        $request->validate([
            'application_status' => 'required|in:approved,rejected',
        ]);

        // 2. Update the applicant record
        StudentInfo::where('studID', $studID)->update([
            'application_status' => $request->application_status,
        ]);

        // 3. Go back to inbox
        return redirect()->back()->with('status_updated', true);
    }
}
