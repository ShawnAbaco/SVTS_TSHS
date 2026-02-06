<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Adviser;
use App\Models\ViolationRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PAdviserController extends Controller
{
     // Updated notification data helper method for Prefect ONLY
private function getNotificationData()
{
    // Get recent violations (last 24 hours) that were referred FROM ADVISER TO PRECEF
    $referredViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
        ->where('handled_by', 'prefect')
        ->where('status', 'pending')
        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($violation) {
            $studentName = 'Unknown Student';
            $violationType = 'N/A';
            $adviserName = 'N/A';

            if ($violation->student) {
                $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;

                // Get adviser name if available
                if ($violation->student->adviser) {
                    $adviserName = $violation->student->adviser->adviser_fname . ' ' . $violation->student->adviser->adviser_lname;
                }
            }

            if ($violation->offense) {
                $violationType = $violation->offense->offense_type ?? 'N/A';
            }

            return [
                'violation_id' => $violation->violation_id,
                'student_name' => $studentName,
                'violation_type' => $violationType,
                'adviser_name' => $adviserName,
                'incident' => $violation->violation_incident,
                'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
                'time' => \Carbon\Carbon::parse($violation->violation_time)->format('h:i A'),
                'created_at' => $violation->created_at,
                'handled_by' => $violation->handled_by,
                'status' => $violation->status
            ];
        });

    $referredViolationsCount = $referredViolations->count();

    // For prefect, we also want to show new direct violations (created by prefect)
    $newDirectViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
        ->where('handled_by', 'prefect')
        ->where('status', 'in_progress') // Direct violations by prefect are usually in_progress
        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($violation) {
            $studentName = 'Unknown Student';
            $violationType = 'N/A';

            if ($violation->student) {
                $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;
            }

            if ($violation->offense) {
                $violationType = $violation->offense->offense_type ?? 'N/A';
            }

            return [
                'violation_id' => $violation->violation_id,
                'student_name' => $studentName,
                'violation_type' => $violationType,
                'incident' => $violation->violation_incident,
                'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
                'time' => \Carbon\Carbon::parse($violation->violation_time)->format('h:i A'),
                'created_at' => $violation->created_at,
                'handled_by' => $violation->handled_by,
                'status' => $violation->status
            ];
        });

    $newDirectViolationsCount = $newDirectViolations->count();

    // Calculate total notifications
    $totalViolationsCount = $referredViolationsCount + $newDirectViolationsCount;

    // For prefect, we don't care about new students or parents
    $newStudentsCount = 0;
    $newParentsCount = 0;
    $newComplaintsCount = 0;

    $notificationCount = $totalViolationsCount + $newStudentsCount + $newParentsCount + $newComplaintsCount;

    return [
        'notificationCount' => $notificationCount,
        'referredViolationsCount' => $referredViolationsCount,
        'newDirectViolationsCount' => $newDirectViolationsCount,
        'totalViolationsCount' => $totalViolationsCount,
        'newStudentsCount' => $newStudentsCount,
        'newParentsCount' => $newParentsCount,
        'newComplaintsCount' => $newComplaintsCount,
        'referredViolations' => $referredViolations,
        'newDirectViolations' => $newDirectViolations,
'newViolations' => collect($referredViolations)->merge($newDirectViolations),
        'newStudents' => collect([]), // Empty for prefect
        'newComplaints' => collect([]) // Empty for prefect
    ];
}


    public function index(Request $request)
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        $totalAdvisers = DB::table('tbl_adviser')
            ->where('status', 'active')
            ->count();

        $grade11Advisers = DB::table('tbl_adviser')
            ->where('adviser_gradelevel', '11')
            ->where('status', 'active')
            ->count();

        $grade12Advisers = DB::table('tbl_adviser')
            ->where('adviser_gradelevel', '12')
            ->where('status', 'active')
            ->count();

        // Changed from 20 to 4 per page
        $advisers = Adviser::where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->paginate(6) // Only 4 per page
            ->appends($request->query());

        // Get archived advisers for the archive modal
        $archivedAdvisers = Adviser::where('status', 'inactive')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('prefect.adviser', array_merge(
            compact('advisers', 'totalAdvisers', 'grade11Advisers', 'grade12Advisers', 'archivedAdvisers'),
            $notificationData
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'advisers' => 'required|array|min:1',
            'advisers.*.adviser_fname' => 'required|string|max:255',
            'advisers.*.adviser_lname' => 'required|string|max:255',
            'advisers.*.adviser_sex' => 'nullable|in:male,female,other',
            'advisers.*.adviser_email' => 'required|email|max:255|unique:tbl_adviser,adviser_email',
            'advisers.*.adviser_password' => 'required|string|min:6',
            'advisers.*.adviser_contactinfo' => 'required|string|max:255',
            'advisers.*.adviser_section' => 'required|string|max:255',
            'advisers.*.adviser_gradelevel' => 'required|string|max:50',
        ]);

        $messages = [];

        foreach ($request->advisers as $index => $adviserData) {
            try {
                Adviser::create([
                    'adviser_fname' => $adviserData['adviser_fname'],
                    'adviser_lname' => $adviserData['adviser_lname'],
                    'adviser_sex' => $adviserData['adviser_sex'] ?? null,
                    'adviser_email' => $adviserData['adviser_email'],
                    'adviser_password' => Hash::make($adviserData['adviser_password']),
                    'adviser_contactinfo' => $adviserData['adviser_contactinfo'],
                    'adviser_section' => $adviserData['adviser_section'],
                    'adviser_gradelevel' => $adviserData['adviser_gradelevel'],
                    'status' => 'active',
                ]);

                $messages[] = "✅ Adviser " . ($index + 1) . " (" . $adviserData['adviser_fname'] . " " . $adviserData['adviser_lname'] . ") created successfully.";
            } catch (\Exception $e) {
                $messages[] = "⚠️ Failed to create Adviser " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return redirect()->route('prefect.adviser')->with('messages', $messages);
    }

    public function createAdviser()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        return view('prefect.create-adviser', $notificationData);
    }

    public function update(Request $request)
{
    $adviser = Adviser::findOrFail($request->adviser_id);

    $adviser->update([
        'adviser_fname' => $request->adviser_fname,
        'adviser_lname' => $request->adviser_lname,
        'adviser_section' => $request->adviser_section,
        'adviser_gradelevel' => $request->adviser_gradelevel,
        'adviser_email' => $request->adviser_email,
        'adviser_contactinfo' => $request->adviser_contactinfo,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Adviser updated successfully!'
    ]);
}

   // Get archived advisers via AJAX
    public function getArchived()
    {
        try {
            $archivedAdvisers = Adviser::where('status', 'inactive')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json($archivedAdvisers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load archived advisers'], 500);
        }
    }

    // Move to trash with AJAX response
    public function moveToTrash(Request $request)
    {
        try {
            $request->validate([
                'adviser_ids' => 'required|array'
            ]);

            $adviserIds = $request->adviser_ids;

            Adviser::whereIn('adviser_id', $adviserIds)
                ->update([
                    'status' => 'inactive',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Advisers moved to archive successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move advisers to archive: ' . $e->getMessage()
            ], 500);
        }
    }

    // Restore advisers with AJAX response
    public function restore(Request $request)
    {
        try {
            $request->validate([
                'adviser_ids' => 'required|array'
            ]);

            $adviserIds = $request->adviser_ids;

            Adviser::whereIn('adviser_id', $adviserIds)
                ->update([
                    'status' => 'active',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Advisers restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore advisers: ' . $e->getMessage()
            ], 500);
        }
    }

    // Permanently delete advisers
    public function destroyMultiple(Request $request)
    {
        try {
            $request->validate([
                'adviser_ids' => 'required|array'
            ]);

            $adviserIds = $request->adviser_ids;

            Adviser::whereIn('adviser_id', $adviserIds)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Advisers deleted permanently'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete advisers: ' . $e->getMessage()
            ], 500);
        }
    }
}
