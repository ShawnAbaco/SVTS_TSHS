<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense; // Changed from OffensesWithSanction
use App\Models\Sanction; // Added Sanction model
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Services\PhilSMSService;

use App\Models\Complaints;

use Barryvdh\DomPDF\Facade\Pdf;


class PViolationController extends Controller
{
    //       // UPDATED NOTIFICATION DATA HELPER METHOD WITH DETAILED INFORMATION
    //     private function getNotificationData()
    // {
    //     // Get recent violations (last 24 hours) with details
    //     $newViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
    //         ->where('status', 'pending')
    //         ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function($violation) {
    //             $studentName = 'Unknown Student';
    //             $violationType = 'N/A';

    //             if ($violation->student) {
    //                 $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;
    //             }

    //             if ($violation->offense) {
    //                 $violationType = $violation->offense->offense_type ?? 'N/A';
    //             }

    //             return [
    //                 'student_name' => $studentName,
    //                 'violation_type' => $violationType,
    //                 'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
    //                 'created_at' => $violation->created_at
    //             ];
    //         });

    //     $newViolationsCount = $newViolations->count();

    //     // Get recent students (last 24 hours) with details
    //     $newStudents = Student::with(['adviser'])
    //         ->where('status', 'active')
    //         ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function($student) {
    //             $gradeLevel = 'N/A';
    //             $section = 'N/A';

    //             if ($student->adviser) {
    //                 $gradeLevel = $student->adviser->adviser_gradelevel ?? 'N/A';
    //                 $section = $student->adviser->adviser_section ?? 'N/A';
    //             }

    //             return [
    //                 'name' => $student->student_fname . ' ' . $student->student_lname,
    //                 'grade_level' => $gradeLevel,
    //                 'section' => $section,
    //                 'created_at' => $student->created_at
    //             ];
    //         });

    //     $newStudentsCount = $newStudents->count();

    //     // Get recent complaints (last 24 hours) with details
    //     $newComplaints = \App\Models\Complaints::with(['complainant.adviser', 'offense'])
    //         ->where('status', 'pending')
    //         ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->map(function($complaint) {
    //             $studentName = 'Unknown Student';
    //             $complaintType = 'N/A';

    //             if ($complaint->complainant) {
    //                 $studentName = $complaint->complainant->student_fname . ' ' . $complaint->complainant->student_lname;
    //             }

    //             if ($complaint->offense) {
    //                 $complaintType = $complaint->offense->offense_type ?? 'N/A';
    //             }

    //             return [
    //                 'student_name' => $studentName,
    //                 'complaint_type' => $complaintType,
    //                 'description' => $complaint->complaints_incident ?? 'N/A',
    //                 'created_at' => $complaint->created_at
    //             ];
    //         });

    //     $newComplaintsCount = $newComplaints->count();

    //     $newParentsCount = 0;

    //     $notificationCount = $newViolationsCount + $newStudentsCount + $newParentsCount + $newComplaintsCount;

    //     return [
    //         'notificationCount' => $notificationCount,
    //         'newViolationsCount' => $newViolationsCount,
    //         'newStudentsCount' => $newStudentsCount,
    //         'newParentsCount' => $newParentsCount,
    //         'newComplaintsCount' => $newComplaintsCount,
    //         'newViolations' => $newViolations,
    //         'newStudents' => $newStudents,
    //         'newComplaints' => $newComplaints
    //     ];
    // }
public function index(Request $request)
{
    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    $viewType = $request->get('view', 'individual');
    $groupKey = $request->get('group');

    // Initialize variables to avoid undefined errors
    $violations = collect();
    $byGroupViolations = collect();

    // Get appointments with proper sorting (newest first) - ONLY PRECEF HANDLED
    $vappointments = ViolationAppointment::with(['violation.student'])
        ->whereHas('violation', function($query) {
            $query->where('handled_by', 'prefect');
        })
        ->orderBy('created_at', 'desc')
        ->orderBy('updated_at', 'desc')
        ->get();

    // Get anecdotal records with proper sorting (newest first) - ONLY PRECEF HANDLED
    $vanecdotals = ViolationAnecdotal::with(['violation.student'])
        ->whereHas('violation', function($query) {
            $query->where('handled_by', 'prefect');
        })
        ->orderBy('created_at', 'desc')
        ->orderBy('updated_at', 'desc')
        ->get();

    // Get the actual dates from your violation records - ONLY PRECEF HANDLED
    $mostRecentViolationDate = DB::table('tbl_violation_record')
        ->where('handled_by', 'prefect')
        ->max('violation_date');
    $earliestViolationDate = DB::table('tbl_violation_record')
        ->where('handled_by', 'prefect')
        ->min('violation_date');

    // Use the most recent violation date for calculations, or today if no records exist
    $referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

    // Calculate date ranges based on the actual violation dates
    $today = $referenceDate->copy();
    $startOfWeek = $referenceDate->copy()->startOfWeek();
    $endOfWeek = $referenceDate->copy()->endOfWeek();
    $startOfMonth = $referenceDate->copy()->startOfMonth();
    $endOfMonth = $referenceDate->copy()->endOfMonth();

    // Summary Counts - Count ALL violations regardless of status
    $dailyViolations = DB::table('tbl_violation_record')
        ->whereDate('violation_date', $today)
        ->where('handled_by', 'prefect')
        ->count();

    $weeklyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
        ->where('handled_by', 'prefect')
        ->count();

    $monthlyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
        ->where('handled_by', 'prefect')
        ->count();

    // Handle different view types - ALL STATUSES INCLUDED
    if ($viewType == 'group') {
        // GROUPED VIOLATIONS LOGIC - Include ALL statuses
        $groupedViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
            ->where('handled_by', 'prefect')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function($violation) {
                // Group by incident, offense type, sanction, date, and time (within 1 hour range)
                $time = Carbon::parse($violation->violation_time);
                $timeGroup = $time->format('H');

                return $violation->violation_incident . '|' .
                       $violation->offense->offense_type . '|' .
                       $violation->sanction->sanction_consequences . '|' .
                       $violation->violation_date . '|' .
                       $timeGroup;
            });

        $byGroupViolations = $groupedViolations->map(function($group, $key) {
            // Get the most recent violation in the group
            $mostRecentViolation = $group->sortByDesc('created_at')->first();

            return (object)[
                'group_key' => $key,
                'violation_incident' => $mostRecentViolation->violation_incident,
                'offense_type' => $mostRecentViolation->offense->offense_type,
                'sanction_consequences' => $mostRecentViolation->sanction->sanction_consequences,
                'violation_date' => $mostRecentViolation->violation_date,
                'violation_time' => $mostRecentViolation->violation_time,
                'status' => $mostRecentViolation->status, // Include status
                'latest_created_at' => $mostRecentViolation->created_at,
                'latest_updated_at' => $mostRecentViolation->updated_at,
                'student_count' => $group->count(),
                'students' => $group->pluck('student'),
                'violation_ids' => $group->pluck('violation_id'), // All violation IDs in this group
                'violations_data' => $group->map(function($violation) {
                    return [
                        'violation_id' => $violation->violation_id,
                        'student_id' => $violation->student->student_id,
                        'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_sanc_id' => $violation->offense_sanc_id,
                        'sanction_id' => $violation->sanction_id
                    ];
                })->values() // Detailed violations data
            ];
        })
        ->sortByDesc('latest_created_at')
        ->sortByDesc('latest_updated_at')
        ->values();

    } elseif ($viewType == 'individual') {
        if ($groupKey) {
            // Viewing individual violations for a specific group - newest first - ALL STATUSES
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'prefect')
                ->whereIn('violation_id', function($query) use ($groupKey) {
                    $query->select('vr.violation_id')
                          ->from('tbl_violation_record as vr')
                          ->join('tbl_offense_sanction as off', 'vr.offense_sanc_id', '=', 'off.offense_sanc_id')
                          ->join('tbl_sanction as s', 'off.sanction_id', '=', 's.sanction_id')
                          ->where('vr.handled_by', 'prefect')
                          ->whereRaw("CONCAT(vr.violation_incident, '|', off.offense_type, '|', s.sanction_consequences, '|', vr.violation_date, '|', HOUR(vr.violation_time)) = ?", [$groupKey]);
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->paginate(20);

            // Add violation_ids to each violation for easy access
            $violations->getCollection()->transform(function($violation) {
                $violation->all_violation_ids = [$violation->violation_id]; // Single violation in this case
                return $violation;
            });

        } else {
            // SIMPLE MERGING: Group by student, date, and time, then manually paginate - ALL STATUSES
            $allViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'prefect')
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->get();

            $groupedViolations = $allViolations->groupBy(function($violation) {
                return $violation->violator_id . '|' . $violation->violation_date . '|' . $violation->violation_time;
            });

            $mergedViolations = $groupedViolations->map(function($group) {
                if ($group->count() == 1) {
                    $violation = $group->first();
                    $violation->all_violation_ids = [$violation->violation_id];
                    return $violation;
                }

                // For multiple violations, use the first one but add merged info
                $first = $group->first();
                $first->merged_count = $group->count();
                $first->merged_violation_ids = $group->pluck('violation_id');
                $first->merged_offense_types = $group->pluck('offense.offense_type')->unique()->implode(', ');
                $first->merged_sanctions = $group->pluck('sanction.sanction_consequences')->unique()->implode(', ');
                $first->all_violation_ids = $group->pluck('violation_id'); // All violation IDs
                return $first;
            })->values();

            // Manual pagination
            $page = $request->get('page', 1);
            $perPage = 20;
            $paginatedViolations = new \Illuminate\Pagination\LengthAwarePaginator(
                $mergedViolations->forPage($page, $perPage),
                $mergedViolations->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $violations = $paginatedViolations;
        }
    }
    public function settle(Request $request, Violation $violation)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,resolved,dismissed,closed,settled',
        'settlement_date' => 'required|date',
        'settlement_time' => 'required',
        'settlement_notes' => 'nullable|string'
    ]);

    // Prevent settling already settled violations
    if ($violation->status === 'settled' && $violation->settlement_date) {
        return response()->json([
            'success' => false,
            'message' => 'This violation is already settled and cannot be modified.'
        ], 400);
    }

    // Prevent setting status to "pending" when already "pending"
    if ($violation->status === 'pending' && $request->status === 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Cannot set status to "pending" when it is already "pending".'
        ], 400);
    }

    $violation->update([
        'status' => $request->status,
        'settlement_date' => $request->settlement_date,
        'settlement_time' => $request->settlement_time,
        'settlement_notes' => $request->settlement_notes,
        'settled_at' => now(), // Add this field to your migrations if needed
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Violation settled successfully!'
    ]);
}

    // Fetch Violation Appointments - ONLY PRECEF HANDLED
    $appointments = DB::table('tbl_violation_appointment')
        ->join('tbl_violation_record', 'tbl_violation_appointment.violation_id', '=', 'tbl_violation_record.violation_id')
        ->select(
            'tbl_violation_appointment.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_appointment.violation_app_status', 'Scheduled')
        ->where('tbl_violation_record.handled_by', 'prefect')
        ->orderBy('tbl_violation_appointment.created_at', 'desc')
        ->orderBy('tbl_violation_appointment.updated_at', 'desc')
        ->orderBy('tbl_violation_appointment.violation_app_date', 'desc')
        ->paginate(20);

    // Fetch Violation Anecdotals - ONLY PRECEF HANDLED
    $anecdotals = DB::table('tbl_violation_anecdotal')
        ->join('tbl_violation_record', 'tbl_violation_anecdotal.violation_id', '=', 'tbl_violation_record.violation_id')
        ->select(
            'tbl_violation_anecdotal.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_anecdotal.status', 'completed')
        ->where('tbl_violation_record.handled_by', 'prefect')
        ->orderBy('tbl_violation_anecdotal.created_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.updated_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.violation_anec_date', 'desc')
        ->paginate(20);

    // Fetch Offenses and Sanctions
    $offenses = Offense::all();
    $sanctions = Sanction::all();

    // Prepare only the data that's actually used in the Blade view
    $data = compact(
        'violations',
        'byGroupViolations',
        'offenses',
        'sanctions',
        'dailyViolations',
        'weeklyViolations',
        'monthlyViolations',
        'viewType',
        'groupKey'
    );

    // Return to Blade with merged notification data
    return view('prefect.violation', array_merge($data, $notificationData));
}


    public function indexAnecdotal()
{
    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    $vappointments = ViolationAppointment::with(['violation.student'])
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    $vanecdotals = ViolationAnecdotal::with(['violation.student'])
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    // Get the actual dates from your violation records
    $mostRecentViolationDate = DB::table('tbl_violation_record')->max('violation_date');
    $earliestViolationDate = DB::table('tbl_violation_record')->min('violation_date');

    // Use the most recent violation date for calculations, or today if no records exist
    $referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

    // Calculate date ranges based on the actual violation dates
    $today = $referenceDate->copy();
    $startOfWeek = $referenceDate->copy()->startOfWeek();
    $endOfWeek = $referenceDate->copy()->endOfWeek();
    $startOfMonth = $referenceDate->copy()->startOfMonth();
    $endOfMonth = $referenceDate->copy()->endOfMonth();

    // Summary Counts - Updated to use 'pending' status for active violations
    $dailyViolations = DB::table('tbl_violation_record')
        ->whereDate('violation_date', $today)
        ->where('status', 'pending')
        ->count();

    $weeklyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
        ->where('status', 'pending')
        ->count();

    $monthlyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
        ->where('status', 'pending')
        ->count();

    // Fetch Main Violation Records
    $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
        ->where('status', 'pending')
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->orderBy('violation_date', 'desc')
        ->paginate(20);

    // Fetch Violation Appointments
    $appointments = DB::table('tbl_violation_appointment')
        ->join('tbl_violation_record', 'tbl_violation_appointment.violation_id', '=', 'tbl_violation_record.violation_id')
        ->select(
            'tbl_violation_appointment.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_appointment.violation_app_status', 'Scheduled')
        ->orderBy('tbl_violation_appointment.updated_at', 'desc')
        ->orderBy('tbl_violation_appointment.created_at', 'desc')
        ->orderBy('tbl_violation_appointment.violation_app_date', 'desc')
        ->paginate(20);

    // Fetch Violation Anecdotals
    $anecdotals = DB::table('tbl_violation_anecdotal')
        ->join('tbl_violation_record', 'tbl_violation_anecdotal.violation_id', '=', 'tbl_violation_record.violation_id')
        ->select(
            'tbl_violation_anecdotal.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_anecdotal.status', 'completed')
        ->orderBy('tbl_violation_anecdotal.updated_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.created_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.violation_anec_date', 'desc')
        ->paginate(20);

    // Fetch Offenses and Sanctions
    $offenses = Offense::all();
    $sanctions = Sanction::all();

    // Prepare all data
    $data = compact(
        'violations',
        'appointments',
        'anecdotals',
        'vanecdotals',
        'vappointments',
        'offenses',
        'sanctions',
        'mostRecentViolationDate',
        'earliestViolationDate',
        'referenceDate',
        'today',
        'startOfWeek',
        'endOfWeek',
        'startOfMonth',
        'endOfMonth',
        'dailyViolations',
        'weeklyViolations',
        'monthlyViolations'
    );

    // Return to Blade with merged notification data
    return view('prefect.violationAnecdotal', array_merge($data, $notificationData));
}
public function indexAppointment()
{
    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    // Only keep what's used in the Blade template
    $vappointments = ViolationAppointment::with(['violation.student'])
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $offenses = Offense::all();

    // Prepare data
    $data = compact(
        'vappointments',
        'offenses'
    );

    // Return to Blade with merged notification data
    return view('prefect.violationAppointment', array_merge($data, $notificationData));
}


    public function store(Request $request)
{
    \Log::info('=== VIOLATION STORE START ===');
    \Log::info('Request method: ' . $request->method());
    \Log::info('Request headers: ', $request->headers->all());
    \Log::info('Request data count - violations: ' . count($request->input('violations', [])));
    \Log::info('Request data count - offenses: ' . count($request->input('offenses', [])));
    \Log::info('Has files: ' . ($request->hasFile('evidence_files') ? 'Yes' : 'No'));

    try {
        DB::beginTransaction();

        $prefect_id = Auth::id() ?? 1;
        $violationsData = $request->input('violations', []);
        $offensesData = $request->input('offenses', []);
        $savedCount = 0;
        $messages = [];

        \Log::info('Violations submission details', [
            'count' => count($violationsData),
            'offenses_count' => count($offensesData),
            'prefect_id' => $prefect_id,
            'data_sample' => array_slice($violationsData, 0, 2) // Log first 2 for debugging
        ]);

        // Early validation
        if (!Auth::check()) {
            throw new \Exception('User not authenticated');
        }

        if (empty($violationsData)) {
            throw new \Exception('No violation data found');
        }

        if (empty($offensesData)) {
            throw new \Exception('No offense data found');
        }

        // Validate required fields for each violation
        foreach ($violationsData as $index => $violation) {
            if (empty($violation['violator_id']) || empty($violation['date']) || empty($violation['time']) || empty($violation['incident'])) {
                throw new \Exception("Violation {$index} is missing required fields (violator_id, date, time, or incident)");
            }
        }

        // Validate required fields for each offense
        foreach ($offensesData as $index => $offense) {
            if (empty($offense['offense_id'])) {
                throw new \Exception("Offense {$index} is missing offense_id");
            }
        }

        // Array to track violations that need SMS notification
        $violationsForSMS = [];

        // Check if this is a group submission (multiple violators in one violation)
        $isGroupSubmission = $this->isGroupSubmission($violationsData);

        \Log::info('Submission type check', [
            'is_group_submission' => $isGroupSubmission,
            'violations_count' => count($violationsData)
        ]);

        foreach ($violationsData as $violationIndex => $violation) {
            // Extract all fields, including new optional fields
            extract(array_merge([
                'violator_id' => null,
                'violator_ids' => [], // For group submissions
                'date' => null,
                'time' => null,
                'incident' => null,
                'witnesses' => null,
                'evidence_description' => null,
                'sanction_id' => null,
                'custom_sanctions' => null,
            ], $violation));

            // Handle group submissions (multiple violators)
            $violatorIds = [];
            if ($isGroupSubmission && !empty($violator_ids) && is_array($violator_ids)) {
                $violatorIds = $violator_ids;
                \Log::info("Group submission with violators", ['violator_ids' => $violatorIds]);
            } else if (!empty($violator_id)) {
                $violatorIds = [$violator_id];
                \Log::info("Individual submission with violator", ['violator_id' => $violator_id]);
            }

            // Skip if no violators found
            if (empty($violatorIds)) {
                \Log::warning("Skipping violation {$violationIndex} - no violators found", $violation);
                continue;
            }

            // Skip if any required field is empty
            if (empty($date) || empty($time) || empty($incident)) {
                \Log::warning("Skipping violation {$violationIndex} - missing required fields", $violation);
                continue;
            }

            // Handle evidence file uploads
            $evidenceFilesJson = null;
            if ($request->hasFile('evidence_files')) {
                $storedFiles = [];
                foreach ($request->file('evidence_files') as $file) {
                    if ($file->isValid()) {
                        // Validate file type (images and videos)
                        $mimeType = $file->getMimeType();
                        $allowedTypes = [
                            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                            'video/mp4', 'video/mov', 'video/avi', 'video/x-msvideo',
                            'video/x-matroska', 'video/webm'
                        ];

                        if (!in_array($mimeType, $allowedTypes)) {
                            continue; // Skip invalid file types
                        }

                        // Generate unique filename
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '.' . $extension;

                        // Store in appropriate folder based on type
                        if (str_starts_with($mimeType, 'image/')) {
                            $file->storeAs('public/evidence/images', $filename);
                            $storedFiles[] = [
                                'path' => 'storage/evidence/images/' . $filename,
                                'type' => 'image',
                                'original_name' => $file->getClientOriginalName()
                            ];
                        } else {
                            $file->storeAs('public/evidence/videos', $filename);
                            $storedFiles[] = [
                                'path' => 'storage/evidence/videos/' . $filename,
                                'type' => 'video',
                                'original_name' => $file->getClientOriginalName()
                            ];
                        }
                    }
                }
                $evidenceFilesJson = $storedFiles ? json_encode($storedFiles) : null;
            }

            // Parse custom sanctions if any
            $customSanctionsData = [];
            if (!empty($custom_sanctions)) {
                $customSanctionsData = json_decode($custom_sanctions, true);
                \Log::info("Custom sanctions data for violation {$violationIndex}:", $customSanctionsData);
            }

            // Process each violator (for both individual and group submissions)
            foreach ($violatorIds as $currentViolatorId) {
                // Validate violator exists
                $violatorExists = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                if (!$violatorExists) {
                    \Log::warning("Invalid violator ID for violation {$violationIndex}", ['violator_id' => $currentViolatorId]);
                    continue;
                }

                // Get violator name for logging
                $violator = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                $violatorName = $violator ? "{$violator->student_fname} {$violator->student_lname}" : 'Unknown';

                \Log::info("Processing violation for violator", [
                    'violator_id' => $currentViolatorId,
                    'violator_name' => $violatorName,
                    'offenses_count' => count($offensesData),
                    'incident' => $incident
                ]);

                // Create ONE violation record per offense-sanction pair per violator
                foreach ($offensesData as $offenseIndex => $offense) {
                    $offenseId = $offense['offense_id'] ?? null;
                    $generalSanctionId = $offense['sanction_id'] ?? null;

                    if (empty($offenseId)) {
                        \Log::warning("Skipping offense {$offenseIndex} - missing offense ID");
                        continue;
                    }

                    // Validate offense exists
                    $offenseExists = DB::table('tbl_offense')
                        ->where('offense_id', $offenseId)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (!$offenseExists) {
                        \Log::warning("Invalid offense ID {$offenseId} for violation {$violationIndex}");
                        continue;
                    }

                    // Determine the final sanction ID for this specific offense
                    $finalSanctionId = $generalSanctionId;

                    // Check if this violator has a custom sanction for this specific offense
                    if (isset($customSanctionsData['customSanctions'][$offenseIndex])) {
                        $customSanction = $customSanctionsData['customSanctions'][$offenseIndex];
                        $finalSanctionId = $customSanction['sanctionId'] ?? $generalSanctionId;
                        \Log::info("Using custom sanction for offense {$offenseIndex}", [
                            'custom_sanction_id' => $finalSanctionId,
                            'general_sanction_id' => $generalSanctionId
                        ]);
                    }

                    // Handle "not_assigned" sanction
                    if ($finalSanctionId === 'not_assigned' || empty($finalSanctionId)) {
                        $finalSanctionId = DB::table('tbl_sanction')
                            ->where('sanction_consequences', 'NOT ASSIGNED')
                            ->value('sanction_id')
                            ?? DB::table('tbl_sanction')->insertGetId([
                                'sanction_consequences' => 'NOT ASSIGNED',
                                'sanction_description' => 'Default sanction for violations that have not been assigned a specific consequence yet.',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                    }

                    // Validate sanction exists
                    $sanctionExists = DB::table('tbl_sanction')->where('sanction_id', $finalSanctionId)->exists();
                    if (!$sanctionExists) {
                        \Log::warning("Invalid sanction ID {$finalSanctionId} for offense {$offenseIndex}");
                        continue;
                    }

                    // Create violation record for this offense-sanction pair
                    try {
                        $newViolation = DB::table('tbl_violation_record')->insertGetId([
                            'violator_id' => $currentViolatorId,
                            'prefect_id' => $prefect_id,
                            'offense_id' => $offenseId,
                            'sanction_id' => $finalSanctionId,
                            'violation_incident' => $incident,
                            'violation_date' => $date,
                            'violation_time' => $time,
                            'status' => 'on progress',
                            'handled_by' => 'prefect',
                            'witnesses' => $witnesses,
                            'evidence_description' => $evidence_description,
                            'evidence_files' => $evidenceFilesJson,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $savedCount++;

                        // Get offense and sanction names for message
                        $offenseRecord = DB::table('tbl_offense')->where('offense_id', $offenseId)->first();
                        $sanctionRecord = DB::table('tbl_sanction')->where('sanction_id', $finalSanctionId)->first();

                        $offenseName = $offenseRecord ? $offenseRecord->offense_type : 'Unknown Offense';
                        $sanctionName = $sanctionRecord ? $sanctionRecord->sanction_consequences : 'Unknown Sanction';

                        $messages[] = "✅ {$violatorName} - {$offenseName} ({$sanctionName})";

                        // Store violation info for SMS notification - WILL BE GROUPED BY INCIDENT LATER
                        $violationsForSMS[] = [
                            'violation_id' => $newViolation,
                            'violator_id' => $currentViolatorId,
                            'violator_name' => $violatorName,
                            'offense_name' => $offenseName,
                            'sanction_name' => $sanctionName,
                            'date' => $date,
                            'time' => $time,
                            'incident' => $incident
                        ];

                        \Log::info("Violation created and queued for SMS", [
                            'id' => $newViolation,
                            'violator' => $violatorName,
                            'offense' => $offenseName,
                            'sanction' => $sanctionName,
                            'is_group' => $isGroupSubmission,
                            'sms_queue_count' => count($violationsForSMS)
                        ]);

                    } catch (\Exception $e) {
                        \Log::error("Failed to create violation {$violationIndex} for offense {$offenseIndex}: {$e->getMessage()}");
                        \Log::error("Stack trace: {$e->getTraceAsString()}");
                    }
                }

                // Handle additional offenses from custom sanctions for this violator
                if (isset($customSanctionsData['offenses']) && is_array($customSanctionsData['offenses'])) {
                    foreach ($customSanctionsData['offenses'] as $additionalOffense) {
                        $additionalOffenseId = $additionalOffense['offense_id'] ?? null;
                        $additionalSanctionId = $additionalOffense['sanction_id'] ?? null;

                        if (empty($additionalOffenseId) || empty($additionalSanctionId)) {
                            \Log::warning("Skipping additional offense - missing offense_id or sanction_id", $additionalOffense);
                            continue;
                        }

                        // Validate additional offense exists
                        $additionalOffenseExists = DB::table('tbl_offense')
                            ->where('offense_id', $additionalOffenseId)
                            ->whereNull('deleted_at')
                            ->exists();

                        if (!$additionalOffenseExists) {
                            \Log::warning("Invalid additional offense ID {$additionalOffenseId}");
                            continue;
                        }

                        // Validate additional sanction exists
                        $additionalSanctionExists = DB::table('tbl_sanction')->where('sanction_id', $additionalSanctionId)->exists();
                        if (!$additionalSanctionExists) {
                            \Log::warning("Invalid additional sanction ID {$additionalSanctionId}");
                            continue;
                        }

                        // Create separate violation record for additional offense
                        try {
                            $newAdditionalViolation = DB::table('tbl_violation_record')->insertGetId([
                                'violator_id' => $currentViolatorId,
                                'prefect_id' => $prefect_id,
                                'offense_id' => $additionalOffenseId,
                                'sanction_id' => $additionalSanctionId,
                                'violation_incident' => $incident,
                                'violation_date' => $date,
                                'violation_time' => $time,
                                'status' => 'on progress',
                                'handled_by' => 'prefect',
                                'witnesses' => $witnesses,
                                'evidence_description' => $evidence_description,
                                'evidence_files' => $evidenceFilesJson,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            $savedCount++;

                            // Get names for message
                            $offenseRecord = DB::table('tbl_offense')->where('offense_id', $additionalOffenseId)->first();
                            $sanctionRecord = DB::table('tbl_sanction')->where('sanction_id', $additionalSanctionId)->first();

                            $offenseName = $offenseRecord ? $offenseRecord->offense_type : 'Unknown Offense';
                            $sanctionName = $sanctionRecord ? $sanctionRecord->sanction_consequences : 'Unknown Sanction';

                            $messages[] = "✅ {$violatorName} - {$offenseName} ({$sanctionName}) [Additional]";

                            // Store additional violation info for SMS - SAME INCIDENT
                            $violationsForSMS[] = [
                                'violation_id' => $newAdditionalViolation,
                                'violator_id' => $currentViolatorId,
                                'violator_name' => $violatorName,
                                'offense_name' => $offenseName,
                                'sanction_name' => $sanctionName,
                                'date' => $date,
                                'time' => $time,
                                'incident' => $incident
                            ];

                            \Log::info("Additional violation created and queued for SMS", [
                                'id' => $newAdditionalViolation,
                                'offense' => $offenseName,
                                'sanction' => $sanctionName,
                                'sms_queue_count' => count($violationsForSMS)
                            ]);

                        } catch (\Exception $e) {
                            \Log::error("Failed to create additional violation {$violationIndex}: {$e->getMessage()}");
                            \Log::error("Stack trace: {$e->getTraceAsString()}");
                        }
                    }
                }
            }
        }

        DB::commit();

        \Log::info("Violations saved - Final SMS check", [
            'saved_count' => $savedCount,
            'is_group_submission' => $isGroupSubmission,
            'violations_for_sms_count' => count($violationsForSMS),
            'unique_violators' => count(array_unique(array_column($violationsForSMS, 'violator_id'))),
            'unique_incidents' => count(array_unique(array_column($violationsForSMS, 'incident')))
        ]);

        // Send SMS notifications after successful commit - WILL GROUP BY INCIDENT
        if ($savedCount > 0 && !empty($violationsForSMS)) {
            \Log::info("Sending GROUPED SMS notifications", [
                'total_violations' => count($violationsForSMS),
                'unique_violators' => count(array_unique(array_column($violationsForSMS, 'violator_id'))),
                'unique_incidents' => count(array_unique(array_column($violationsForSMS, 'incident')))
            ]);
            $this->sendViolationNotifications($violationsForSMS);
        } else {
            \Log::warning("No SMS notifications sent", [
                'saved_count' => $savedCount,
                'violations_for_sms_count' => count($violationsForSMS)
            ]);
        }

        if ($savedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No violations were saved. Please check all fields are properly filled.'
            ], 400);
        }

        $successMessage = "Successfully saved $savedCount violation record(s)!" .
                          ($messages ? "<br><br>" . implode('<br>', array_slice($messages, 0, 10)) : '');

        if (count($messages) > 10) {
            $successMessage .= "<br>... and " . (count($messages) - 10) . " more";
        }

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'saved_count' => $savedCount
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Violations submission failed: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

  /**
 * Send SMS to parent of violator - GROUPED VERSION (Plain Text for SMS)
 */
private function sendSMSToParent($violatorId, $incidentData)
{
    try {
        Log::info("DEBUG: Preparing SMS for violator: {$violatorId}", [
            'incident' => $incidentData['incident'],
            'offenses_count' => count($incidentData['offenses'])
        ]);

        // Get student with parent information
        $student = DB::table('tbl_student as s')
            ->join('tbl_parent as p', 's.parent_id', '=', 'p.parent_id')
            ->where('s.student_id', $violatorId)
            ->select(
                's.student_fname',
                's.student_lname',
                'p.parent_fname',
                'p.parent_lname',
                'p.parent_contactinfo'
            )
            ->first();

        if (!$student) {
            Log::warning("Student not found for SMS notification: {$violatorId}");
            return;
        }

        // DEBUG: Check if this is YOUR number
        $isMyNumber = in_array($student->parent_contactinfo, ['09513738659', '639513738659']);
        Log::info("DEBUG PHONE CHECK:", [
            'student_name' => $student->student_fname . ' ' . $student->student_lname,
            'parent_name' => $student->parent_fname . ' ' . $student->parent_lname,
            'parent_contact' => $student->parent_contactinfo,
            'is_my_number' => $isMyNumber
        ]);

        $parentPhone = $student->parent_contactinfo ?? null;

        if (!$parentPhone) {
            Log::warning("No parent contact found for student: {$student->student_fname} {$student->student_lname}");
            return;
        }

        Log::info("DEBUG: Parent phone found: {$parentPhone}");

        $studentName = $student->student_fname . ' ' . $student->student_lname;
        $parentName = $student->parent_fname . ' ' . $student->parent_lname;

        // Count offense repetitions and gather detailed information
        $offenseCounts = [];
        $sanctionsList = [];

        foreach ($incidentData['offenses'] as $offense) {
            $offenseName = $offense['offense_name'];
            $sanctionName = $offense['sanction_name'];

            // Count repetitions of each offense type
            if (!isset($offenseCounts[$offenseName])) {
                $offenseCounts[$offenseName] = 0;
                $sanctionsList[$offenseName] = $sanctionName;
            }
            $offenseCounts[$offenseName]++;
        }

// Build concise but formal message - ONE SMS PER INCIDENT - PLAIN TEXT
$message = "Dear Parent/Guardian {$parentName},\n\n";
$message .= "Your child {$studentName} has a school violation.\n\n";

$message .= "Incident: {$incidentData['incident']}\n";
$message .= "Date: " . date('M j, Y', strtotime($incidentData['date'])) . "\n";
$message .= "Time: " . date('g:i A', strtotime($incidentData['time'])) . "\n\n";

$message .= "Violations:\n";
foreach ($offenseCounts as $offenseName => $count) {
    $sanction = $sanctionsList[$offenseName] ?? 'Pending';
    $message .= "- {$offenseName}";
    if ($count > 1) {
        $message .= " ({$count}x)";
    }
    $message .= " - {$sanction}\n";
}

$message .= "\nPlease contact the Tagoloan Senior High School for more details.";

        // Limit message length (SMS concatenation handles longer messages)
        if (strlen($message) > 480) {
            $message = substr($message, 0, 477) . '...';
        }

        Log::info("DEBUG: Sending plain text SMS message", [
            'message_length' => strlen($message),
            'offense_counts' => $offenseCounts,
            'total_offenses' => count($incidentData['offenses']),
            'incident' => substr($incidentData['incident'], 0, 50) . '...'
        ]);

        // Send SMS
        $smsResult = $this->smsService->sendSMS($parentPhone, $message);

        if ($smsResult['success']) {
            Log::info("✅ PLAIN TEXT SMS sent to parent of {$studentName}", [
                'parent_name' => $parentName,
                'phone' => $parentPhone,
                'total_offenses' => count($incidentData['offenses']),
                'offense_types' => array_keys($offenseCounts),
                'incident' => $incidentData['incident']
            ]);
        } else {
            Log::error("❌ Failed to send plain text SMS to parent of {$studentName}", [
                'parent_name' => $parentName,
                'phone' => $parentPhone,
                'error' => $smsResult['error'] ?? 'Unknown error'
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error in sendSMSToParent: ' . $e->getMessage());
    }
}
    protected $smsService;

    public function __construct(PhilSMSService $smsService)
    {
        $this->smsService = $smsService;
    }


/**
 * Check if the submission is a group submission (multiple violators)
 */
private function isGroupSubmission($violationsData)
{
    foreach ($violationsData as $violation) {
        if (isset($violation['violator_ids']) && is_array($violation['violator_ids']) && count($violation['violator_ids']) > 1) {
            return true;
        }
    }
    return false;
}

/**
 * Send SMS notifications to parents of violators - GROUPED BY INCIDENT
 */
private function sendViolationNotifications($violations)
{
    try {
        Log::info("Starting SMS notifications", ['total_violations' => count($violations)]);

        // Group violations by violator AND incident to avoid duplicate SMS
        $violatorIncidents = [];

        foreach ($violations as $violation) {
            $violatorId = $violation['violator_id'];
            $incidentKey = $violation['incident'] . '|' . $violation['date'] . '|' . $violation['time'];

            if (!isset($violatorIncidents[$violatorId])) {
                $violatorIncidents[$violatorId] = [];
            }

            if (!isset($violatorIncidents[$violatorId][$incidentKey])) {
                $violatorIncidents[$violatorId][$incidentKey] = [
                    'violator_name' => $violation['violator_name'],
                    'incident' => $violation['incident'],
                    'date' => $violation['date'],
                    'time' => $violation['time'],
                    'offenses' => []
                ];
            }

            // Add offense to this incident
            $violatorIncidents[$violatorId][$incidentKey]['offenses'][] = [
                'offense_name' => $violation['offense_name'],
                'sanction_name' => $violation['sanction_name']
            ];
        }

        Log::info("Grouped violations for SMS", [
            'unique_violators' => count($violatorIncidents),
            'total_incidents' => array_sum(array_map('count', $violatorIncidents))
        ]);

        // Send ONE SMS per violator per incident
        $smsSentCount = 0;
        foreach ($violatorIncidents as $violatorId => $incidents) {
            foreach ($incidents as $incidentKey => $incidentData) {
                $this->sendSMSToParent($violatorId, $incidentData);
                $smsSentCount++;

                Log::info("SMS queued for incident", [
                    'violator_id' => $violatorId,
                    'violator_name' => $incidentData['violator_name'],
                    'incident' => $incidentData['incident'],
                    'offenses_count' => count($incidentData['offenses'])
                ]);
            }
        }

        Log::info("SMS notifications completed", ['total_sms_sent' => $smsSentCount]);

    } catch (\Exception $e) {
        Log::error('SMS notification failed: ' . $e->getMessage());
    }
}

public function storeMultipleAppointments(Request $request)
{
    \Log::info('=== storeMultipleAppointments START ===');
    \Log::info('Request data:', $request->all());

    // Validate the request
    $validator = Validator::make($request->all(), [
        'violation_ids' => 'sometimes|array',
        'violation_ids.*' => 'exists:tbl_violation_record,violation_id',
        'group_keys' => 'sometimes|array',
        'group_keys.*' => 'string',
        'schedule_date' => 'required|date|after_or_equal:today',
        'schedule_time' => 'required|date_format:H:i',
        'violation_app_notes' => 'nullable|string|max:500'
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $createdAppointments = [];
        $violationIds = $request->violation_ids ?? [];
        $groupKeys = $request->group_keys ?? [];
        $appointmentsForSMS = [];

        // Track processed violation IDs to prevent duplicates
        $processedViolationIds = [];

        \Log::info('Initial - Violation IDs:', $violationIds);
        \Log::info('Initial - Group Keys:', $groupKeys);

        // Check if at least one violation or group is selected
        if (empty($violationIds) && empty($groupKeys)) {
            \Log::warning('No violations or groups selected');
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one violation or group'
            ], 422);
        }

        // Process individual violations first
        if (!empty($violationIds)) {
            \Log::info('=== PROCESSING INDIVIDUAL VIOLATIONS ===');
            foreach ($violationIds as $violationId) {
                // Skip if already processed
                if (in_array($violationId, $processedViolationIds)) {
                    \Log::info("❌ SKIPPING - Already processed individual violation: $violationId");
                    continue;
                }

                // Check if appointment already exists for this violation
                $existingAppointment = ViolationAppointment::where('violation_id', $violationId)
                    ->where('violation_app_status', 'Scheduled')
                    ->first();

                if ($existingAppointment) {
                    \Log::warning("❌ SKIPPING - Appointment already exists for violation: $violationId");
                    $processedViolationIds[] = $violationId;
                    continue;
                }

                $violation = ViolationRecord::with(['student', 'offense'])
                    ->where('violation_id', $violationId)
                    ->first();

                if ($violation) {
                    $appointment = ViolationAppointment::create([
                        'violation_id' => $violationId,
                        'violation_app_date' => $request->schedule_date,
                        'violation_app_time' => $request->schedule_time,
                        'violation_app_notes' => $request->violation_app_notes,
                        'violation_app_status' => 'Scheduled',
                        'handled_by' => 'prefect',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $createdAppointments[] = $appointment;
                    $processedViolationIds[] = $violationId;

                    // Store appointment info for SMS notification
                    $appointmentsForSMS[] = [
                        'appointment_id' => $appointment->violation_app_id,
                        'violation_id' => $violationId,
                        'violator_id' => $violation->violator_id,
                        'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                        'violation_status' => $violation->status,
                        'appointment_date' => $request->schedule_date,
                        'appointment_time' => $request->schedule_time,
                        'notes' => $request->violation_app_notes
                    ];

                    \Log::info("✅ CREATED - Appointment for individual violation: $violationId", [
                        'appointment_id' => $appointment->violation_app_id,
                        'violation_status' => $violation->status
                    ]);
                } else {
                    \Log::warning("❌ VIOLATION NOT FOUND: $violationId");
                }
            }
        }

        // Process group violations - but skip violations already processed from individual selection
        if (!empty($groupKeys)) {
            \Log::info('=== PROCESSING GROUP VIOLATIONS ===');
            foreach ($groupKeys as $groupKey) {
                \Log::info("Processing group key: $groupKey");

                // Parse the group key to match your grouping logic
                $groupParts = explode('|', $groupKey);
                \Log::info("Group parts:", $groupParts);

                if (count($groupParts) >= 5) {
                    $incident = $groupParts[0];
                    $offenseType = $groupParts[1];
                    $sanction = $groupParts[2];
                    $date = $groupParts[3];
                    $timeGroup = $groupParts[4];

                    \Log::info("Searching for group violations with:", [
                        'incident' => $incident,
                        'offense_type' => $offenseType,
                        'sanction' => $sanction,
                        'date' => $date,
                        'time_group' => $timeGroup
                    ]);

                    // Find violations that match this exact group
                    $groupViolations = ViolationRecord::with(['student', 'offense'])
                        ->where('violation_incident', $incident)
                        ->where('violation_date', $date)
                        ->whereHas('offense', function($query) use ($offenseType) {
                            $query->where('offense_type', $offenseType);
                        })
                        ->whereHas('sanction', function($query) use ($sanction) {
                            $query->where('sanction_consequences', $sanction);
                        })
                        ->get();

                    \Log::info("Found " . $groupViolations->count() . " violations in group");

                    foreach ($groupViolations as $violation) {
                        // Skip if this violation was already processed from individual selection
                        if (in_array($violation->violation_id, $processedViolationIds)) {
                            \Log::info("❌ SKIPPING - Already processed group violation: " . $violation->violation_id);
                            continue;
                        }

                        // Check if appointment already exists for this violation
                        $existingAppointment = ViolationAppointment::where('violation_id', $violation->violation_id)
                            ->where('violation_app_status', 'Scheduled')
                            ->first();

                        if ($existingAppointment) {
                            \Log::warning("❌ SKIPPING - Appointment already exists for group violation: " . $violation->violation_id);
                            $processedViolationIds[] = $violation->violation_id;
                            continue;
                        }

                        $appointment = ViolationAppointment::create([
                            'violation_id' => $violation->violation_id,
                            'violation_app_date' => $request->schedule_date,
                            'violation_app_time' => $request->schedule_time,
                            'violation_app_notes' => $request->violation_app_notes,
                            'violation_app_status' => 'Scheduled',
                            'handled_by' => 'prefect',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $createdAppointments[] = $appointment;
                        $processedViolationIds[] = $violation->violation_id;

                        // Store appointment info for SMS notification
                        $appointmentsForSMS[] = [
                            'appointment_id' => $appointment->violation_app_id,
                            'violation_id' => $violation->violation_id,
                            'violator_id' => $violation->violator_id,
                            'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                            'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                            'violation_status' => $violation->status,
                            'appointment_date' => $request->schedule_date,
                            'appointment_time' => $request->schedule_time,
                            'notes' => $request->violation_app_notes
                        ];

                        \Log::info("✅ CREATED - Appointment for group violation: " . $violation->violation_id, [
                            'appointment_id' => $appointment->violation_app_id,
                            'violation_status' => $violation->status
                        ]);
                    }
                } else {
                    \Log::warning("❌ INVALID GROUP KEY FORMAT: $groupKey");
                }
            }
        }

        DB::commit();

        \Log::info("=== FINAL RESULTS ===");
        \Log::info("Total appointments created: " . count($createdAppointments));
        \Log::info("Processed violation IDs: ", $processedViolationIds);
        \Log::info("Appointments for SMS: ", array_column($appointmentsForSMS, 'violation_id'));

        if (empty($createdAppointments)) {
            return response()->json([
                'success' => false,
                'message' => 'No appointments were created. Please check if the selected violations exist or if appointments already exist.'
            ], 400);
        }

        // Send SMS notifications after successful commit
        if (!empty($appointmentsForSMS)) {
            \Log::info("Sending SMS notifications for appointments", [
                'total_appointments' => count($appointmentsForSMS)
            ]);
            $this->sendAppointmentNotifications($appointmentsForSMS);
        }

        return response()->json([
            'success' => true,
            'message' => count($createdAppointments) . ' appointment(s) created successfully',
            'data' => $createdAppointments
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating appointments: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Error creating appointments: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Send SMS notifications for appointments to parents
 */
private function sendAppointmentNotifications($appointments)
{
    try {
        Log::info("Starting SMS notifications for appointments", ['total_appointments' => count($appointments)]);

        // Group appointments by violator to avoid duplicate SMS
        $violatorAppointments = [];

        foreach ($appointments as $appointment) {
            $violatorId = $appointment['violator_id'];

            if (!isset($violatorAppointments[$violatorId])) {
                $violatorAppointments[$violatorId] = [];
            }

            $violatorAppointments[$violatorId][] = $appointment;
        }

        Log::info("Grouped appointments for SMS", [
            'unique_violators' => count($violatorAppointments)
        ]);

        // Send ONE SMS per violator with all their appointments
        $smsSentCount = 0;
        foreach ($violatorAppointments as $violatorId => $appointmentList) {
            $this->sendAppointmentSMSToParent($violatorId, $appointmentList);
            $smsSentCount++;

            Log::info("SMS queued for appointment notification", [
                'violator_id' => $violatorId,
                'appointments_count' => count($appointmentList)
            ]);
        }

        Log::info("Appointment SMS notifications completed", ['total_sms_sent' => $smsSentCount]);

    } catch (\Exception $e) {
        Log::error('Appointment SMS notification failed: ' . $e->getMessage());
    }
}

/**
 * Send SMS to parent about appointment - PLAIN TEXT VERSION
 */
private function sendAppointmentSMSToParent($violatorId, $appointments)
{
    try {
        Log::info("DEBUG: Preparing APPOINTMENT SMS for violator: {$violatorId}", [
            'appointments_count' => count($appointments)
        ]);

        // Get student with parent information
        $student = DB::table('tbl_student as s')
            ->join('tbl_parent as p', 's.parent_id', '=', 'p.parent_id')
            ->where('s.student_id', $violatorId)
            ->select(
                's.student_fname',
                's.student_lname',
                'p.parent_fname',
                'p.parent_lname',
                'p.parent_contactinfo'
            )
            ->first();

        if (!$student) {
            Log::warning("Student not found for appointment SMS notification: {$violatorId}");
            return;
        }

        // DEBUG: Check if this is YOUR number
        $isMyNumber = in_array($student->parent_contactinfo, ['09513738659', '639513738659']);
        Log::info("DEBUG APPOINTMENT PHONE CHECK:", [
            'student_name' => $student->student_fname . ' ' . $student->student_lname,
            'parent_name' => $student->parent_fname . ' ' . $student->parent_lname,
            'parent_contact' => $student->parent_contactinfo,
            'is_my_number' => $isMyNumber
        ]);

        $parentPhone = $student->parent_contactinfo ?? null;

        if (!$parentPhone) {
            Log::warning("No parent contact found for student: {$student->student_fname} {$student->student_lname}");
            return;
        }

        Log::info("DEBUG: Parent phone found for appointment: {$parentPhone}");

        $studentName = $student->student_fname . ' ' . $student->student_lname;
        $parentName = $student->parent_fname . ' ' . $student->parent_lname;

        // Build appointment message
        $message = "Dear Parent/Guardian {$parentName},\n\n";
        $message .= "Your child {$studentName} has a scheduled appointment regarding school violation(s).\n\n";

        // Use the first appointment for date/time (they should all be the same)
        $firstAppointment = $appointments[0];
        $message .= "Appointment Details:\n";
        $message .= "Date: " . date('M j, Y', strtotime($firstAppointment['appointment_date'])) . "\n";
        $message .= "Time: " . date('g:i A', strtotime($firstAppointment['appointment_time'])) . "\n\n";

        $message .= "Related Violations:\n";

        // List all offenses from all appointments
        $offensesList = [];
        foreach ($appointments as $appointment) {
            $offenseName = $appointment['offense_name'];
            if (!in_array($offenseName, $offensesList)) {
                $offensesList[] = $offenseName;
                $message .= "- {$offenseName}\n";
            }
        }

        if (!empty($firstAppointment['notes'])) {
            $message .= "\nNotes: {$firstAppointment['notes']}\n";
        }

        $message .= "\nPlease be punctual for the appointment at Tagoloan Senior High School.";

        // Limit message length
        if (strlen($message) > 480) {
            $message = substr($message, 0, 477) . '...';
        }

        Log::info("DEBUG: Sending appointment SMS message", [
            'message_length' => strlen($message),
            'appointments_count' => count($appointments),
            'unique_offenses' => count($offensesList)
        ]);

        // Send SMS
        $smsResult = $this->smsService->sendSMS($parentPhone, $message);

        if ($smsResult['success']) {
            Log::info("✅ APPOINTMENT SMS sent to parent of {$studentName}", [
                'parent_name' => $parentName,
                'phone' => $parentPhone,
                'appointments_count' => count($appointments),
                'offenses' => $offensesList
            ]);
        } else {
            Log::error("❌ Failed to send appointment SMS to parent of {$studentName}", [
                'parent_name' => $parentName,
                'phone' => $parentPhone,
                'error' => $smsResult['error'] ?? 'Unknown error'
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error in sendAppointmentSMSToParent: ' . $e->getMessage());
    }
}

public function storeMultipleAnecdotals(Request $request)
{
    \Log::info('=== storeMultipleAnecdotals START ===');
    \Log::info('Request data:', $request->all());

    // Validate the request
    $validator = Validator::make($request->all(), [
        'violation_ids' => 'sometimes|array',
        'violation_ids.*' => 'exists:tbl_violation_record,violation_id',
        'group_keys' => 'sometimes|array',
        'group_keys.*' => 'string',
        'anecdotal_date' => 'required|date',
        'anecdotal_time' => 'required|date_format:H:i',
        'violation_anec_solution' => 'required|string|min:10|max:1000',
        'violation_anec_recommendation' => 'required|string|min:10|max:1000'
    ]);

    if ($validator->fails()) {
        \Log::error('Anecdotal validation errors:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $createdAnecdotals = [];
        $updatedAnecdotals = [];
        $allViolationIds = [];

        \Log::info('Processing individual violations...');
        // Process individual violations
        if ($request->has('violation_ids')) {
            $allViolationIds = array_merge($allViolationIds, $request->violation_ids);
            \Log::info('Individual violation IDs:', $request->violation_ids);
        }

        \Log::info('Processing group violations...');
        // Process group violations
        if ($request->has('group_keys')) {
            \Log::info('Group keys found:', $request->group_keys);

            foreach ($request->group_keys as $groupKey) {
                \Log::info("Processing group key: $groupKey");

                // Parse the group key to match your grouping logic
                $groupParts = explode('|', $groupKey);
                \Log::info("Group parts:", $groupParts);

                if (count($groupParts) >= 5) {
                    $incident = $groupParts[0];
                    $offenseType = $groupParts[1];
                    $sanction = $groupParts[2];
                    $date = $groupParts[3];
                    $timeGroup = $groupParts[4];

                    \Log::info("Searching for group with:", [
                        'incident' => $incident,
                        'offense_type' => $offenseType,
                        'sanction' => $sanction,
                        'date' => $date,
                        'time_group' => $timeGroup
                    ]);

                    // Find violations that match this exact group
                    $groupViolations = ViolationRecord::with(['offense', 'sanction'])
                        ->where('status', 'pending')
                        ->where('violation_incident', $incident)
                        ->where('violation_date', $date)
                        ->whereHas('offense', function($query) use ($offenseType) {
                            $query->where('offense_type', $offenseType);
                        })
                        ->whereHas('sanction', function($query) use ($sanction) {
                            $query->where('sanction_consequences', $sanction);
                        })
                        ->get();

                    \Log::info("Found " . $groupViolations->count() . " violations in this group");

                    if ($groupViolations->count() > 0) {
                        foreach ($groupViolations as $violation) {
                            $allViolationIds[] = $violation->violation_id;
                            \Log::info("Added violation ID: " . $violation->violation_id);
                        }
                    } else {
                        \Log::warning("No violations found for group key: $groupKey");
                    }
                } else {
                    \Log::warning("Invalid group key format: $groupKey");
                }
            }
        }

        // Remove duplicates
        $allViolationIds = array_unique($allViolationIds);
        \Log::info('All unique violation IDs to process:', $allViolationIds);

        if (empty($allViolationIds)) {
            \Log::warning('No violation IDs to process after filtering');
            return response()->json([
                'success' => false,
                'message' => 'No valid violations found to process. Please check if violations exist and are pending.'
            ], 400);
        }

        foreach ($allViolationIds as $violationId) {
            \Log::info("Processing violation ID: $violationId");

            // Check if violation exists and is pending
            $violation = ViolationRecord::with([
                'student.parent',
                'student.adviser',
                'offense',
                'sanction',
                'prefect'
            ])
            ->where('violation_id', $violationId)
            ->where('status', 'pending')
            ->first();

            if (!$violation) {
                \Log::warning("Violation not found or not pending: $violationId");

                // Let's check what the actual status is
                $violationCheck = ViolationRecord::find($violationId);
                if ($violationCheck) {
                    \Log::warning("Violation exists but status is: " . $violationCheck->status);
                } else {
                    \Log::warning("Violation does not exist in database");
                }
                continue;
            }

            // Check if anecdotal already exists for this violation
            $existingAnecdotal = ViolationAnecdotal::where('violation_id', $violationId)->first();

            if ($existingAnecdotal) {
                \Log::info("Updating existing anecdotal for violation: $violationId");

                // Update existing anecdotal record
                $existingAnecdotal->update([
                    'violation_anec_date' => $request->anecdotal_date,
                    'violation_anec_time' => $request->anecdotal_time,
                    'violation_anec_solution' => $request->violation_anec_solution,
                    'violation_anec_recommendation' => $request->violation_anec_recommendation,
                    'status' => 'completed',
                    'updated_at' => now()
                ]);

                $existingAnecdotal->load([
                    'violation.student.parent',
                    'violation.student.adviser',
                    'violation.offense',
                    'violation.sanction',
                    'violation.prefect'
                ]);

                $updatedAnecdotals[] = $existingAnecdotal;

                \Log::info("✅ Updated anecdotal record for violation $violationId", [
                    'anecdotal_id' => $existingAnecdotal->violation_anec_id,
                    'violation_id' => $violationId,
                    'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                ]);
            } else {
                \Log::info("Creating new anecdotal for violation: $violationId");

                // Create new anecdotal record
                $anecdotal = ViolationAnecdotal::create([
                    'violation_id' => $violationId,
                    'violation_anec_date' => $request->anecdotal_date,
                    'violation_anec_time' => $request->anecdotal_time,
                    'violation_anec_solution' => $request->violation_anec_solution,
                    'violation_anec_recommendation' => $request->violation_anec_recommendation,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $anecdotal->load([
                    'violation.student.parent',
                    'violation.student.adviser',
                    'violation.offense',
                    'violation.sanction',
                    'violation.prefect'
                ]);

                $createdAnecdotals[] = $anecdotal;

                \Log::info("✅ Created anecdotal record for violation $violationId", [
                    'anecdotal_id' => $anecdotal->violation_anec_id,
                    'violation_id' => $violationId,
                    'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                ]);
            }
        }

        DB::commit();

        $totalProcessed = count($createdAnecdotals) + count($updatedAnecdotals);
        \Log::info("✅ Total processed: $totalProcessed (Created: " . count($createdAnecdotals) . ", Updated: " . count($updatedAnecdotals) . ")");

        if ($totalProcessed === 0) {
            \Log::error('No anecdotal records were created or updated.');

            return response()->json([
                'success' => false,
                'message' => 'No anecdotal records were created or updated. Please check if violations exist and are pending.'
            ], 400);
        }

        $message = '';
        if (count($createdAnecdotals) > 0 && count($updatedAnecdotals) > 0) {
            $message = count($createdAnecdotals) . ' anecdotal record(s) created and ' . count($updatedAnecdotals) . ' record(s) updated successfully';
        } elseif (count($createdAnecdotals) > 0) {
            $message = count($createdAnecdotals) . ' anecdotal record(s) created successfully';
        } else {
            $message = count($updatedAnecdotals) . ' anecdotal record(s) updated successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'created' => $createdAnecdotals,
                'updated' => $updatedAnecdotals,
                'total_processed' => $totalProcessed
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('❌ Error creating anecdotal records: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
public function generateMultipleAnecdotalPDF(Request $request)
{
    try {
        $violationIds = $request->input('violation_ids', '');

        // Convert comma-separated string to array
        if (is_string($violationIds)) {
            $violationIds = explode(',', $violationIds);
        }

        // Remove any empty values and ensure they are integers
        $violationIds = array_filter(array_map('intval', $violationIds));

        if (empty($violationIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No violation IDs provided'
            ], 400);
        }

        // Get all selected violations with their details
        $violations = ViolationRecord::with([
            'student.parent',
            'student.adviser',
            'offense',
            'sanction',
            'prefect'
        ])
        ->whereIn('violation_id', $violationIds)
        ->get();

        if ($violations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No violations found for the provided IDs'
            ], 400);
        }

        // Get the solution and recommendation from the first anecdotal record
        $sampleAnecdotal = ViolationAnecdotal::whereIn('violation_id', $violationIds)
            ->first();

        $data = [
            'violations' => $violations,
            'solution' => $sampleAnecdotal->violation_anec_solution ?? 'No solution provided',
            'recommendation' => $sampleAnecdotal->violation_anec_recommendation ?? 'No recommendation provided',
            'currentDate' => now()->format('F d, Y'),
        ];

        $pdf = PDF::loadView('prefect.pdf.multiple_anecdotal', $data);

        return $pdf->download('anecdotal-record-multiple-' . time() . '.pdf');

    } catch (\Exception $e) {
        Log::error('Error generating multiple anecdotal PDF: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error generating PDF: ' . $e->getMessage()
        ], 500);
    }
}
    public function update(Request $request, $violationId)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'violator_id'        => 'required|exists:tbl_student,student_id',
            'offense_id'         => 'required|exists:tbl_offense,offense_id',
            'sanction_id'        => 'required|exists:tbl_sanction,sanction_id',
            'violation_incident' => 'required|string|max:255',
            'violation_date'     => 'required|date',
            'violation_time'     => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $violation = ViolationRecord::findOrFail($violationId);

            $violation->violator_id        = $request->input('violator_id');
            $violation->offense_id         = $request->input('offense_id');
            $violation->sanction_id        = $request->input('sanction_id');
            $violation->violation_incident = $request->input('violation_incident');
            $violation->violation_date     = $request->input('violation_date');
            $violation->violation_time     = $request->input('violation_time');

            $violation->save();

            return response()->json([
                'success' => true,
                'message' => '✅ Violation updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error updating violation: ' . $e->getMessage()
            ], 500);
        }
    }
/**
 * Update violation appointment
 */
public function updateAppointment(Request $request, $appointmentId)
{
    $validator = Validator::make($request->all(), [
        'appointment_date' => 'required|date',
        'appointment_time' => 'required|date_format:H:i',
        'appointment_status' => 'required|in:Pending,Scheduled,Completed,Cancelled',
        'violation_app_notes' => 'nullable|string|max:500'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $appointment = ViolationAppointment::findOrFail($appointmentId);

        $appointment->update([
            'violation_app_date' => $request->appointment_date,
            'violation_app_time' => $request->appointment_time,
            'violation_app_status' => $request->appointment_status,
            'violation_app_notes' => $request->violation_app_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating appointment: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update violation anecdotal
     */
    public function updateAnecdotal(Request $request, $anecdotalId)
    {
        $validator = Validator::make($request->all(), [
            'solution' => 'required|string|min:10|max:1000',
            'recommendation' => 'required|string|min:10|max:1000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|in:active,in_progress,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $anecdotal = ViolationAnecdotal::findOrFail($anecdotalId);

            $anecdotal->update([
                'violation_anec_solution' => $request->solution,
                'violation_anec_recommendation' => $request->recommendation,
                'violation_anec_date' => $request->date,
                'violation_anec_time' => $request->time,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anecdotal record updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating anecdotal record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archive(Request $request)
    {
        $request->validate([
            'violation_ids' => 'required|array',
            'violation_ids.*' => 'exists:tbl_violation_record,violation_id',
            'status' => 'required|in:resolved,under_review'
        ]);

        try {
            ViolationRecord::whereIn('violation_id', $request->violation_ids)
                   ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->violation_ids) . ' violation(s) archived as ' . $request->status . ' successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving violations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived violations
     */
    public function getArchived()
    {
        try {
            $archivedViolations = DB::table('tbl_violation_record')
                ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id')
                ->join('tbl_offense', 'tbl_violation_record.offense_id', '=', 'tbl_offense.offense_id')
                ->join('tbl_sanction', 'tbl_violation_record.sanction_id', '=', 'tbl_sanction.sanction_id')
                ->select(
                    'tbl_violation_record.*',
                    'tbl_student.student_fname',
                    'tbl_student.student_lname',
                    'tbl_offense.offense_type',
                    'tbl_sanction.sanction_consequences'
                )
                ->whereIn('tbl_violation_record.status', ['resolved', 'under_review'])
                ->orderBy('tbl_violation_record.updated_at', 'desc')
                ->get();

            return response()->json($archivedViolations);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Restore archived violations
     */
    public function restore(Request $request)
    {
        $request->validate([
            'violation_ids' => 'required|array',
            'violation_ids.*' => 'exists:tbl_violation_record,violation_id'
        ]);

        try {
            ViolationRecord::whereIn('violation_id', $request->violation_ids)
                   ->update(['status' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => count($request->violation_ids) . ' violation(s) restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring violations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete multiple violations
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'violation_ids' => 'required|array',
            'violation_ids.*' => 'exists:tbl_violation_record,violation_id'
        ]);

        try {
            ViolationRecord::whereIn('violation_id', $request->violation_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->violation_ids) . ' violation(s) deleted permanently'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting violations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archiveAppointments(Request $request)
    {
        $request->validate([
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:tbl_violation_appointment,violation_app_id',
            'status' => 'required|in:Completed,Cancelled'
        ]);

        try {
            ViolationAppointment::whereIn('violation_app_id', $request->appointment_ids)
                       ->update(['violation_app_status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->appointment_ids) . ' appointment(s) archived as ' . $request->status . ' successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive violation anecdotals
     */
    public function archiveAnecdotals(Request $request)
    {
        $request->validate([
            'anecdotal_ids' => 'required|array',
            'anecdotal_ids.*' => 'exists:tbl_violation_anecdotal,violation_anec_id',
            'status' => 'required|in:completed,closed'
        ]);

        try {
            ViolationAnecdotal::whereIn('violation_anec_id', $request->anecdotal_ids)
                       ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->anecdotal_ids) . ' anecdotal record(s) archived as ' . $request->status . ' successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving anecdotal records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived violation appointments
     */
    public function getArchivedAppointments()
    {
        try {
            $archivedAppointments = DB::table('tbl_violation_appointment')
                ->join('tbl_violation_record', 'tbl_violation_appointment.violation_id', '=', 'tbl_violation_record.violation_id')
                ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id')
                ->select(
                    'tbl_violation_appointment.*',
                    'tbl_student.student_fname',
                    'tbl_student.student_lname'
                )
                ->whereIn('tbl_violation_appointment.violation_app_status', ['Completed', 'Cancelled'])
                ->orderBy('tbl_violation_appointment.updated_at', 'desc')
                ->get();

            return response()->json($archivedAppointments);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Get archived violation anecdotals
     */
    public function getArchivedAnecdotals()
    {
        try {
            $archivedAnecdotals = DB::table('tbl_violation_anecdotal')
                ->join('tbl_violation_record', 'tbl_violation_anecdotal.violation_id', '=', 'tbl_violation_record.violation_id')
                ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id')
                ->select(
                    'tbl_violation_anecdotal.*',
                    'tbl_student.student_fname',
                    'tbl_student.student_lname'
                )
                ->whereIn('tbl_violation_anecdotal.status', ['completed', 'closed'])
                ->orderBy('tbl_violation_anecdotal.updated_at', 'desc')
                ->get();

            return response()->json($archivedAnecdotals);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Restore multiple archived records
     */
    public function restoreMultiple(Request $request)
    {
        $request->validate([
            'records' => 'required|array',
            'records.*.id' => 'required',
            'records.*.type' => 'required|in:violation,appointment,anecdotal'
        ]);

        try {
            $restoredCount = 0;

            foreach ($request->records as $record) {
                if ($record['type'] === 'violation') {
                    ViolationRecord::where('violation_id', $record['id'])
                        ->update(['status' => 'pending']);
                    $restoredCount++;
                } elseif ($record['type'] === 'appointment') {
                    ViolationAppointment::where('violation_app_id', $record['id'])
                        ->update(['violation_app_status' => 'Scheduled']);
                    $restoredCount++;
                } elseif ($record['type'] === 'anecdotal') {
                    ViolationAnecdotal::where('violation_anec_id', $record['id'])
                        ->update(['status' => 'completed']);
                    $restoredCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $restoredCount . ' record(s) restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple archived records permanently
     */
    public function destroyMultipleArchived(Request $request)
    {
        $request->validate([
            'records' => 'required|array',
            'records.*.id' => 'required',
            'records.*.type' => 'required|in:violation,appointment,anecdotal'
        ]);

        try {
            $deletedCount = 0;

            foreach ($request->records as $record) {
                if ($record['type'] === 'violation') {
                    ViolationRecord::where('violation_id', $record['id'])->delete();
                    $deletedCount++;
                } elseif ($record['type'] === 'appointment') {
                    ViolationAppointment::where('violation_app_id', $record['id'])->delete();
                    $deletedCount++;
                } elseif ($record['type'] === 'anecdotal') {
                    ViolationAnecdotal::where('violation_anec_id', $record['id'])->delete();
                    $deletedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' record(s) deleted permanently'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔍 Live Search Students
    public function searchStudents(Request $request)
    {
        $query = $request->input('query', '');
        $students = DB::table('tbl_student')
            ->where('student_fname', 'like', "%$query%")
            ->orWhere('student_lname', 'like', "%$query%")
            ->limit(10)
            ->get();

        $html = '';
        foreach ($students as $student) {
            $name = $student->student_fname . ' ' . $student->student_lname;
            $html .= "<div class='student-item' data-id='{$student->student_id}'>$name</div>";
        }
        return $html ?: '<div>No students found</div>';
    }

    // Search offenses
    public function searchOffenses(Request $request)
    {
        $query = $request->input('query', '');
        $offenses = DB::table('tbl_offense')
            ->select('offense_type', 'offense_id')
            ->where('offense_type', 'like', "%$query%")
            ->limit(10)
            ->get();

        $html = '';
        foreach ($offenses as $offense) {
            $html .= "<div class='offense-item' data-id='{$offense->offense_id}'>{$offense->offense_type}</div>";
        }
        return $html ?: '<div>No results found</div>';
    }

    // Search sanctions
    public function searchSanctions(Request $request)
    {
        $query = $request->input('query', '');
        $sanctions = DB::table('tbl_sanction')
            ->select('sanction_consequences', 'sanction_id')
            ->where('sanction_consequences', 'like', "%$query%")
            ->limit(10)
            ->get();

        $html = '';
        foreach ($sanctions as $sanction) {
            $html .= "<div class='sanction-item' data-id='{$sanction->sanction_id}'>{$sanction->sanction_consequences}</div>";
        }
        return $html ?: '<div>No results found</div>';
    }

// ENHANCED NOTIFICATION DATA HELPER METHOD WITH SPECIFIC DETAILS
private function getNotificationData()
{
    // Get recent violations (last 24 hours) with detailed information
    $newViolations = ViolationRecord::with(['student.adviser', 'offense'])
        ->where('status', 'pending')
        ->where('created_at', '>=', Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($violation) {
            $studentName = 'Unknown Student';
            $violationType = 'N/A';
            $gradeLevel = 'N/A';

            if ($violation->student) {
                $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;
                if ($violation->student->adviser) {
                    $gradeLevel = $violation->student->adviser->adviser_gradelevel ?? 'N/A';
                }
            }

            if ($violation->offense) {
                $violationType = $violation->offense->offense_type ?? 'N/A';
            }

            return [
                'student_name' => $studentName,
                'violation_type' => $violationType,
                'grade_level' => $gradeLevel,
                'date' => Carbon::parse($violation->violation_date)->format('M d, Y'),
                'created_at' => $violation->created_at,
                'time_ago' => Carbon::parse($violation->created_at)->diffForHumans()
            ];
        });

    $newViolationsCount = $newViolations->count();

    // Get recent students (last 24 hours) with detailed information
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
                'created_at' => $student->created_at,
                'time_ago' => Carbon::parse($student->created_at)->diffForHumans()
            ];
        });

    $newStudentsCount = $newStudents->count();

    // Get recent complaints (last 24 hours) with detailed information
    $newComplaints = Complaints::with(['complainant.adviser', 'offense'])
        ->where('status', 'pending')
        ->where('created_at', '>=', Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($complaint) {
            $studentName = 'Unknown Student';
            $complaintType = 'N/A';
            $gradeLevel = 'N/A';

            if ($complaint->complainant) {
                $studentName = $complaint->complainant->student_fname . ' ' . $complaint->complainant->student_lname;
                if ($complaint->complainant->adviser) {
                    $gradeLevel = $complaint->complainant->adviser->adviser_gradelevel ?? 'N/A';
                }
            }

            if ($complaint->offense) {
                $complaintType = $complaint->offense->offense_type ?? 'N/A';
            }

            return [
                'student_name' => $studentName,
                'complaint_type' => $complaintType,
                'grade_level' => $gradeLevel,
                'description' => $complaint->complaints_incident ?? 'N/A',
                'created_at' => $complaint->created_at,
                'time_ago' => Carbon::parse($complaint->created_at)->diffForHumans()
            ];
        });

    $newComplaintsCount = $newComplaints->count();

    $newParentsCount = 0; // You can add parent model when available

    $notificationCount = $newViolationsCount + $newStudentsCount + $newParentsCount + $newComplaintsCount;

    return [
        'notificationCount' => $notificationCount,
        'newViolationsCount' => $newViolationsCount,
        'newStudentsCount' => $newStudentsCount,
        'newParentsCount' => $newParentsCount,
        'newComplaintsCount' => $newComplaintsCount,
        'newViolations' => $newViolations,
        'newStudents' => $newStudents,
        'newComplaints' => $newComplaints
    ];
}
// KEEP THE OLD METHOD FOR BACKWARD COMPATIBILITY
private function getNotificationCounts()
{
    $data = $this->getNotificationData();
    return [
        'notificationCount' => $data['notificationCount'],
        'newViolationsCount' => $data['newViolationsCount'],
        'newStudentsCount' => $data['newStudentsCount'],
        'newParentsCount' => $data['newParentsCount'],
        'newComplaintsCount' => $data['newComplaintsCount']
    ];
}

    public function create()
    {
        // Get notification counts
        $notificationCounts = $this->getNotificationCounts();

        try {
            $students = Student::with(['adviser'])
                ->where('status', 'active')
                ->orderBy('student_lname')
                ->orderBy('student_fname')
                ->get();

            $offenses = Offense::where('category', 'violation')
                ->whereNull('deleted_at')
                ->orderBy('offense_type')
                ->get();

            return view('prefect.create-violation', array_merge(
                compact('students', 'offenses'),
                $notificationCounts
            ));

        } catch (\Exception $e) {
            Log::error('Error loading create complaint form: ' . $e->getMessage());
            return redirect()->route('prefect.violation')
                ->with('error', 'Error loading complaint form: ' . $e->getMessage());
        }
    }


    /**
     * Get violation details for selected violations (for modal display)
     */
    public function getSelectedViolations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'violation_ids' => 'required|array',
            'violation_ids.*' => 'exists:tbl_violation_record,violation_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid violation IDs',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->whereIn('violation_id', $request->violation_ids)
                ->where('status', 'pending')
                ->get()
                ->map(function ($violation) {
                    return [
                        'violation_id' => $violation->violation_id,
                        'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'incident' => $violation->violation_incident,
                        'offense_type' => $violation->offense->offense_type,
                        'sanction' => $violation->sanction->sanction_consequences
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $violations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching violation details: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getOffenseCounts(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'violator_ids' => 'required|array',
            'violator_ids.*' => 'exists:tbl_student,student_id',
            'offense_ids' => 'required|array',
            'offense_ids.*' => 'exists:tbl_offense,offense_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided'
            ], 422);
        }

        $violatorIds = $request->violator_ids;
        $offenseIds = $request->offense_ids;

        $counts = [];
        $offenseNames = [];

        // Get offense names
        $offenses = Offense::whereIn('offense_id', $offenseIds)->get();
        foreach ($offenses as $offense) {
            $offenseNames[$offense->offense_id] = $offense->offense_type;
        }

        foreach ($offenseIds as $offenseId) {
            $offenseCounts = [];

            foreach ($violatorIds as $violatorId) {
                // Get student info
                $student = Student::find($violatorId);

                if ($student) {
                    // Count previous offenses of this type for this student
                    $offenseCount = ViolationRecord::where('violator_id', $violatorId)
                        ->where('offense_id', $offenseId)
                        ->where('status', 'pending') // Include only pending violations
                        ->count();

                    $offenseCounts['students'][$violatorId] = [
                        'name' => $student->student_fname . ' ' . $student->student_lname,
                        'count' => $offenseCount
                    ];
                }
            }

            if (!empty($offenseCounts)) {
                $counts[$offenseId] = $offenseCounts;
            }
        }

        return response()->json([
            'success' => true,
            'counts' => $counts,
            'offense_names' => $offenseNames
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching offense counts: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error fetching offense counts'
        ], 500);
    }
}

public function getSanctionsByOffense(Request $request)
    {
        try {
            $offenseId = $request->input('offense_id');

            Log::info('Getting sanctions for offense ID: ' . $offenseId);

            if (!$offenseId) {
                return response()->json([
                    ['sanction_id' => 'not_assigned', 'sanction_consequences' => 'NOT ASSIGNED', 'sanction_description' => 'Please select an offense first']
                ]);
            }

            // Get sanctions from the sanction stages table for this specific offense
            $sanctions = DB::table('tbl_offense_with_sanction_stages as owss')
                ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
                ->where('owss.offense_id', $offenseId)
                ->select('s.sanction_id', 's.sanction_consequences', 's.sanction_description')
                ->whereNull('owss.deleted_at')
                ->whereNull('s.deleted_at')
                ->orderBy('owss.owss_id')
                ->get();

            Log::info('Found ' . $sanctions->count() . ' sanctions for offense ID: ' . $offenseId);

            // Convert all sanction consequences to uppercase for consistency
            $sanctions = $sanctions->map(function ($sanction) {
                $sanction->sanction_consequences = strtoupper($sanction->sanction_consequences);
                return $sanction;
            });

            // Add "NOT ASSIGNED" sanction as the first option
            $notAssignedSanction = [
                (object) [
                    'sanction_id' => 'not_assigned',
                    'sanction_consequences' => 'NOT ASSIGNED',
                    'sanction_description' => 'Default sanction for complaints that have not been assigned a specific consequence yet.'
                ]
            ];

            // Merge "NOT ASSIGNED" with the existing sanctions from stages
            $allSanctions = array_merge($notAssignedSanction, $sanctions->toArray());

            Log::info('Returning ' . count($allSanctions) . ' total sanctions');

            return response()->json($allSanctions);

        } catch (\Exception $e) {
            Log::error('Error in getSanctionsByOffense: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                (object) [
                    'sanction_id' => 'not_assigned',
                    'sanction_consequences' => 'ERROR - CHECK LOGS',
                    'sanction_description' => 'There was an error loading sanctions. Please check the server logs.'
                ]
            ], 500);
        }
    }
public function getOffenseHistory(Request $request)
{
    try {
        $studentIds = $request->input('student_ids');
        $offenseId = $request->input('offense_id');

        \Log::info('🔍 getOffenseHistory called:', [
            'student_ids' => $studentIds,
            'offense_id' => $offenseId
        ]);

        // Validate input
        if (!$studentIds || !$offenseId) {
            \Log::warning('Missing required parameters for offense history');
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Ensure studentIds is an array
        if (!is_array($studentIds)) {
            $studentIds = [$studentIds];
        }

        $history = [];

        foreach ($studentIds as $studentId) {
            \Log::info("📊 Getting UNIQUE offense history for student: {$studentId}, offense: {$offenseId}");

            // GET UNIQUE VIOLATIONS (grouped by date, time, incident, offense_id)
            $uniqueViolations = \DB::table('tbl_violation_record')
                ->where('violator_id', $studentId)
                ->where('offense_id', $offenseId)
                ->where('status', 'on progress')
                ->select(
                    'violation_date',
                    'violation_time',
                    'violation_incident',
                    'offense_id'
                )
                ->distinct()
                ->get();

            $violationCount = $uniqueViolations->count();

            // GET UNIQUE COMPLAINTS (grouped by date, time, incident, offense_id)
            $uniqueComplaints = \DB::table('tbl_complaints')
                ->where('respondent_id', $studentId)
                ->where('offense_id', $offenseId)
                ->where('status', 'on progress')
                ->select(
                    'complaints_date',
                    'complaints_time',
                    'complaints_incident',
                    'offense_id'
                )
                ->distinct()
                ->get();

            $complaintCount = $uniqueComplaints->count();

            $totalCount = $violationCount + $complaintCount;

            \Log::info("📈 Student {$studentId} UNIQUE offense count:", [
                'unique_violations' => $violationCount,
                'unique_complaints' => $complaintCount,
                'total_unique_count' => $totalCount
            ]);

            // Get recent violation records (get all then filter for unique in PHP)
            $violationRecords = \DB::table('tbl_violation_record as vr')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->where('vr.violator_id', $studentId)
                ->where('vr.offense_id', $offenseId)
                ->where('vr.status', 'on progress')
                ->select(
                    'vr.violation_date as date',
                    'vr.violation_time as time',
                    'vr.violation_incident as description',
                    'o.offense_type',
                    'o.offense_id',
                    'vr.created_at'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->get();

            // Get recent complaint records (get all then filter for unique in PHP)
            $complaintRecords = \DB::table('tbl_complaints as c')
                ->join('tbl_offense as o', 'c.offense_id', '=', 'o.offense_id')
                ->where('c.respondent_id', $studentId)
                ->where('c.offense_id', $offenseId)
                ->where('c.status', 'on progress')
                ->select(
                    'c.complaints_date as date',
                    'c.complaints_time as time',
                    'c.complaints_incident as description',
                    'o.offense_type',
                    'o.offense_id',
                    'c.created_at'
                )
                ->orderBy('c.complaints_date', 'desc')
                ->get();

            // Combine all records and filter for unique ones
            $allRecords = $violationRecords->merge($complaintRecords);

            // Filter for unique records based on date, time, description, and offense_id
            $uniqueRecords = $allRecords->unique(function ($record) {
                return $record->date . '|' . $record->time . '|' . $record->description . '|' . $record->offense_id;
            });

            // Take only the most recent 3 unique records
            $recentUniqueRecords = $uniqueRecords->sortByDesc('created_at')
                ->take(3)
                ->values();

            // Format the records for frontend
            $formattedRecords = $recentUniqueRecords->map(function($record) {
                return [
                    'date' => \Carbon\Carbon::parse($record->date)->format('M d, Y'),
                    'time' => $record->time,
                    'description' => $record->description,
                    'offense_type' => $record->offense_type,
                    'offense_id' => $record->offense_id
                ];
            })->toArray();

            $history[$studentId] = [
                'count' => $totalCount, // This now shows UNIQUE count
                'records' => $formattedRecords
            ];

            \Log::info("✅ Student {$studentId} UNIQUE history prepared:", [
                'unique_count' => $totalCount,
                'unique_records_count' => count($formattedRecords)
            ]);
        }

        \Log::info('🎯 Final UNIQUE offense history data:', $history);

        return response()->json($history);

    } catch (\Exception $e) {
        \Log::error('❌ Error in getOffenseHistory: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        // Return empty data structure instead of error to prevent frontend issues
        $fallbackHistory = [];
        foreach ($request->input('student_ids', []) as $studentId) {
            if (!is_array($studentId)) {
                $fallbackHistory[$studentId] = [
                    'count' => 0,
                    'records' => []
                ];
            }
        }

        return response()->json($fallbackHistory);
    }
}

public function getGroupStudents($groupKey)
{
    try {
        // Get violations with this group key
        $violations = Violation::where('group_key', $groupKey)
            ->with('student')
            ->get();

        // Extract unique students
        $students = $violations->map(function($violation) {
            return $violation->student;
        })->unique('student_id')->values();

        return response()->json([
            'success' => true,
            'students' => $students,
            'count' => $students->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading group students: ' . $e->getMessage()
        ], 500);
    }
}

public function getStudentOffenseHistory(Request $request)
{
    try {
        $studentIds = $request->input('student_ids');

        \Log::info('🔍 getStudentOffenseHistory called:', [
            'student_ids' => $studentIds
        ]);

        if (!$studentIds) {
            return response()->json(['error' => 'Missing student IDs'], 400);
        }

        if (!is_array($studentIds)) {
            $studentIds = [$studentIds];
        }

        $history = [];

        foreach ($studentIds as $studentId) {
            // GET UNIQUE VIOLATIONS (grouped by date, time, incident, offense_id)
            $uniqueViolations = \DB::table('tbl_violation_record')
                ->where('violator_id', $studentId)
                ->where('status', 'on progress')
                ->select(
                    'violation_date',
                    'violation_time',
                    'violation_incident',
                    'offense_id'
                )
                ->distinct()
                ->get();

            $violationCount = $uniqueViolations->count();

            // GET UNIQUE COMPLAINTS (grouped by date, time, incident, offense_id)
            $uniqueComplaints = \DB::table('tbl_complaints')
                ->where('respondent_id', $studentId)
                ->where('status', 'on progress')
                ->select(
                    'complaints_date',
                    'complaints_time',
                    'complaints_incident',
                    'offense_id'
                )
                ->distinct()
                ->get();

            $complaintCount = $uniqueComplaints->count();

            $totalCount = $violationCount + $complaintCount;

            \Log::info("📊 Student {$studentId} UNIQUE total offenses:", [
                'unique_violations' => $violationCount,
                'unique_complaints' => $complaintCount,
                'total_unique' => $totalCount
            ]);

            // Get recent violation records (get all then filter for unique)
            $violationRecords = \DB::table('tbl_violation_record as vr')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->where('vr.violator_id', $studentId)
                ->where('vr.status', 'on progress')
                ->select(
                    'vr.violation_date as date',
                    'vr.violation_time as time',
                    'vr.violation_incident as description',
                    'o.offense_type',
                    'o.offense_id',
                    'vr.created_at'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->get();

            // Get recent complaint records (get all then filter for unique)
            $complaintRecords = \DB::table('tbl_complaints as c')
                ->join('tbl_offense as o', 'c.offense_id', '=', 'o.offense_id')
                ->where('c.respondent_id', $studentId)
                ->where('c.status', 'on progress')
                ->select(
                    'c.complaints_date as date',
                    'c.complaints_time as time',
                    'c.complaints_incident as description',
                    'o.offense_type',
                    'o.offense_id',
                    'c.created_at'
                )
                ->orderBy('c.complaints_date', 'desc')
                ->get();

            // Combine all records and filter for unique ones
            $allRecords = $violationRecords->merge($complaintRecords);

            // Filter for unique records based on date, time, description, and offense_id
            $uniqueRecords = $allRecords->unique(function ($record) {
                return $record->date . '|' . $record->time . '|' . $record->description . '|' . $record->offense_id;
            });

            // Take only the most recent 3 unique records
            $recentUniqueRecords = $uniqueRecords->sortByDesc('created_at')
                ->take(3)
                ->values();

            $formattedRecords = $recentUniqueRecords->map(function($record) {
                return [
                    'date' => \Carbon\Carbon::parse($record->date)->format('M d, Y'),
                    'time' => $record->time,
                    'description' => $record->description,
                    'offense_type' => $record->offense_type,
                    'offense_id' => $record->offense_id
                ];
            })->toArray();

            $history[$studentId] = [
                'count' => $totalCount, // This now shows UNIQUE count
                'records' => $formattedRecords
            ];
        }

        \Log::info('✅ Student UNIQUE offense history loaded:', [
            'student_count' => count($studentIds),
            'data' => $history
        ]);

        return response()->json($history);

    } catch (\Exception $e) {
        \Log::error('❌ Error in getStudentOffenseHistory: ' . $e->getMessage());

        // Return fallback data
        $fallbackHistory = [];
        foreach ($request->input('student_ids', []) as $studentId) {
            if (!is_array($studentId)) {
                $fallbackHistory[$studentId] = [
                    'count' => 0,
                    'records' => []
                ];
            }
        }

        return response()->json($fallbackHistory);
    }
}
public function generateAnecdotalPDF($anecdotalId)
{
    try {
        // Get the anecdotal record with all related data
        $anecdotal = ViolationAnecdotal::with([
            'violation.student.parent',
            'violation.student.adviser',
            'violation.offense',
            'violation.sanction',
            'violation.prefect'
        ])->findOrFail($anecdotalId);

        $data = [
            'anecdotal' => $anecdotal,
            'student' => $anecdotal->violation->student,
            'violation' => $anecdotal->violation,
            'currentDate' => now()->format('F d, Y'),
        ];

        $pdf = PDF::loadView('prefect.pdf.anecdotal', $data);

        return $pdf->download('anecdotal-record-' . $anecdotal->violation_anec_id . '.pdf');

    } catch (\Exception $e) {
        Log::error('Error generating PDF: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error generating PDF: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Export selected anecdotal records as PDF
 */
public function exportSelectedPdf(Request $request)
{
    try {
        \Log::info('=== exportSelectedPdf START ===');
        \Log::info('Request data:', $request->all());

        $request->validate([
            'anecdotal_ids' => 'required|array',
            'anecdotal_ids.*' => 'exists:tbl_violation_anecdotal,violation_anec_id'
        ]);

        $pdfData = [];
        $anecdotalIds = $request->anecdotal_ids;

        \Log::info('Processing anecdotal IDs for PDF export:', $anecdotalIds);

        foreach ($anecdotalIds as $id) {
            try {
                \Log::info("Processing anecdotal ID: {$id}");

                $anecdotal = ViolationAnecdotal::with([
                    'violation.student.parent',
                    'violation.student.adviser',
                    'violation.offense',
                    'violation.sanction'
                ])->findOrFail($id);

                \Log::info("Found anecdotal record", [
                    'anecdotal_id' => $anecdotal->violation_anec_id,
                    'student_name' => $anecdotal->violation->student->student_fname . ' ' . $anecdotal->violation->student->student_lname
                ]);

                // Generate PDF for this anecdotal record using your existing method
                $pdf = PDF::loadView('prefect.pdf.anecdotal', [
                    'anecdotal' => $anecdotal,
                    'student' => $anecdotal->violation->student,
                    'violation' => $anecdotal->violation,
                    'currentDate' => now()->format('F d, Y'),
                ]);

                $pdfContent = $pdf->output();

                $pdfData[] = [
                    'content' => base64_encode($pdfContent),
                    'filename' => 'Anecdotal_Record_' .
                                 str_replace(' ', '_', $anecdotal->violation->student->student_fname . '_' . $anecdotal->violation->student->student_lname) . '_' .
                                 $anecdotal->violation_anec_id . '.pdf'
                ];

                \Log::info("PDF generated for anecdotal ID: {$id}");

            } catch (\Exception $e) {
                \Log::error("Error processing anecdotal ID {$id}: " . $e->getMessage());
                continue; // Skip this record and continue with others
            }
        }

        if (empty($pdfData)) {
            \Log::warning('No PDF data generated for any of the selected records');
            return response()->json([
                'success' => false,
                'message' => 'No PDF data could be generated for the selected records'
            ], 400);
        }

        \Log::info('=== exportSelectedPdf COMPLETE ===', [
            'total_pdfs_generated' => count($pdfData)
        ]);

        return response()->json([
            'success' => true,
            'pdf_data' => $pdfData,
            'count' => count($pdfData)
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in exportSelectedPdf: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error exporting PDFs: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate PDF for a single anecdotal record
 */
private function generatePdfForAnecdotal($anecdotal)
{
    // Your existing PDF generation logic here
    // This should return the PDF data

    $data = [
        'anecdotalId' => $anecdotal->violation_anec_id,
        'studentName' => $anecdotal->violation->student->student_fname . ' ' . $anecdotal->violation->student->student_lname,
        'studentId' => $anecdotal->violation->student->student_id,
        'gradeSection' => $anecdotal->violation->student->grade_level . ' - ' . $anecdotal->violation->student->section,
        'adviser' => $anecdotal->violation->student->adviser->adviser_fname . ' ' . $anecdotal->violation->student->adviser->adviser_lname,
        'parentName' => $anecdotal->violation->student->parent->parent_fname . ' ' . $anecdotal->violation->student->parent->parent_lname,
        'incident' => $anecdotal->violation->violation_incident,
        'offense' => $anecdotal->violation->offense->offense_type,
        'solution' => $anecdotal->violation_anec_solution,
        'recommendation' => $anecdotal->violation_anec_recommendation,
        'anecdotalDate' => $anecdotal->violation_anec_date,
        'violationDate' => $anecdotal->violation->violation_date
    ];

    // Generate PDF using your existing PDF generation logic
    $pdf = $this->generatePdf($data);

    return [
        'content' => base64_encode($pdf),
        'filename' => 'Violation_Anecdotal_' . $data['studentName'] . '_' . $data['anecdotalId'] . '.pdf'
    ];
}
/**
 * Generate PDF content for a single anecdotal record
 */
private function generateSingleAnecdotalPDF($anecdotal)
{
    try {
        $data = [
            'anecdotal' => $anecdotal,
            'student' => $anecdotal->violation->student,
            'violation' => $anecdotal->violation,
            'currentDate' => now()->format('F d, Y'),
        ];

        // Use your existing PDF view
        $pdf = PDF::loadView('prefect.pdf.anecdotal', $data);

        return $pdf->output();

    } catch (\Exception $e) {
        \Log::error('Error generating single anecdotal PDF: ' . $e->getMessage());
        throw $e; // Re-throw to be caught by the parent method
    }
}
/**
 * Generate multiple anecdotal records
 */
public function generateMultipleAnecdotals(Request $request)
{
    try {
        \Log::info('=== generateMultipleAnecdotals START ===');
        \Log::info('Request data:', $request->all());

        $request->validate([
            'students' => 'required|array',
            'solution' => 'required|string',
            'recommendation' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'generate_option' => 'required|in:single,individual',
            'include_pdf' => 'boolean'
        ]);

        $generatedIds = [];
        $pdfData = [];
        $prefect_id = Auth::id() ?? 1;

        \Log::info('Starting anecdotal generation', [
            'students_count' => count($request->students),
            'generate_option' => $request->generate_option,
            'include_pdf' => $request->include_pdf
        ]);

        foreach ($request->students as $student) {
            try {
                // Get the most recent violation for this student
                $violation = ViolationRecord::where('violator_id', $student['student_id'])
                    ->where('handled_by', 'prefect')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$violation) {
                    \Log::warning("No recent violation found for student ID: " . $student['student_id']);
                    continue;
                }

                // Check if anecdotal already exists for this violation
                $existingAnecdotal = ViolationAnecdotal::where('violation_id', $violation->violation_id)->first();

                if ($existingAnecdotal) {
                    \Log::info("Updating existing anecdotal for violation: " . $violation->violation_id);

                    $existingAnecdotal->update([
                        'violation_anec_solution' => $request->solution,
                        'violation_anec_recommendation' => $request->recommendation,
                        'violation_anec_date' => $request->date,
                        'violation_anec_time' => $request->time,
                        'status' => 'completed',
                        'updated_at' => now()
                    ]);

                    $anecdotal = $existingAnecdotal;
                } else {
                    \Log::info("Creating new anecdotal for violation: " . $violation->violation_id);

                    $anecdotal = ViolationAnecdotal::create([
                        'violation_id' => $violation->violation_id,
                        'violation_anec_solution' => $request->solution,
                        'violation_anec_recommendation' => $request->recommendation,
                        'violation_anec_date' => $request->date,
                        'violation_anec_time' => $request->time,
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $generatedIds[] = $anecdotal->violation_anec_id;

                // Generate PDF if requested
                if ($request->include_pdf) {
                    $anecdotal->load([
                        'violation.student.parent',
                        'violation.student.adviser',
                        'violation.offense',
                        'violation.sanction'
                    ]);

                    try {
                        $pdfContent = $this->generateSingleAnecdotalPDF($anecdotal);

                        $pdfData[] = [
                            'content' => base64_encode($pdfContent),
                            'filename' => 'Anecdotal_Record_' .
                                         str_replace(' ', '_', $anecdotal->violation->student->student_fname . '_' . $anecdotal->violation->student->student_lname) . '_' .
                                         $anecdotal->violation_anec_id . '.pdf'
                        ];

                        \Log::info("PDF generated for anecdotal ID: " . $anecdotal->violation_anec_id);
                    } catch (\Exception $pdfError) {
                        \Log::error("Failed to generate PDF for anecdotal ID " . $anecdotal->violation_anec_id . ": " . $pdfError->getMessage());
                    }
                }

                \Log::info("✅ Anecdotal processed for student: " . $student['student_name'], [
                    'anecdotal_id' => $anecdotal->violation_anec_id,
                    'violation_id' => $violation->violation_id
                ]);

            } catch (\Exception $e) {
                \Log::error("Error processing student " . ($student['student_name'] ?? 'Unknown') . ": " . $e->getMessage());
                continue; // Skip this student and continue with others
            }
        }

        if (empty($generatedIds)) {
            \Log::warning('No anecdotal records were generated');
            return response()->json([
                'success' => false,
                'message' => 'No anecdotal records were generated. Please check if students have violation records.'
            ], 400);
        }

        \Log::info('=== generateMultipleAnecdotals COMPLETE ===', [
            'generated_count' => count($generatedIds),
            'pdfs_generated' => count($pdfData)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anecdotal records generated successfully',
            'count' => count($generatedIds),
            'pdf_data' => $request->include_pdf ? $pdfData : null
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in generateMultipleAnecdotals: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error generating anecdotal records: ' . $e->getMessage()
        ], 500);
    }
}



}
