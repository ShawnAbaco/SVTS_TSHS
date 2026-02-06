<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\ViolationRecord;
use Carbon\Carbon;

class PParentController extends Controller
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

    /**
     * Display parent list
     */
    public function parentlists()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        $totalParents = DB::table('tbl_parent')->count();

        // Get active parents
        $activeParents = DB::table('tbl_parent')
            ->where('status', 'active')
            ->count();

        // Get archived parents
        $archivedParentsCount = DB::table('tbl_parent')
            ->where('status', 'inactive')
            ->count();

        // Get active parents with their students and adviser information
        $parents = ParentModel::with(['students' => function($query) {
                $query->select('student_id', 'student_fname', 'student_lname', 'parent_id', 'adviser_id')
                      ->with(['adviser' => function($adviserQuery) {
                          $adviserQuery->select('adviser_id', 'adviser_fname', 'adviser_lname', 'adviser_gradelevel', 'adviser_section');
                      }]);
            }])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(6);

        // Get archived parents with their students and adviser information
        $archivedParents = ParentModel::with(['students' => function($query) {
                $query->select('student_id', 'student_fname', 'student_lname', 'parent_id', 'adviser_id')
                      ->with(['adviser' => function($adviserQuery) {
                          $adviserQuery->select('adviser_id', 'adviser_fname', 'adviser_lname', 'adviser_gradelevel', 'adviser_section');
                      }]);
            }])
            ->where('status', 'inactive')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('prefect.parentlists', array_merge(
            compact('parents', 'archivedParents','totalParents','activeParents','archivedParentsCount'),
            $notificationData
        ));
    }

    /**
     * Store new parent
     */
    public function parentStore(Request $request)
    {
        // Debug: Check what's being received
        \Log::info('Received data:', $request->all());

        // Validate the array of parents
        $request->validate([
            'parents' => 'required|array|min:1',
            'parents.*.parent_fname' => 'required|string|max:255',
            'parents.*.parent_lname' => 'required|string|max:255',
            'parents.*.parent_sex' => 'required|in:Male,Female,Other', // Match your form values
            'parents.*.parent_relationship' => 'required|string|max:255',
            'parents.*.parent_birthdate' => 'required|date',
            'parents.*.parent_contactinfo' => 'required|string|max:20',
            'parents.*.parent_email' => 'nullable|email|max:255',
        ]);

        try {
            $insertedCount = 0;
            $errors = [];

            foreach ($request->parents as $index => $parentData) {
                // Check if required fields are not empty
                if (empty($parentData['parent_fname']) || empty($parentData['parent_lname']) ||
                    empty($parentData['parent_birthdate']) || empty($parentData['parent_contactinfo'])) {
                    $errors[] = "Parent #" . ($index + 1) . " has missing required fields";
                    continue;
                }

                DB::table('tbl_parent')->insert([
                    'parent_fname' => $parentData['parent_fname'],
                    'parent_lname' => $parentData['parent_lname'],
                    'parent_sex' => $parentData['parent_sex'],
                    'parent_relationship' => $parentData['parent_relationship'],
                    'parent_birthdate' => $parentData['parent_birthdate'],
                    'parent_contactinfo' => $parentData['parent_contactinfo'],
                    'parent_email' => $parentData['parent_email'] ?? null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertedCount++;
            }

            if (!empty($errors)) {
                return back()->withInput()->with('error', implode(', ', $errors));
            }

            if ($insertedCount > 0) {
                return redirect()->route('parent.lists')->with('success', $insertedCount . ' parent(s) added successfully!');
            } else {
                return back()->withInput()->with('error', 'No parents were saved. Please check your data.');
            }

        } catch (\Exception $e) {
            \Log::error('Error saving parents: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error saving parents: ' . $e->getMessage());
        }
    }

    /**
     * Show create parent form
     */
    public function createParent()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        return view('prefect.create-parent', $notificationData);
    }

    public function update(Request $request)
{
    // Get ID from request body instead of URL
    $id = $request->input('parent_id');

    // Validate the request
    $request->validate([
        'parent_id' => 'required|exists:tbl_parent,parent_id', // Add parent_id validation
        'parent_fname' => 'required|string|max:255',
        'parent_lname' => 'required|string|max:255',
        'parent_birthdate' => 'required|date',
        'parent_contactinfo' => 'required|string|max:20',
        'parent_sex' => 'required|in:Male,Female',
        'parent_relationship' => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    try {
        $parent = DB::table('tbl_parent')->where('parent_id', $id)->first();

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Parent not found.'
            ], 404);
        }

        DB::table('tbl_parent')->where('parent_id', $id)->update([
            'parent_fname' => $request->parent_fname,
            'parent_lname' => $request->parent_lname,
            'parent_birthdate' => $request->parent_birthdate,
            'parent_contactinfo' => $request->parent_contactinfo,
            'parent_sex' => $request->parent_sex,
            'parent_relationship' => $request->parent_relationship,
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parent updated successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating parent: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error updating parent: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Move parents to archive (set status to inactive)
     */
    public function archiveParents(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        ParentModel::whereIn('parent_id', $request->parent_ids)
            ->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => count($request->parent_ids) . ' parent(s) moved to archive successfully!'
        ]);
    }

    /**
     * Get archived parents (status = inactive) - UPDATED WITH ADVISER INFORMATION
     */
    public function getArchivedParents()
    {
        $archivedParents = ParentModel::with(['students' => function($query) {
                $query->select('student_id', 'student_fname', 'student_lname', 'parent_id', 'adviser_id')
                      ->with(['adviser' => function($adviserQuery) {
                          $adviserQuery->select('adviser_id', 'adviser_fname', 'adviser_lname', 'adviser_gradelevel', 'adviser_section');
                      }]);
            }])
            ->where('status', 'inactive')
            ->get();

        return response()->json($archivedParents);
    }

    /**
     * Restore parents from archive
     */
    public function restoreParents(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        ParentModel::whereIn('parent_id', $request->parent_ids)
            ->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => count($request->parent_ids) . ' parent(s) restored successfully!'
        ]);
    }

    /**
     * Permanently delete parents
     */
    public function destroyParentsPermanent(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        ParentModel::whereIn('parent_id', $request->parent_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->parent_ids) . ' parent(s) permanently deleted!'
        ]);
    }

    /**
     * Single parent destroy (for individual deletion)
     */
    public function destroyParent($id)
    {
        try {
            $parent = DB::table('tbl_parent')->where('parent_id', $id)->first();

            if (!$parent) {
                return redirect()->back()->with('error', 'Parent not found.');
            }

            // Update status to inactive instead of deleting
            DB::table('tbl_parent')->where('parent_id', $id)->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);

            return redirect()->route('parents.list')->with('success', 'Parent moved to archive successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error archiving parent: ' . $e->getMessage());
        }
    }

    public function getArchivedParentsCount()
    {
        $count = ParentModel::where('status', 'inactive')->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Send SMS to parent
     */
    public function sendSms(Request $request)
    {
        $parentId = $request->parent_id;
        $message = $request->message;

        // Retrieve parent info
        $parent = DB::table('tbl_parent')->where('parent_id', $parentId)->first();

        if (!$parent) {
            return back()->with('error', 'Parent not found.');
        }

        // Here you would integrate your SMS API
        // Example: SmsService::send($parent->parent_contactinfo, $message);

        return back()->with('success', 'SMS sent to ' . $parent->parent_fname);
    }
}
