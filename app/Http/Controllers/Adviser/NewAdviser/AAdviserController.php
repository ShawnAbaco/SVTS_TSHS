<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

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

class AAdviserController extends Controller
{
    // UPDATED NOTIFICATION DATA HELPER METHOD WITH DETAILED INFORMATION
    private function getNotificationData()
    {
        // Get recent violations (last 24 hours) with details
        $newViolations = ViolationRecord::with(['student.adviser', 'offense'])
            ->where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subDays(1))
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
                    'student_name' => $studentName,
                    'violation_type' => $violationType,
                    'date' => Carbon::parse($violation->violation_date)->format('M d, Y'),
                    'created_at' => $violation->created_at
                ];
            });

        $newViolationsCount = $newViolations->count();

        // Get recent students (last 24 hours) with details
        $newStudents = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($student) {
                $gradeLevel = 'N/A';
                $section = 'N/A';

                if ($student->adviser) {
                    $gradeLevel = $student->adviser->adviser_gradelevel ?? 'N/A';
                    $section = $student->adviser->adviser_section ?? 'N/A';
                }

                return [
                    'name' => $student->student_fname . ' ' . $student->student_lname,
                    'grade_level' => $gradeLevel,
                    'section' => $section,
                    'created_at' => $student->created_at
                ];
            });

        $newStudentsCount = $newStudents->count();

        $newParentsCount = 0; // Add parent model when available

        $notificationCount = $newViolationsCount + $newStudentsCount + $newParentsCount;

        return [
            'notificationCount' => $notificationCount,
            'newViolationsCount' => $newViolationsCount,
            'newStudentsCount' => $newStudentsCount,
            'newParentsCount' => $newParentsCount,
            'newViolations' => $newViolations,
            'newStudents' => $newStudents
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

        return view('adviser.NewAdviser.adviser', array_merge(
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

        return redirect()->route('adviser.adviser')->with('messages', $messages);
    }

    public function createAdviser()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        return view('adviser.NewAdviser.create-adviser', $notificationData);
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
