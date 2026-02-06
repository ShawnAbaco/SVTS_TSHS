<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Services\PhilSMSService;
use App\Models\ViolationAnecdotal;


use Barryvdh\DomPDF\Facade\Pdf;


class AViolationController extends Controller
{
    // UPDATED NOTIFICATION DATA HELPER METHOD WITH DETAILED INFORMATION
    private function getNotificationData()
    {
        // Get recent violations (last 24 hours) with details
        $newViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
            ->where('status', 'pending')
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
                    'student_name' => $studentName,
                    'violation_type' => $violationType,
                    'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
                    'created_at' => $violation->created_at
                ];
            });

        $newViolationsCount = $newViolations->count();

        // Get recent students (last 24 hours) with details
        $newStudents = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
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

        // Get recent complaints (last 24 hours) with details
        $newComplaints = \App\Models\Complaints::with(['complainant.adviser', 'offense'])
            ->where('status', 'pending')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($complaint) {
                $studentName = 'Unknown Student';
                $complaintType = 'N/A';

                if ($complaint->complainant) {
                    $studentName = $complaint->complainant->student_fname . ' ' . $complaint->complainant->student_lname;
                }

                if ($complaint->offense) {
                    $complaintType = $complaint->offense->offense_type ?? 'N/A';
                }

                return [
                    'student_name' => $studentName,
                    'complaint_type' => $complaintType,
                    'description' => $complaint->complaints_incident ?? 'N/A',
                    'created_at' => $complaint->created_at
                ];
            });

        $newComplaintsCount = $newComplaints->count();

        $newParentsCount = 0;

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

   public function index(Request $request)
{
    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    $viewType = $request->get('view', 'individual');
    $groupKey = $request->get('group');

    // Initialize variables to avoid undefined errors
    $violations = collect();
    $byGroupViolations = collect();

    // Get the actual dates from your violation records - EXCLUDE INACTIVE
    $mostRecentViolationDate = DB::table('tbl_violation_record')
        ->where('status', '!=', 'inactive')
        ->max('violation_date');

    $earliestViolationDate = DB::table('tbl_violation_record')
        ->where('status', '!=', 'inactive')
        ->min('violation_date');

    // Use the most recent violation date for calculations, or today if no records exist
    $referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

    // Calculate date ranges based on the actual violation dates
    $today = $referenceDate->copy();
    $startOfWeek = $referenceDate->copy()->startOfWeek();
    $endOfWeek = $referenceDate->copy()->endOfWeek();
    $startOfMonth = $referenceDate->copy()->startOfMonth();
    $endOfMonth = $referenceDate->copy()->endOfMonth();

    // Summary Counts - Count ALL violations EXCEPT INACTIVE
    $dailyViolations = DB::table('tbl_violation_record')
        ->whereDate('violation_date', $today)
        ->where('status', '!=', 'inactive')
        ->count();

    $weeklyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
        ->where('status', '!=', 'inactive')
        ->count();

    $monthlyViolations = DB::table('tbl_violation_record')
        ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
        ->where('status', '!=', 'inactive')
        ->count();

    // Handle different view types - EXCLUDE INACTIVE STATUS
    if ($viewType == 'group') {
        // GROUPED VIOLATIONS LOGIC - Exclude inactive status
        $groupedViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
            ->where('status', '!=', 'inactive') // Exclude inactive violations
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
                'status' => $mostRecentViolation->status, // Include status for group rows
                'latest_created_at' => $mostRecentViolation->created_at,
                'latest_updated_at' => $mostRecentViolation->updated_at,
                'student_count' => $group->count(),
                'students' => $group->pluck('student'),
                'violation_ids' => $group->pluck('violation_id')
            ];
        })
        ->sortByDesc('latest_created_at')
        ->sortByDesc('latest_updated_at')
        ->values();

    } elseif ($viewType == 'individual') {
        if ($groupKey) {
            // Viewing individual violations for a specific group - newest first - EXCLUDE INACTIVE
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('status', '!=', 'inactive') // Exclude inactive violations
                ->whereIn('violation_id', function($query) use ($groupKey) {
                    $query->select('vr.violation_id')
                          ->from('tbl_violation_record as vr')
                          ->join('tbl_offense_sanction as off', 'vr.offense_sanc_id', '=', 'off.offense_sanc_id')
                          ->join('tbl_sanction as s', 'off.sanction_id', '=', 's.sanction_id')
                          ->where('vr.status', '!=', 'inactive') // Also exclude inactive in subquery
                          ->whereRaw("CONCAT(vr.violation_incident, '|', off.offense_type, '|', s.sanction_consequences, '|', vr.violation_date, '|', HOUR(vr.violation_time)) = ?", [$groupKey]);
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->paginate(5); // 5 records per page

        } else {
            // SIMPLE MERGING: Group by student, date, and time, then manually paginate - EXCLUDE INACTIVE
            $allViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('status', '!=', 'inactive') // Exclude inactive violations
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->get();

            $groupedViolations = $allViolations->groupBy(function($violation) {
                return $violation->violator_id . '|' . $violation->violation_date . '|' . $violation->violation_time;
            });

            $mergedViolations = $groupedViolations->map(function($group) {
                if ($group->count() == 1) {
                    return $group->first();
                }

                // For multiple violations, use the first one but add merged info
                $first = $group->first();
                $first->merged_count = $group->count();
                $first->merged_violation_ids = $group->pluck('violation_id');
                $first->merged_offense_types = $group->pluck('offense.offense_type')->unique()->implode(', ');
                $first->merged_sanctions = $group->pluck('sanction.sanction_consequences')->unique()->implode(', ');
                return $first;
            })->values();

            // Manual pagination - 5 records per page
            $page = $request->get('page', 1);
            $perPage = 5;
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

    // Fetch Offenses and Sanctions (used in edit modal)
    $offenses = Offense::all();
    $sanctions = Sanction::all();

    // Prepare only the data that's actually used in the Blade template
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
    return view('adviser.violationrecord', array_merge($data, $notificationData));
}

    public function indexAnecdotal()
{
    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    // FIXED: Use paginate() instead of get() for $vanecdotals
    $vanecdotals = ViolationAnecdotal::with(['violation.student'])
        ->where('status', 'completed')
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10); // 10 records per page

    $vappointments = ViolationAppointment::with(['violation.student'])
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
        ->paginate(5); // 5 records per page

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
        ->paginate(5); // 5 records per page

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
        ->paginate(5); // 5 records per page

    // Fetch Offenses and Sanctions
    $offenses = Offense::all();
    $sanctions = Sanction::all();

    // Prepare all data
    $data = compact(
        'violations',
        'appointments',
        'anecdotals',
        'vanecdotals', // This is now paginated
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
    return view('adviser.violationAnecdotal', array_merge($data, $notificationData));
}
    public function indexAppointment()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        // Only keep what's used in the Blade template
        $vappointments = ViolationAppointment::with(['violation.student'])
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: 5); // 4 records per page

        $offenses = Offense::all();

        // Prepare data
        $data = compact(
            'vappointments',
            'offenses'
        );

        // Return to Blade with merged notification data
        return view('adviser.violationAppointment', array_merge($data, $notificationData));
    }

       public function store(Request $request)
    {
        \Log::info('=== ADVISER VIOLATION STORE START ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request data count - violations: ' . count($request->input('violations', [])));
        \Log::info('Request data count - offenses: ' . count($request->input('offenses', [])));

        try {
            DB::beginTransaction();

            $adviser_id = Auth::id() ?? 1;
            $violationsData = $request->input('violations', []);
            $offensesData = $request->input('offenses', []);
            $savedCount = 0;
            $messages = [];

            \Log::info('Adviser violations submission details', [
                'count' => count($violationsData),
                'offenses_count' => count($offensesData),
                'adviser_id' => $adviser_id,
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

            // Array to track violations that need SMS notification
            $violationsForSMS = [];

            // Check if this is a group submission (multiple violators in one violation)
            $isGroupSubmission = $this->isGroupSubmission($violationsData);

            foreach ($violationsData as $violationIndex => $violation) {
                // Extract all fields
                extract(array_merge([
                    'violator_id' => null,
                    'violator_ids' => [], // For group submissions
                    'date' => null,
                    'time' => null,
                    'incident' => null,
                    'witnesses' => null,
                    'evidence_description' => null,
                    'sanction_id' => null,
                ], $violation));

                // Handle group submissions (multiple violators)
                $violatorIds = [];
                if ($isGroupSubmission && !empty($violator_ids) && is_array($violator_ids)) {
                    $violatorIds = $violator_ids;
                } else if (!empty($violator_id)) {
                    $violatorIds = [$violator_id];
                }

                // Skip if no violators found or required fields empty
                if (empty($violatorIds) || empty($date) || empty($time) || empty($incident)) {
                    continue;
                }

                // Process each violator
                foreach ($violatorIds as $currentViolatorId) {
                    // Validate violator exists
                    $violatorExists = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                    if (!$violatorExists) {
                        continue;
                    }

                    // Get violator name for logging
                    $violator = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                    $violatorName = $violator ? "{$violator->student_fname} {$violator->student_lname}" : 'Unknown';

                    // Create ONE violation record per offense-sanction pair per violator
                    foreach ($offensesData as $offenseIndex => $offense) {
                        $offenseId = $offense['offense_id'] ?? null;
                        $sanctionId = $offense['sanction_id'] ?? null;

                        if (empty($offenseId)) {
                            continue;
                        }

                        // Validate offense exists
                        $offenseExists = DB::table('tbl_offense')
                            ->where('offense_id', $offenseId)
                            ->whereNull('deleted_at')
                            ->exists();

                        if (!$offenseExists) {
                            continue;
                        }

                        // Handle "not_assigned" sanction
                        if ($sanctionId === 'not_assigned' || empty($sanctionId)) {
                            $sanctionId = DB::table('tbl_sanction')
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
                        $sanctionExists = DB::table('tbl_sanction')->where('sanction_id', $sanctionId)->exists();
                        if (!$sanctionExists) {
                            continue;
                        }

                        // Create violation record for this offense-sanction pair
                        try {
                            $newViolation = DB::table('tbl_violation_record')->insertGetId([
                                'violator_id' => $currentViolatorId,
                                'prefect_id' => $adviser_id, // Using prefect_id field for adviser
                                'offense_id' => $offenseId,
                                'sanction_id' => $sanctionId,
                                'violation_incident' => $incident,
                                'violation_date' => $date,
                                'violation_time' => $time,
                                'status' => 'pending',
                                'handled_by' => 'adviser',
                                'witnesses' => $witnesses,
                                'evidence_description' => $evidence_description,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            $savedCount++;

                            // Get offense and sanction names for message
                            $offenseRecord = DB::table('tbl_offense')->where('offense_id', $offenseId)->first();
                            $sanctionRecord = DB::table('tbl_sanction')->where('sanction_id', $sanctionId)->first();

                            $offenseName = $offenseRecord ? $offenseRecord->offense_type : 'Unknown Offense';
                            $sanctionName = $sanctionRecord ? $sanctionRecord->sanction_consequences : 'Unknown Sanction';

                            $messages[] = "✅ {$violatorName} - {$offenseName} ({$sanctionName})";

                            // Store violation info for SMS notification
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

                        } catch (\Exception $e) {
                            \Log::error("Failed to create violation {$violationIndex} for offense {$offenseIndex}: {$e->getMessage()}");
                        }
                    }
                }
            }

            DB::commit();

            // Send SMS notifications after successful commit
            if ($savedCount > 0 && !empty($violationsForSMS)) {
                \Log::info("Sending GROUPED SMS notifications from adviser", [
                    'total_violations' => count($violationsForSMS)
                ]);
                $this->sendViolationNotifications($violationsForSMS);
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
            \Log::error('Adviser violations submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Get Prefect of Discipline phone number
 */
private function getPrefectPhoneNumber()
{
    try {
        // Get specifically "Prefect of Discipline" from the correct table
        $prefect = DB::table('tbl_prefect_of_discipline')
            ->where('status', 'active')
            ->select('prefect_contactinfo', 'prefect_fname', 'prefect_lname')
            ->first();

        if (!$prefect) {
            Log::warning("Prefect of Discipline not found in database");
            return null;
        }

        Log::info("Using Prefect of Discipline phone number:", [
            'prefect_name' => $prefect->prefect_fname . ' ' . $prefect->prefect_lname,
            'phone' => $prefect->prefect_contactinfo
        ]);

        return $prefect->prefect_contactinfo;

    } catch (\Exception $e) {
        Log::error('Error getting Prefect of Discipline phone number: ' . $e->getMessage());
        return null;
    }
}

/**
 * Send SMS to Prefect about referred violations - PLAIN TEXT VERSION
 */
private function sendSMSToPrefect($prefectPhone, $incidentData)
{
    try {
        Log::info("Preparing SMS for Prefect about referred violation", [
            'incident' => $incidentData['incident'],
            'violators_count' => count($incidentData['violators'] ?? [])
        ]);

        // Build detailed plain text message for Prefect
        $message = "VIOLATION REFERRAL NOTIFICATION\n\n";

        $message .= "Incident: " . ($incidentData['incident'] ?? 'No description provided') . "\n";
        $message .= "Date: " . date('F j, Y', strtotime($incidentData['date'] ?? now())) . "\n";
        $message .= "Time: " . date('g:i A', strtotime($incidentData['time'] ?? now())) . "\n";
        $message .= "Students Involved: " . count($incidentData['violators'] ?? []) . "\n\n";

        // List students involved
        if (!empty($incidentData['violators'])) {
            $message .= "Students:\n";
            foreach ($incidentData['violators'] as $index => $student) {
                $message .= ($index + 1) . ". " . $student . "\n";
            }
            $message .= "\n";
        }

        $message .= "Location: Tagoloan Senior High School\n";
        $message .= "Status: Referred for disciplinary action\n";
        $message .= "Action Required: Please review and take appropriate disciplinary measures\n\n";

        $message .= "Please log in to the system for complete details and documentation.";

        Log::info("Sending SMS to Prefect of Discipline", [
            'phone' => $prefectPhone,
            'message_length' => strlen($message),
            'violators_count' => count($incidentData['violators'] ?? [])
        ]);

        // Send SMS
        $smsResult = $this->smsService->sendSMS($prefectPhone, $message);

        if ($smsResult['success']) {
            Log::info("✅ SMS sent to Prefect of Discipline: " . $prefectPhone);
        } else {
            Log::error("❌ Failed to send SMS to Prefect of Discipline: " . $prefectPhone, [
                'error' => $smsResult['error'] ?? 'Unknown error'
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error sending SMS to Prefect of Discipline: ' . $e->getMessage());
    }
}

/**
 * Send SMS notifications to Prefect of Discipline for referred violations
 */
private function sendPrefectReferralNotifications($violations)
{
    try {
        Log::info("Starting SMS notifications to Prefect of Discipline", [
            'total_violations' => count($violations)
        ]);

        // Get Prefect of Discipline phone number
        $prefectPhone = $this->getPrefectPhoneNumber();

        if (!$prefectPhone) {
            Log::warning("Prefect of Discipline phone number not found");
            return;
        }

        Log::info("Prefect phone confirmed: " . $prefectPhone);

        // Group violations by incident to avoid duplicate SMS
        $incidentGroups = [];

        foreach ($violations as $violation) {
            $incidentKey = $violation['incident'] . '|' . $violation['date'] . '|' . $violation['time'];

            if (!isset($incidentGroups[$incidentKey])) {
                $incidentGroups[$incidentKey] = [
                    'incident' => $violation['incident'],
                    'date' => $violation['date'],
                    'time' => $violation['time'],
                    'violators' => []
                ];
            }

            // Add unique violators
            if (!in_array($violation['violator_name'], $incidentGroups[$incidentKey]['violators'])) {
                $incidentGroups[$incidentKey]['violators'][] = $violation['violator_name'];
            }
        }

        Log::info("Violations grouped into " . count($incidentGroups) . " incident(s)");

        // Send ONE SMS per incident group to Prefect of Discipline
        $smsSentCount = 0;
        foreach ($incidentGroups as $incidentKey => $incidentData) {
            Log::info("Sending SMS for incident:", [
                'incident' => $incidentData['incident'],
                'violators_count' => count($incidentData['violators']),
                'date' => $incidentData['date'],
                'time' => $incidentData['time']
            ]);

            $this->sendSMSToPrefect($prefectPhone, $incidentData);
            $smsSentCount++;

            Log::info("SMS queued for incident: " . $incidentData['incident']);
        }

        Log::info("Prefect referral SMS notifications completed", [
            'total_sms_sent' => $smsSentCount,
            'total_incidents' => count($incidentGroups)
        ]);

    } catch (\Exception $e) {
        Log::error('Prefect referral SMS notification failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
    }
}



       /**
     * Send SMS to parent of violator - PLAIN TEXT VERSION (Same as prefect)
     */
    private function sendSMSToParent($violatorId, $incidentData)
    {
        try {
            Log::info("ADVISER DEBUG: Preparing SMS for violator: {$violatorId}", [
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
            Log::info("ADVISER DEBUG PHONE CHECK:", [
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

            Log::info("ADVISER DEBUG: Parent phone found: {$parentPhone}");

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

            // Limit message length
            if (strlen($message) > 480) {
                $message = substr($message, 0, 477) . '...';
            }

            Log::info("ADVISER DEBUG: Sending plain text SMS message", [
                'message_length' => strlen($message),
                'offense_counts' => $offenseCounts,
                'total_offenses' => count($incidentData['offenses'])
            ]);

            // Send SMS
            $smsResult = $this->smsService->sendSMS($parentPhone, $message);

            if ($smsResult['success']) {
                Log::info("✅ ADVISER PLAIN TEXT SMS sent to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentPhone,
                    'total_offenses' => count($incidentData['offenses']),
                    'offense_types' => array_keys($offenseCounts)
                ]);
            } else {
                Log::error("❌ ADVISER Failed to send plain text SMS to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentPhone,
                    'error' => $smsResult['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('ADVISER Error in sendSMSToParent: ' . $e->getMessage());
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
            Log::info("ADVISER Starting SMS notifications", ['total_violations' => count($violations)]);

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

            Log::info("ADVISER Grouped violations for SMS", [
                'unique_violators' => count($violatorIncidents),
                'total_incidents' => array_sum(array_map('count', $violatorIncidents))
            ]);

            // Send ONE SMS per violator per incident
            $smsSentCount = 0;
            foreach ($violatorIncidents as $violatorId => $incidents) {
                foreach ($incidents as $incidentKey => $incidentData) {
                    $this->sendSMSToParent($violatorId, $incidentData);
                    $smsSentCount++;

                    Log::info("ADVISER SMS queued for incident", [
                        'violator_id' => $violatorId,
                        'violator_name' => $incidentData['violator_name'],
                        'incident' => $incidentData['incident'],
                        'offenses_count' => count($incidentData['offenses'])
                    ]);
                }
            }

            Log::info("ADVISER SMS notifications completed", ['total_sms_sent' => $smsSentCount]);

        } catch (\Exception $e) {
            Log::error('ADVISER SMS notification failed: ' . $e->getMessage());
        }
    }

        public function storeMultipleAppointments(Request $request)
    {
        \Log::info('=== ADVISER storeMultipleAppointments START ===');
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
            \Log::error('Adviser appointment validation failed:', $validator->errors()->toArray());
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

            \Log::info('Adviser Initial - Violation IDs:', $violationIds);
            \Log::info('Adviser Initial - Group Keys:', $groupKeys);

            // Check if at least one violation or group is selected
            if (empty($violationIds) && empty($groupKeys)) {
                \Log::warning('Adviser: No violations or groups selected');
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one violation or group'
                ], 422);
            }

            // Process individual violations first
            if (!empty($violationIds)) {
                \Log::info('=== ADVISER PROCESSING INDIVIDUAL VIOLATIONS ===');
                foreach ($violationIds as $violationId) {
                    // Skip if already processed
                    if (in_array($violationId, $processedViolationIds)) {
                        \Log::info("❌ ADVISER SKIPPING - Already processed individual violation: $violationId");
                        continue;
                    }

                    // Check if appointment already exists for this violation
                    $existingAppointment = ViolationAppointment::where('violation_id', $violationId)
                        ->where('violation_app_status', 'Scheduled')
                        ->first();

                    if ($existingAppointment) {
                        \Log::warning("❌ ADVISER SKIPPING - Appointment already exists for violation: $violationId");
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
                            'handled_by' => 'adviser',
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

                        \Log::info("✅ ADVISER CREATED - Appointment for individual violation: $violationId");
                    } else {
                        \Log::warning("❌ ADVISER VIOLATION NOT FOUND: $violationId");
                    }
                }
            }

            // Process group violations - but skip violations already processed from individual selection
            if (!empty($groupKeys)) {
                \Log::info('=== ADVISER PROCESSING GROUP VIOLATIONS ===');
                foreach ($groupKeys as $groupKey) {
                    \Log::info("Adviser Processing group key: $groupKey");

                    // Parse the group key to match your grouping logic
                    $groupParts = explode('|', $groupKey);

                    if (count($groupParts) >= 5) {
                        $incident = $groupParts[0];
                        $offenseType = $groupParts[1];
                        $sanction = $groupParts[2];
                        $date = $groupParts[3];
                        $timeGroup = $groupParts[4];

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

                        \Log::info("Adviser Found " . $groupViolations->count() . " violations in group");

                        foreach ($groupViolations as $violation) {
                            // Skip if this violation was already processed from individual selection
                            if (in_array($violation->violation_id, $processedViolationIds)) {
                                \Log::info("❌ ADVISER SKIPPING - Already processed group violation: " . $violation->violation_id);
                                continue;
                            }

                            // Check if appointment already exists for this violation
                            $existingAppointment = ViolationAppointment::where('violation_id', $violation->violation_id)
                                ->where('violation_app_status', 'Scheduled')
                                ->first();

                            if ($existingAppointment) {
                                \Log::warning("❌ ADVISER SKIPPING - Appointment already exists for group violation: " . $violation->violation_id);
                                $processedViolationIds[] = $violation->violation_id;
                                continue;
                            }

                            $appointment = ViolationAppointment::create([
                                'violation_id' => $violation->violation_id,
                                'violation_app_date' => $request->schedule_date,
                                'violation_app_time' => $request->schedule_time,
                                'violation_app_notes' => $request->violation_app_notes,
                                'violation_app_status' => 'Scheduled',
                                'handled_by' => 'adviser',
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

                            \Log::info("✅ ADVISER CREATED - Appointment for group violation: " . $violation->violation_id);
                        }
                    } else {
                        \Log::warning("❌ ADVISER INVALID GROUP KEY FORMAT: $groupKey");
                    }
                }
            }

            DB::commit();

            \Log::info("=== ADVISER FINAL RESULTS ===");
            \Log::info("Total appointments created: " . count($createdAppointments));

            if (empty($createdAppointments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No appointments were created. Please check if the selected violations exist or if appointments already exist.'
                ], 400);
            }

            // Send SMS notifications after successful commit
            if (!empty($appointmentsForSMS)) {
                \Log::info("Adviser Sending SMS notifications for appointments", [
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
            \Log::error('Adviser Error creating appointments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating appointments: ' . $e->getMessage()
            ], 500);
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

                    // Find ALL violations - NO STATUS RESTRICTIONS
                    $groupViolations = ViolationRecord::with(['offense', 'sanction'])
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
                            \Log::info("Added violation ID: " . $violation->violation_id . " with status: " . $violation->status);
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
                'message' => 'No valid violations found to process.'
            ], 400);
        }

        foreach ($allViolationIds as $violationId) {
            \Log::info("Processing violation ID: $violationId");

            // Check if violation exists - NO STATUS RESTRICTIONS
            $violation = ViolationRecord::with([
                'student.parent',
                'student.adviser',
                'offense',
                'sanction',
                'prefect'
            ])
            ->where('violation_id', $violationId)
            ->first();

            if (!$violation) {
                \Log::warning("Violation not found: $violationId");
                continue;
            }

            \Log::info("Violation found with status: " . $violation->status);

            // ALWAYS CREATE NEW ANECDOTAL RECORD - regardless of existing anecdotal or violation status
            \Log::info("Creating new anecdotal record for violation: $violationId");

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

            \Log::info("✅ Created new anecdotal record for violation $violationId", [
                'anecdotal_id' => $anecdotal->violation_anec_id,
                'violation_id' => $violationId,
                'violation_status' => $violation->status,
                'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                'anecdotal_status' => 'completed'
            ]);

            // Optional: You can update the violation status if you want
            // This is commented out since you want no restrictions
            // if ($violation->status !== 'completed') {
            //     $violation->update(['status' => 'completed']);
            //     \Log::info("Updated violation status to completed for ID: $violationId");
            // }
        }

        DB::commit();

        $totalProcessed = count($createdAnecdotals);
        \Log::info("✅ Total created: $totalProcessed anecdotal records");

        if ($totalProcessed === 0) {
            \Log::error('No anecdotal records were created.');
            return response()->json([
                'success' => false,
                'message' => 'No anecdotal records were created.'
            ], 400);
        }

        $message = $totalProcessed . ' anecdotal record(s) created successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'created' => $createdAnecdotals,
                'total_processed' => $totalProcessed,
                'violation_ids' => $allViolationIds // Include IDs for PDF generation
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
'status' => 'required|string'
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
    // Logged-in adviser ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification counts
    $notificationCounts = $this->getNotificationCounts();

    try {
        // Only load students under this adviser's advisory
        $students = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('adviser_id', $adviserId)   // <-- FILTER ADDED
            ->orderBy('student_lname')
            ->orderBy('student_fname')
            ->get();

        // Load violation offenses
        $offenses = Offense::where('category', 'violation')
            ->whereNull('deleted_at')
            ->orderBy('offense_type')
            ->get();

        return view('adviser.create-violation', array_merge(
            compact('students', 'offenses'),
            $notificationCounts
        ));

    } catch (\Exception $e) {
        Log::error('Error loading create violation form: ' . $e->getMessage());
        return redirect()->route('adviser.violation')
            ->with('error', 'Error loading violation form: ' . $e->getMessage());
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

            // Convert all sanction consequences to uppercase for consistency and filter only allowed sanctions
            $filteredSanctions = $sanctions->map(function ($sanction) {
                $sanction->sanction_consequences = strtoupper($sanction->sanction_consequences);
                return $sanction;
            })->filter(function ($sanction) {
                // Only keep Verbal Warning and Parent Notification
                $consequences = $sanction->sanction_consequences;
                return str_contains($consequences, 'VERBAL WARNING') ||
                       str_contains($consequences, 'PARENT/GUARDIAN NOTIFICATION');
            });

            // Add "NOT ASSIGNED" sanction as the first option
            $notAssignedSanction = [
                (object) [
                    'sanction_id' => 'not_assigned',
                    'sanction_consequences' => 'NOT ASSIGNED',
                    'sanction_description' => 'Default sanction for complaints that have not been assigned a specific consequence yet.'
                ]
            ];

            // Merge "NOT ASSIGNED" with the filtered sanctions
            $allSanctions = array_merge($notAssignedSanction, $filteredSanctions->toArray());

            Log::info('Returning ' . count($allSanctions) . ' filtered sanctions for adviser UI');

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

    /**
 * Refer violations to Prefect with SMS notification
 */
public function referToPrefect(Request $request)
{
    \Log::info('=== REFER TO PREFECT START ===');
    \Log::info('Request data:', $request->all());

    $validator = Validator::make($request->all(), [
        'complaint_ids' => 'sometimes|array',
        'complaint_ids.*' => 'exists:tbl_violation_record,violation_id',
        'group_keys' => 'sometimes|array',
        'group_keys.*' => 'string'
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

        $complaintIds = $request->complaint_ids ?? [];
        $groupKeys = $request->group_keys ?? [];
        $processedCount = 0;
        $referredViolationsForSMS = []; // Array to store violations for SMS notification

        \Log::info('Processing referral to Prefect:', [
            'individual_violations' => count($complaintIds),
            'groups' => count($groupKeys)
        ]);

        // Define statuses that can be referred to prefect
        $allowedStatuses = ['pending', 'under_review', 'active', 'rescheduled', 'needs_review'];

        // Process individual violations
        foreach ($complaintIds as $violationId) {
            $violation = ViolationRecord::with(['student', 'offense'])
                ->where('violation_id', $violationId)
                ->whereIn('status', $allowedStatuses)
                ->first();

            if ($violation) {
                $oldStatus = $violation->status;
                // Update status to indicate referred to prefect
                $violation->update([
                    'status' => 'referred_to_prefect',
                    'handled_by' => 'prefect',
                    'escalated_at' => now(),
                    'updated_at' => now()
                ]);
                $processedCount++;

                // Store violation info for SMS notification
                $referredViolationsForSMS[] = [
                    'violation_id' => $violation->violation_id,
                    'violator_id' => $violation->violator_id,
                    'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                    'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                    'incident' => $violation->violation_incident,
                    'date' => $violation->violation_date,
                    'time' => $violation->violation_time
                ];

                \Log::info("Referred individual violation to Prefect: $violationId", [
                    'violation_id' => $violationId,
                    'old_status' => $oldStatus,
                    'new_status' => 'referred_to_prefect',
                    'escalated_at' => now()->toDateTimeString()
                ]);
            } else {
                \Log::warning("Violation not found or not in allowed status for referral: $violationId");
            }
        }

        // Process group violations
        foreach ($groupKeys as $groupKey) {
            \Log::info("Processing group referral: $groupKey");

            $groupParts = explode('|', $groupKey);

            if (count($groupParts) >= 5) {
                $incident = $groupParts[0];
                $offenseType = $groupParts[1];
                $sanction = $groupParts[2];
                $date = $groupParts[3];
                $timeGroup = $groupParts[4];

                // Find violations that match this group
                $groupViolations = ViolationRecord::with(['student', 'offense'])
                    ->whereIn('status', $allowedStatuses)
                    ->where('violation_incident', $incident)
                    ->where('violation_date', $date)
                    ->whereHas('offense', function($query) use ($offenseType) {
                        $query->where('offense_type', $offenseType);
                    })
                    ->whereHas('sanction', function($query) use ($sanction) {
                        $query->where('sanction_consequences', $sanction);
                    })
                    ->get();

                \Log::info("Found " . $groupViolations->count() . " violations in group for referral");

                foreach ($groupViolations as $violation) {
                    $oldStatus = $violation->status;
                    $violation->update([
                        'status' => 'referred_to_prefect',
                        'handled_by' => 'prefect',
                        'escalated_at' => now(),
                        'updated_at' => now()
                    ]);
                    $processedCount++;

                    // Store violation info for SMS notification
                    $referredViolationsForSMS[] = [
                        'violation_id' => $violation->violation_id,
                        'violator_id' => $violation->violator_id,
                        'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                        'incident' => $violation->violation_incident,
                        'date' => $violation->violation_date,
                        'time' => $violation->violation_time
                    ];

                    \Log::info("Referred group violation to Prefect: " . $violation->violation_id, [
                        'violation_id' => $violation->violation_id,
                        'old_status' => $oldStatus,
                        'new_status' => 'referred_to_prefect',
                        'escalated_at' => now()->toDateTimeString()
                    ]);
                }
            } else {
                \Log::warning("Invalid group key format: $groupKey");
            }
        }

        DB::commit();

        \Log::info("Referral to Prefect completed", [
            'processed_count' => $processedCount,
            'sms_notifications_count' => count($referredViolationsForSMS)
        ]);

        // Send SMS notifications to Prefect after successful commit
        if ($processedCount > 0 && !empty($referredViolationsForSMS)) {
            \Log::info("Sending SMS notifications to Prefect for referred violations", [
                'total_violations' => count($referredViolationsForSMS)
            ]);
            $this->sendPrefectReferralNotifications($referredViolationsForSMS);
        }

        if ($processedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No violations were referred. Please check if the selected violations exist and are in a referrable status (pending, under_review, active, rescheduled, or needs_review).'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $processedCount . ' violation(s) referred to Prefect successfully',
            'processed_count' => $processedCount
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error referring violations to Prefect: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error referring violations to Prefect: ' . $e->getMessage()
        ], 500);
    }
}
    public function getOffenseHistory(Request $request)
    {
        try {
            $studentIds = $request->input('student_ids');
            $offenseId = $request->input('offense_id');

            // Validate input
            if (!$studentIds || !$offenseId) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            // Ensure studentIds is an array
            if (!is_array($studentIds)) {
                $studentIds = [$studentIds];
            }

            $history = [];

            foreach ($studentIds as $studentId) {
                // Count violations from tbl_violation_record (student as violator)
                $violationCount = \DB::table('tbl_violation_record')
                    ->where('violator_id', $studentId)
                    ->where('offense_id', $offenseId)
                    ->count();

                $history[$studentId] = [
                    'count' => $violationCount,
                    'records' => []
                ];

                // If you want to include detailed records, you can uncomment and modify this:
                /*
                $records = \DB::table('tbl_violation_record')
                    ->where('violator_id', $studentId)
                    ->where('offense_id', $offenseId)
                    ->select('violation_id', 'violation_date', 'violation_time', 'status', 'sanction_id')
                    ->get();

                $history[$studentId]['records'] = $records;
                */
            }

            return response()->json($history);

        } catch (\Exception $e) {
            \Log::error('Error in getOffenseHistory: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
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

            $pdf = PDF::loadView('adviser.pdf.anecdotal', $data);

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
     * Send SMS notifications for appointments to parents
     */
    private function sendAppointmentNotifications($appointments)
    {
        try {
            Log::info("ADVISER Starting SMS notifications for appointments", ['total_appointments' => count($appointments)]);

            // Group appointments by violator to avoid duplicate SMS
            $violatorAppointments = [];

            foreach ($appointments as $appointment) {
                $violatorId = $appointment['violator_id'];

                if (!isset($violatorAppointments[$violatorId])) {
                    $violatorAppointments[$violatorId] = [];
                }

                $violatorAppointments[$violatorId][] = $appointment;
            }

            Log::info("ADVISER Grouped appointments for SMS", [
                'unique_violators' => count($violatorAppointments)
            ]);

            // Send ONE SMS per violator with all their appointments
            $smsSentCount = 0;
            foreach ($violatorAppointments as $violatorId => $appointmentList) {
                $this->sendAppointmentSMSToParent($violatorId, $appointmentList);
                $smsSentCount++;

                Log::info("ADVISER SMS queued for appointment notification", [
                    'violator_id' => $violatorId,
                    'appointments_count' => count($appointmentList)
                ]);
            }

            Log::info("ADVISER Appointment SMS notifications completed", ['total_sms_sent' => $smsSentCount]);

        } catch (\Exception $e) {
            Log::error('ADVISER Appointment SMS notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS to parent about appointment - PLAIN TEXT VERSION
     */
    private function sendAppointmentSMSToParent($violatorId, $appointments)
    {
        try {
            Log::info("ADVISER DEBUG: Preparing APPOINTMENT SMS for violator: {$violatorId}", [
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
                Log::warning("ADVISER Student not found for appointment SMS notification: {$violatorId}");
                return;
            }

            // DEBUG: Check if this is YOUR number
            $isMyNumber = in_array($student->parent_contactinfo, ['09513738659', '639513738659']);
            Log::info("ADVISER DEBUG APPOINTMENT PHONE CHECK:", [
                'student_name' => $student->student_fname . ' ' . $student->student_lname,
                'parent_name' => $student->parent_fname . ' ' . $student->parent_lname,
                'parent_contact' => $student->parent_contactinfo,
                'is_my_number' => $isMyNumber
            ]);

            $parentPhone = $student->parent_contactinfo ?? null;

            if (!$parentPhone) {
                Log::warning("ADVISER No parent contact found for student: {$student->student_fname} {$student->student_lname}");
                return;
            }

            Log::info("ADVISER DEBUG: Parent phone found for appointment: {$parentPhone}");

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

            Log::info("ADVISER DEBUG: Sending appointment SMS message", [
                'message_length' => strlen($message),
                'appointments_count' => count($appointments),
                'unique_offenses' => count($offensesList)
            ]);

            // Send SMS
            $smsResult = $this->smsService->sendSMS($parentPhone, $message);

            if ($smsResult['success']) {
                Log::info("✅ ADVISER APPOINTMENT SMS sent to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentPhone,
                    'appointments_count' => count($appointments),
                    'offenses' => $offensesList
                ]);
            } else {
                Log::error("❌ ADVISER Failed to send appointment SMS to parent of {$studentName}", [
                    'parent_name' => $parentName,
                    'phone' => $parentPhone,
                    'error' => $smsResult['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('ADVISER Error in sendAppointmentSMSToParent: ' . $e->getMessage());
        }
    }
}
