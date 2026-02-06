<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Services\PhilSMSService;
use Barryvdh\DomPDF\Facade\Pdf;

class AViolationController extends Controller
{

public function referToPrefect(Request $request)
{
    try {
        DB::beginTransaction();

        $individualUpdated = 0;
        $groupUpdated = 0;
        $referredViolations = [];

        // Update individual violations
        if ($request->has('violation_ids')) {
            $violationIds = $request->input('violation_ids');

            // Get current statuses before update for logging
            $currentViolations = ViolationRecord::whereIn('violation_id', $violationIds)
                ->select('violation_id', 'status', 'handled_by')
                ->get();

            // Update both handled_by AND status
            $updated = ViolationRecord::whereIn('violation_id', $violationIds)
                ->update([
                    'handled_by' => 'prefect',
                    'status' => 'pending',
                    'updated_at' => now()
                ]);

            $individualUpdated = $updated;

            // Track referred violations for logging
            foreach ($currentViolations as $violation) {
                $referredViolations[] = [
                    'violation_id' => $violation->violation_id,
                    'old_status' => $violation->status,
                    'old_handled_by' => $violation->handled_by,
                    'new_status' => 'pending',
                    'new_handled_by' => 'prefect'
                ];
            }
        }

        // Update group violations (if using groups)
        if ($request->has('group_keys')) {
            $groupKeys = $request->input('group_keys');

            foreach ($groupKeys as $groupKey) {
                // Get current statuses before update for logging
                $currentGroupViolations = ViolationRecord::where('group_key', $groupKey)
                    ->select('violation_id', 'status', 'handled_by')
                    ->get();

                // Update both handled_by AND status for group violations
                $updated = ViolationRecord::where('group_key', $groupKey)
                    ->update([
                        'handled_by' => 'prefect',
                        'status' => 'pending',
                        'updated_at' => now()
                    ]);

                $groupUpdated += $updated;

                // Track referred violations for logging
                foreach ($currentGroupViolations as $violation) {
                    $referredViolations[] = [
                        'violation_id' => $violation->violation_id,
                        'old_status' => $violation->status,
                        'old_handled_by' => $violation->handled_by,
                        'new_status' => 'pending',
                        'new_handled_by' => 'prefect'
                    ];
                }
            }
        }

        DB::commit();

        $totalUpdated = $individualUpdated + $groupUpdated;

        // Log the referral activity
        Log::info('Violations referred to prefect', [
            'total_updated' => $totalUpdated,
            'individual_updated' => $individualUpdated,
            'group_updated' => $groupUpdated,
            'referred_violations' => $referredViolations,
            'user_id' => auth()->id() ?? 'unknown',
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully referred {$totalUpdated} violation(s) to prefect with status updated to 'pending'.",
            'individual_updated' => $individualUpdated,
            'group_updated' => $groupUpdated,
            'total_updated' => $totalUpdated
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to refer violations to prefect: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'error_trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to refer violations: ' . $e->getMessage()
        ], 500);
    }
}

    // Updated notification data helper method without complaints
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

        $newParentsCount = 0;
        $newComplaintsCount = 0; // Removed complaints

        $notificationCount = $newViolationsCount + $newStudentsCount + $newParentsCount + $newComplaintsCount;

        return [
            'notificationCount' => $notificationCount,
            'newViolationsCount' => $newViolationsCount,
            'newStudentsCount' => $newStudentsCount,
            'newParentsCount' => $newParentsCount,
            'newComplaintsCount' => $newComplaintsCount, // Kept for compatibility
            'newViolations' => $newViolations,
            'newStudents' => $newStudents,
            'newComplaints' => collect([]) // Empty collection for compatibility
        ];
    }
public function index(Request $request)
{
    // Get the logged-in adviser's ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    $viewType = $request->get('view', 'individual');
    $groupKey = $request->get('group');

    // Initialize variables to avoid undefined errors
    $violations = collect();
    $byGroupViolations = collect();

    // Get the actual dates from your violation records - ONLY ADVISER HANDLED, exclude inactive
    // Filter by adviser's students - FIXED: Using violator_id instead of student_id
    $mostRecentViolationDate = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->where('handled_by', 'adviser')
        ->where('tbl_violation_record.status', '!=', 'inactive')
        ->where('tbl_student.adviser_id', $adviserId)
        ->max('violation_date');

    $earliestViolationDate = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->where('handled_by', 'adviser')
        ->where('tbl_violation_record.status', '!=', 'inactive')
        ->where('tbl_student.adviser_id', $adviserId)
        ->min('violation_date');

    // Use the most recent violation date for calculations, or today if no records exist
    $referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

    // Calculate date ranges based on the actual violation dates
    $today = $referenceDate->copy();
    $startOfWeek = $referenceDate->copy()->startOfWeek();
    $endOfWeek = $referenceDate->copy()->endOfWeek();
    $startOfMonth = $referenceDate->copy()->startOfMonth();
    $endOfMonth = $referenceDate->copy()->endOfMonth();

    // Summary Counts - Count ALL violations except inactive status
    // Filter by adviser's students - FIXED: Using violator_id instead of student_id
    $dailyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereDate('violation_date', $today)
        ->where('handled_by', 'adviser')
        ->where('tbl_violation_record.status', '!=', 'inactive')
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    $weeklyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
        ->where('handled_by', 'adviser')
        ->where('tbl_violation_record.status', '!=', 'inactive')
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    $monthlyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
        ->where('handled_by', 'adviser')
        ->where('tbl_violation_record.status', '!=', 'inactive')
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    // Handle different view types - EXCLUDE INACTIVE STATUS
    if ($viewType == 'group') {
        // GROUPED VIOLATIONS LOGIC - Exclude inactive status
        // Filter by adviser's students
        $groupedViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
            ->where('handled_by', 'adviser')
            ->where('status', '!=', 'inactive')
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function ($violation) {
                // Group by incident, offense type, and created_at (same day)
                // Format created_at to date only to group by same day
                $createdDate = Carbon::parse($violation->created_at)->format('Y-m-d');

                return $violation->violation_incident . '|' .
                    $violation->offense->offense_type . '|' .
                    $createdDate;
            });

        $byGroupViolations = $groupedViolations->map(function ($group, $key) {
            // Get the most recent violation in the group
            $mostRecentViolation = $group->sortByDesc('created_at')->first();

            // Calculate time range for the group
            $earliestTime = $group->min('violation_time');
            $latestTime = $group->max('violation_time');
            $hasMultipleTimes = $earliestTime != $latestTime;

            // For display purposes, show time range if multiple times exist
            $displayTime = $hasMultipleTimes ?
                Carbon::parse($earliestTime)->format('h:i A') . ' - ' . Carbon::parse($latestTime)->format('h:i A') :
                Carbon::parse($earliestTime)->format('h:i A');

            // Get unique students - deduplicate by student_id to avoid duplicate names
            $uniqueStudents = $group->unique('student.student_id')->pluck('student');

            // Get student names without duplicates
            $studentNames = $uniqueStudents->map(function($student) {
                return $student->student_fname . ' ' . $student->student_lname;
            })->unique()->values();

            // Count unique students
            $uniqueStudentCount = $studentNames->count();

            return (object)[
                'group_key' => $key,
                'violation_incident' => $mostRecentViolation->violation_incident,
                'offense_type' => $mostRecentViolation->offense->offense_type,
                'sanction_consequences' => $mostRecentViolation->sanction->sanction_consequences,
                'violation_date' => $mostRecentViolation->violation_date,
                'violation_time' => $displayTime,
                'original_time' => $hasMultipleTimes ? $earliestTime : $mostRecentViolation->violation_time,
                'status' => $mostRecentViolation->status,
                'sanction_start_at' => $mostRecentViolation->sanction_start_at,
                'sanction_end_at' => $mostRecentViolation->sanction_end_at,
                'sanction_status' => $mostRecentViolation->sanction_status,
                'updated_at' => $mostRecentViolation->updated_at,
                'latest_created_at' => $mostRecentViolation->created_at,
                'latest_updated_at' => $mostRecentViolation->updated_at,
                'student_count' => $uniqueStudentCount,
                'students' => $uniqueStudents,
                'student_names' => $studentNames->implode(', '),
                'violation_ids' => $group->pluck('violation_id'),
                'violations_data' => $group->map(function ($violation) {
                    return [
                        'violation_id' => $violation->violation_id,
                        'student_id' => $violation->student->student_id,
                        'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_id' => $violation->offense_id,
                        'sanction_id' => $violation->sanction_id,
                        'violation_time' => $violation->violation_time,
                        'created_at' => $violation->created_at
                    ];
                })->sortBy('created_at')->values(),
                'earliest_time' => $earliestTime,
                'latest_time' => $latestTime,
                'has_multiple_times' => $hasMultipleTimes,
                'all_times' => $group->pluck('violation_time')->unique()->values()
            ];
        })
            ->sortByDesc('latest_created_at')
            ->sortByDesc('latest_updated_at')
            ->values();

        // Manual pagination for grouped violations
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginatedByGroupViolations = new \Illuminate\Pagination\LengthAwarePaginator(
            $byGroupViolations->forPage($page, $perPage),
            $byGroupViolations->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => array_merge($request->query(), ['view' => 'group'])
            ]
        );

        $byGroupViolations = $paginatedByGroupViolations;

    } elseif ($viewType == 'group_per_offense') {
        // Group by offense type and created_at (same day) - Filter by adviser's students
        $groupedViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
            ->where('handled_by', 'adviser')
            ->where('status', '!=', 'inactive')
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function ($violation) {
                // Group by offense type AND created_at (same day)
                $createdDate = Carbon::parse($violation->created_at)->format('Y-m-d');

                return $violation->offense->offense_type . '|' . $createdDate;
            });

        $byGroupViolations = $groupedViolations->map(function ($group, $key) {
            // Split the key to get offense type and date
            list($offenseType, $createdDate) = explode('|', $key);

            // Get the most recent violation in the group
            $mostRecentViolation = $group->sortByDesc('created_at')->first();

            // Get the most common incident for this offense type on this day
            $mostCommonIncident = $group->countBy('violation_incident')->sortDesc()->keys()->first();

            // Get date range for this group
            $earliestDate = $group->min('violation_date');
            $latestDate = $group->max('violation_date');
            $hasMultipleDates = $earliestDate != $latestDate;

            // For display purposes, show date range if multiple dates exist
            $displayDate = $hasMultipleDates ?
                Carbon::parse($earliestDate)->format('M d, Y') . ' - ' . Carbon::parse($latestDate)->format('M d, Y') :
                Carbon::parse($earliestDate)->format('M d, Y');

            // Get unique sanctions in this group
            $uniqueSanctions = $group->pluck('sanction.sanction_consequences')->unique()->implode(', ');

            // Get unique students - deduplicate by student_id to avoid duplicate names
            $uniqueStudents = $group->unique('student.student_id')->pluck('student');

            // Get student names without duplicates
            $studentNames = $uniqueStudents->map(function($student) {
                return $student->student_fname . ' ' . $student->student_lname;
            })->unique()->values();

            // Count unique students
            $uniqueStudentCount = $studentNames->count();

            return (object)[
                'group_key' => $key,
                'offense_type' => $offenseType,
                'created_date' => $createdDate,
                'violation_incident' => $mostCommonIncident,
                'sanction_consequences' => $uniqueSanctions,
                'violation_date' => $displayDate,
                'original_date' => $earliestDate,
                'status' => $mostRecentViolation->status,
                'sanction_start_at' => $mostRecentViolation->sanction_start_at,
                'sanction_end_at' => $mostRecentViolation->sanction_end_at,
                'sanction_status' => $mostRecentViolation->sanction_status,
                'updated_at' => $mostRecentViolation->updated_at,
                'latest_created_at' => $mostRecentViolation->created_at,
                'latest_updated_at' => $mostRecentViolation->updated_at,
                'student_count' => $uniqueStudentCount,
                'students' => $uniqueStudents,
                'student_names' => $studentNames->implode(', '),
                'violation_ids' => $group->pluck('violation_id'),
                'violations_data' => $group->map(function ($violation) {
                    return [
                        'violation_id' => $violation->violation_id,
                        'student_id' => $violation->student->student_id,
                        'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_id' => $violation->offense_id,
                        'sanction_id' => $violation->sanction_id,
                        'violation_date' => $violation->violation_date,
                        'violation_time' => $violation->violation_time,
                        'created_at' => $violation->created_at
                    ];
                })->sortBy('created_at')->values(),
                'earliest_date' => $earliestDate,
                'latest_date' => $latestDate,
                'has_multiple_dates' => $hasMultipleDates,
                'all_dates' => $group->pluck('violation_date')->unique()->values()
            ];
        })
            ->sortByDesc('latest_created_at')
            ->sortByDesc('latest_updated_at')
            ->values();

        // Manual pagination for group_per_offense view
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginatedByGroupViolations = new \Illuminate\Pagination\LengthAwarePaginator(
            $byGroupViolations->forPage($page, $perPage),
            $byGroupViolations->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => array_merge($request->query(), ['view' => 'group_per_offense'])
            ]
        );

        $byGroupViolations = $paginatedByGroupViolations;

    } elseif ($viewType == 'individual') {
        if ($groupKey) {
            // Viewing individual violations for a specific group - Filter by adviser's students
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'adviser')
                ->where('status', '!=', 'inactive')
                ->whereHas('student', function($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->whereIn('violation_id', function ($query) use ($groupKey, $adviserId) {
                    $query->select('vr.violation_id')
                        ->from('tbl_violation_record as vr')
                        ->join('tbl_offense as off', 'vr.offense_id', '=', 'off.offense_id')
                        ->join('tbl_student as s', 'vr.violator_id', '=', 's.student_id') // CHANGED: violator_id
                        ->where('vr.handled_by', 'adviser')
                        ->where('vr.status', '!=', 'inactive')
                        ->where('s.adviser_id', $adviserId)
                        ->whereRaw("CONCAT(vr.violation_incident, '|', off.offense_type, '|', DATE(vr.created_at)) = ?", [$groupKey]);
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->paginate(10)
                ->withQueryString();

            // Add violation_ids to each violation for easy access
            $violations->getCollection()->transform(function ($violation) {
                $violation->all_violation_ids = [$violation->violation_id];
                return $violation;
            });
        } else {
            // SIMPLE MERGING: Group by student, date, and time - Filter by adviser's students
            $allViolations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'adviser')
                ->where('status', '!=', 'inactive')
                ->whereHas('student', function($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->get();

            $groupedViolations = $allViolations->groupBy(function ($violation) {
                return $violation->violator_id . '|' . $violation->violation_date . '|' . $violation->violation_time;
            });

            $mergedViolations = $groupedViolations->map(function ($group) {
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
                $first->all_violation_ids = $group->pluck('violation_id');
                return $first;
            })->values();

            // Manual pagination
            $page = $request->get('page', 1);
            $perPage = 10;
            $paginatedViolations = new \Illuminate\Pagination\LengthAwarePaginator(
                $mergedViolations->forPage($page, $perPage),
                $mergedViolations->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => array_merge($request->query(), ['view' => 'individual'])
                ]
            );

            $violations = $paginatedViolations;
        }
    } elseif ($viewType == 'individual_per_offense') {
        // Individual view with one row per offense - Filter by adviser's students
        if ($groupKey) {
            // If coming from a group view, show individual violations for that group
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'adviser')
                ->where('status', '!=', 'inactive')
                ->whereHas('student', function($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->where('offense_id', $groupKey)
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->paginate(10)
                ->withQueryString();
        } else {
            // Regular individual per offense view - no merging, each violation gets its own row
            $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('handled_by', 'adviser')
                ->where('status', '!=', 'inactive')
                ->whereHas('student', function($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->orderBy('violation_date', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        // Add violation_ids to each violation for easy access
        $violations->getCollection()->transform(function ($violation) {
            $violation->all_violation_ids = [$violation->violation_id];
            return $violation;
        });
    }

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
    return view('adviser.NewAdviser.violation', array_merge($data, $notificationData));
}


public function indexAnecdotal()
{
    // Get the logged-in adviser's ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    // Filter violation appointments by adviser's students
    $vappointments = ViolationAppointment::with(['violation.student'])
        ->whereHas('violation.student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    // Filter violation anecdotal records by adviser's students
    $vanecdotals = ViolationAnecdotal::with(['violation.student'])
        ->where('status', 'completed')
        ->whereHas('violation.student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Get the actual dates from violation records for adviser's students
    // FIXED: Using violator_id instead of student_id
    $mostRecentViolationDate = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->where('tbl_student.adviser_id', $adviserId)
        ->max('violation_date');

    $earliestViolationDate = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->where('tbl_student.adviser_id', $adviserId)
        ->min('violation_date');

    // Use the most recent violation date for calculations, or today if no records exist
    $referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

    // Calculate date ranges based on the actual violation dates
    $today = $referenceDate->copy();
    $startOfWeek = $referenceDate->copy()->startOfWeek();
    $endOfWeek = $referenceDate->copy()->endOfWeek();
    $startOfMonth = $referenceDate->copy()->startOfMonth();
    $endOfMonth = $referenceDate->copy()->endOfMonth();

    // Summary Counts - Filter by adviser's students
    // FIXED: Using violator_id instead of student_id
    $dailyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereDate('violation_date', $today)
        ->where('tbl_violation_record.status', 'pending') // SPECIFY TABLE to avoid ambiguity
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    $weeklyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
        ->where('tbl_violation_record.status', 'pending') // SPECIFY TABLE to avoid ambiguity
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    $monthlyViolations = DB::table('tbl_violation_record')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
        ->where('tbl_violation_record.status', 'pending') // SPECIFY TABLE to avoid ambiguity
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    // Fetch Main Violation Records - Filter by adviser's students
    $violations = ViolationRecord::with(['student', 'offense', 'sanction'])
        ->where('status', 'pending')
        ->whereHas('student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->orderBy('violation_date', 'desc')
        ->paginate(10);

    // Fetch Violation Appointments - Filter by adviser's students
    // FIXED: Using violator_id instead of student_id
    $appointments = DB::table('tbl_violation_appointment')
        ->join('tbl_violation_record', 'tbl_violation_appointment.violation_id', '=', 'tbl_violation_record.violation_id')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->select(
            'tbl_violation_appointment.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_appointment.violation_app_status', 'Scheduled')
        ->where('tbl_student.adviser_id', $adviserId)
        ->orderBy('tbl_violation_appointment.updated_at', 'desc')
        ->orderBy('tbl_violation_appointment.created_at', 'desc')
        ->orderBy('tbl_violation_appointment.violation_app_date', 'desc')
        ->paginate(10);

    // Fetch Violation Anecdotals - Filter by adviser's students
    // FIXED: Using violator_id instead of student_id
    $anecdotals = DB::table('tbl_violation_anecdotal')
        ->join('tbl_violation_record', 'tbl_violation_anecdotal.violation_id', '=', 'tbl_violation_record.violation_id')
        ->join('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id') // CHANGED: violator_id
        ->select(
            'tbl_violation_anecdotal.*',
            'tbl_violation_record.violation_incident'
        )
        ->where('tbl_violation_anecdotal.status', 'completed')
        ->where('tbl_student.adviser_id', $adviserId)
        ->orderBy('tbl_violation_anecdotal.updated_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.created_at', 'desc')
        ->orderBy('tbl_violation_anecdotal.violation_anec_date', 'desc')
        ->paginate(10);

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
    return view('adviser.NewAdviser.violationAnecdotal', array_merge($data, $notificationData));
}

public function indexAppointment()
{
    // Get the logged-in adviser's ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    // Filter violation appointments by adviser's students
    $vappointments = ViolationAppointment::with(['violation.student'])
        ->whereHas('violation.student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
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
    return view('adviser.NewAdviser.violationAppointment', array_merge($data, $notificationData));
}
    public function store(Request $request)
{
    Log::info('=== VIOLATION STORE START ===');
    Log::info('Request method: ' . $request->method());
    Log::info('Request headers: ', $request->headers->all());
    Log::info('Request data count - violations: ' . count($request->input('violations', [])));
    Log::info('Request data count - offenses: ' . count($request->input('offenses', [])));
    Log::info('Has files: ' . ($request->hasFile('evidence_files') ? 'Yes' : 'No'));

    try {
        DB::beginTransaction();

        $prefect_id = Auth::id() ?? 1;
        $violationsData = $request->input('violations', []);
        $offensesData = $request->input('offenses', []);
        $savedCount = 0;
        $messages = [];

        Log::info('Violations submission details', [
            'count' => count($violationsData),
            'offenses_count' => count($offensesData),
            'prefect_id' => $prefect_id,
            'data_sample' => array_slice($violationsData, 0, 2)
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

        Log::info('Submission type check', [
            'is_group_submission' => $isGroupSubmission,
            'violations_count' => count($violationsData)
        ]);

        foreach ($violationsData as $violationIndex => $violation) {
            // Extract all fields
            $violator_id = $violation['violator_id'] ?? null;
            $violator_ids = $violation['violator_ids'] ?? [];
            $date = $violation['date'] ?? null;
            $time = $violation['time'] ?? null;
            $incident = $violation['incident'] ?? null;
            $complainant = $violation['complainant'] ?? null;
            $witnesses = $violation['witnesses'] ?? null;
            $evidence_description = $violation['evidence_description'] ?? null;
            $sanction_id = $violation['sanction_id'] ?? null;
            $custom_sanctions = $violation['custom_sanctions'] ?? null;

            // Handle group submissions (multiple violators)
            $violatorIds = [];
            if ($isGroupSubmission && !empty($violator_ids) && is_array($violator_ids)) {
                $violatorIds = $violator_ids;
                Log::info("Group submission with violators", ['violator_ids' => $violatorIds]);
            } else if (!empty($violator_id)) {
                $violatorIds = [$violator_id];
                Log::info("Individual submission with violator", ['violator_id' => $violator_id]);
            }

            // Skip if no violators found
            if (empty($violatorIds)) {
                Log::warning("Skipping violation {$violationIndex} - no violators found", $violation);
                continue;
            }

            // Skip if any required field is empty
            if (empty($date) || empty($time) || empty($incident)) {
                Log::warning("Skipping violation {$violationIndex} - missing required fields", $violation);
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
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'video/mp4',
                            'video/mov',
                            'video/avi',
                            'video/x-msvideo',
                            'video/x-matroska',
                            'video/webm'
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
                Log::info("Custom sanctions data for violation {$violationIndex}:", $customSanctionsData);
            }

            // Process each violator
            foreach ($violatorIds as $currentViolatorId) {
                // Validate violator exists
                $violatorExists = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                if (!$violatorExists) {
                    Log::warning("Invalid violator ID for violation {$violationIndex}", ['violator_id' => $currentViolatorId]);
                    continue;
                }

                // Get violator name for logging
                $violator = DB::table('tbl_student')->where('student_id', $currentViolatorId)->first();
                $violatorName = $violator ? "{$violator->student_fname} {$violator->student_lname}" : 'Unknown';

                Log::info("Processing violation for violator", [
                    'violator_id' => $currentViolatorId,
                    'violator_name' => $violatorName,
                    'offenses_count' => count($offensesData),
                    'incident' => $incident
                ]);

                // Process main offenses
                foreach ($offensesData as $offenseIndex => $offense) {
                    $offenseId = $offense['offense_id'] ?? null;

                    if (empty($offenseId)) {
                        Log::warning("Skipping offense {$offenseIndex} - missing offense ID");
                        continue;
                    }

                    // Validate offense exists
                    $offenseExists = DB::table('tbl_offense')
                        ->where('offense_id', $offenseId)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (!$offenseExists) {
                        Log::warning("Invalid offense ID {$offenseId} for violation {$violationIndex}");
                        continue;
                    }

                    // ============================================================
                    // AUTO-DETERMINE SANCTION BASED ON OFFENSE COUNT
                    // ============================================================

                    // Get offense count for this student
                    $offenseCount = $this->getOffenseCountForStudent($currentViolatorId, $offenseId);

                    // Determine sanction ID based on offense count
                    $finalSanctionId = $this->determineSanctionByOffenseCount($offenseId, $offenseCount);

                    Log::info("Auto-determined sanction for student", [
                        'student_id' => $currentViolatorId,
                        'offense_id' => $offenseId,
                        'offense_count' => $offenseCount,
                        'auto_sanction_id' => $finalSanctionId
                    ]);

                    // Check if this violator has a custom sanction for this specific offense
                    if (isset($customSanctionsData['customSanctions'][$offenseIndex])) {
                        $customSanction = $customSanctionsData['customSanctions'][$offenseIndex];
                        $finalSanctionId = $customSanction['sanctionId'] ?? $finalSanctionId;
                        Log::info("Using custom sanction instead of auto-determined", [
                            'custom_sanction_id' => $finalSanctionId
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
                        Log::warning("Invalid sanction ID {$finalSanctionId} for offense {$offenseIndex}");
                        continue;
                    }

                    // Create violation record
                    try {
                        $newViolation = DB::table('tbl_violation_record')->insertGetId([
                            'violator_id' => $currentViolatorId,
                            'prefect_id' => $prefect_id,
                            'offense_id' => $offenseId,
                            'sanction_id' => $finalSanctionId,
                            'violation_incident' => $incident,
                            'violation_date' => $date,
                            'violation_time' => $time,
                            'complainant' => $complainant,
                            'status' => 'in_progress',
                            'handled_by' => 'adviser',
                            'witnesses' => $witnesses,
                            'evidence_description' => $evidence_description,
                            'evidence_files' => $evidenceFilesJson,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $savedCount++;

                        // Get offense and sanction names
                        $offenseRecord = DB::table('tbl_offense')->where('offense_id', $offenseId)->first();
                        $sanctionRecord = DB::table('tbl_sanction')->where('sanction_id', $finalSanctionId)->first();

                        $offenseName = $offenseRecord ? $offenseRecord->offense_type : 'Unknown Offense';
                        $sanctionName = $sanctionRecord ? $sanctionRecord->sanction_consequences : 'Unknown Sanction';

                        // Calculate what the next sanction would be
                        $nextSanction = $this->getNextSanction($offenseId, $offenseCount + 1);
                        $nextSanctionText = $nextSanction ? "Next: {$nextSanction}" : "Maximum stage reached";

                        $messages[] = "✅ {$violatorName} - {$offenseName} ({$sanctionName}) [Offense #{$offenseCount}] {$nextSanctionText}";

                        // Store for SMS
                        $violationsForSMS[] = [
                            'violation_id' => $newViolation,
                            'violator_id' => $currentViolatorId,
                            'violator_name' => $violatorName,
                            'offense_name' => $offenseName,
                            'sanction_name' => $sanctionName,
                            'offense_count' => $offenseCount,
                            'date' => $date,
                            'time' => $time,
                            'incident' => $incident,
                            'complainant' => $complainant
                        ];

                        Log::info("Violation created", [
                            'id' => $newViolation,
                            'violator' => $violatorName,
                            'offense' => $offenseName,
                            'sanction' => $sanctionName,
                            'offense_count' => $offenseCount,
                            'complainant' => $complainant,
                            'is_group' => $isGroupSubmission
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Failed to create violation {$violationIndex} for offense {$offenseIndex}: {$e->getMessage()}");
                        Log::error("Stack trace: {$e->getTraceAsString()}");
                    }
                }

                // Handle additional offenses from custom sanctions
                if (isset($customSanctionsData['offenses']) && is_array($customSanctionsData['offenses'])) {
                    foreach ($customSanctionsData['offenses'] as $additionalOffense) {
                        $additionalOffenseId = $additionalOffense['offense_id'] ?? null;
                        $additionalSanctionId = $additionalOffense['sanction_id'] ?? null;

                        if (empty($additionalOffenseId) || empty($additionalSanctionId)) {
                            Log::warning("Skipping additional offense - missing offense_id or sanction_id", $additionalOffense);
                            continue;
                        }

                        // Validate additional offense exists
                        $additionalOffenseExists = DB::table('tbl_offense')
                            ->where('offense_id', $additionalOffenseId)
                            ->whereNull('deleted_at')
                            ->exists();

                        if (!$additionalOffenseExists) {
                            Log::warning("Invalid additional offense ID {$additionalOffenseId}");
                            continue;
                        }

                        // Validate additional sanction exists
                        $additionalSanctionExists = DB::table('tbl_sanction')->where('sanction_id', $additionalSanctionId)->exists();
                        if (!$additionalSanctionExists) {
                            Log::warning("Invalid additional sanction ID {$additionalSanctionId}");
                            continue;
                        }

                        // Create separate violation record
                        try {
                            $newAdditionalViolation = DB::table('tbl_violation_record')->insertGetId([
                                'violator_id' => $currentViolatorId,
                                'prefect_id' => $prefect_id,
                                'offense_id' => $additionalOffenseId,
                                'sanction_id' => $additionalSanctionId,
                                'violation_incident' => $incident,
                                'violation_date' => $date,
                                'violation_time' => $time,
                                'complainant' => $complainant,
                                'status' => 'in_progress',
                                'handled_by' => 'adviser',
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

                            // Get offense count for additional offense
                            $additionalOffenseCount = $this->getOffenseCountForStudent($currentViolatorId, $additionalOffenseId);
                            $messages[] = "✅ {$violatorName} - {$offenseName} ({$sanctionName}) [Additional Offense #{$additionalOffenseCount}]";

                            // Store for SMS
                            $violationsForSMS[] = [
                                'violation_id' => $newAdditionalViolation,
                                'violator_id' => $currentViolatorId,
                                'violator_name' => $violatorName,
                                'offense_name' => $offenseName,
                                'sanction_name' => $sanctionName,
                                'offense_count' => $additionalOffenseCount,
                                'date' => $date,
                                'time' => $time,
                                'incident' => $incident,
                                'complainant' => $complainant
                            ];

                            Log::info("Additional violation created", [
                                'id' => $newAdditionalViolation,
                                'offense' => $offenseName,
                                'sanction' => $sanctionName,
                                'offense_count' => $additionalOffenseCount
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to create additional violation {$violationIndex}: {$e->getMessage()}");
                            Log::error("Stack trace: {$e->getTraceAsString()}");
                        }
                    }
                }
            }
        }

        DB::commit();

        Log::info("Violations saved", [
            'saved_count' => $savedCount,
            'is_group_submission' => $isGroupSubmission,
            'violations_for_sms_count' => count($violationsForSMS)
        ]);

        // Send SMS notifications
        if ($savedCount > 0 && !empty($violationsForSMS)) {
            $this->sendViolationNotifications($violationsForSMS);
        }

        if ($savedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No violations were saved.'
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
        Log::error('Violations submission failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}


// ==========================================
// UPDATED: GET NEXT SANCTION
// ==========================================
private function getNextSanction($offenseId, $currentStageNumber)
{
    try {
        $nextStage = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->where('owss.offense_id', $offenseId)
            ->where('owss.owss_id', '>', $currentStageNumber)
            ->whereNull('owss.deleted_at')
            ->whereNull('s.deleted_at')
            ->orderBy('owss.owss_id', 'asc')
            ->select('s.sanction_consequences')
            ->first();

        return $nextStage ? $nextStage->sanction_consequences : 'Maximum stage reached';
    } catch (\Exception $e) {
        Log::error("Error getting next sanction: " . $e->getMessage());
        return null;
    }
}

/**
 * Get offense count for a specific student and offense type
 */
private function getOffenseCountForStudent($studentId, $offenseId)
{
    try {
        // Count ALL violations for this student and offense type
        // Exclude inactive/archived statuses
        $violationCount = DB::table('tbl_violation_record')
            ->where('violator_id', $studentId)
            ->where('offense_id', $offenseId)
            ->whereNotIn('status', ['inactive', 'deleted', 'archived'])
            ->count();

        Log::info("Offense count for student", [
            'student_id' => $studentId,
            'offense_id' => $offenseId,
            'violation_count' => $violationCount
        ]);

        return $violationCount;
    } catch (\Exception $e) {
        Log::error("Error getting offense count: " . $e->getMessage());
        return 0;
    }
}

   public function getSanctionStages(Request $request)
{
    try {
        $offenseId = $request->input('offense_id');

        if (!$offenseId) {
            return response()->json([
                'success' => false,
                'message' => 'Offense ID is required'
            ], 400);
        }

        $stages = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->where('owss.offense_id', $offenseId)
            ->whereNull('owss.deleted_at')
            ->whereNull('s.deleted_at')
            ->orderBy('owss.owss_id', 'asc')
            ->select(
                'owss.owss_id as stage_number',
                's.sanction_id',
                's.sanction_consequences',
                's.sanction_description'
            )
            ->get();

        return response()->json([
            'success' => true,
            'stages' => $stages,
            'total_stages' => $stages->count()
        ]);
    } catch (\Exception $e) {
        Log::error("Error getting sanction stages: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading sanction stages'
        ], 500);
    }
}


public function settle(Request $request, ViolationRecord $violation)
{
        \Log::info('========== SETTLE REQUEST START ==========');
    \Log::info('Request data:', $request->all());
    \Log::info('Violation ID:', ['id' => $violation->violation_id]);
    \Log::info('Current violation status:', ['status' => $violation->status]);
    \Log::info('Current sanction status:', ['sanction_status' => $violation->sanction_status]);

    $request->validate([
        'record_id' => 'required|exists:violation_records,violation_id',
        'status' => 'required|in:pending,in_progress,resolved,noncompliant,dismissed',
        'current_status' => 'sometimes|string',
    ]);

    \Log::info('Validation passed');

    // Prevent settling already resolved violations
    if ($violation->status === 'resolved') {
        return response()->json([
            'success' => false,
            'message' => 'This violation is already resolved and cannot be modified.'
        ], 400);
    }

    // Prevent settling already dismissed violations
    if ($violation->status === 'dismissed') {
        return response()->json([
            'success' => false,
            'message' => 'This violation has been dismissed and cannot be modified.'
        ], 400);
    }

    // Define allowed status transitions
    $allowedTransitions = [
        'pending' => ['in_progress', 'dismissed'],
        'in_progress' => ['resolved', 'noncompliant', 'dismissed'],
        'noncompliant' => ['resolved'], // Fixed: noncompliant can go to resolved or dismissed
        'resolved' => [], // Final status
        'dismissed' => [], // Final status
    ];

    // Check if the requested status change is allowed
    $currentStatus = $violation->status;
    $requestedStatus = $request->status;

    if (!in_array($requestedStatus, $allowedTransitions[$currentStatus] ?? [])) {
        $statusMessages = [
            'pending' => '"In Progress" or "Dismissed"',
            'in_progress' => '"Resolved", "Noncompliant", or "Dismissed"',
            'noncompliant' => '"Resolved" or "Dismissed"',
            'resolved' => '(no further changes allowed)',
            'dismissed' => '(no further changes allowed)',
        ];

        return response()->json([
            'success' => false,
            'message' => 'Cannot change status from "' . ucfirst($currentStatus) .
                        '" to "' . ucfirst($requestedStatus) . '". ' .
                        'Allowed transitions: ' . $statusMessages[$currentStatus]
        ], 400);
    }

    // Check if trying to set to "resolved" but sanction is not completed
    if ($requestedStatus === 'resolved' && $violation->sanction_status !== 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'Cannot resolve violation while sanction is not completed. Current sanction status: ' . $violation->sanction_status
        ], 400);
    }

    // Update only the status and settled_at timestamp
    $violation->update([
        'status' => $requestedStatus,
        'settled_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Violation status updated successfully!'
    ]);
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
    Log::info('=== storeMultipleAppointments START ===');
    Log::info('Request data:', $request->all());

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
        Log::error('Validation failed:', $validator->errors()->toArray());
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

        // REMOVED: $appointmentsForSMS = [];

        // Track processed violation IDs to prevent duplicates
        $processedViolationIds = [];

        Log::info('Initial - Violation IDs:', $violationIds);
        Log::info('Initial - Group Keys:', $groupKeys);

        // Check if at least one violation or group is selected
        if (empty($violationIds) && empty($groupKeys)) {
            Log::warning('No violations or groups selected');
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one violation or group'
            ], 422);
        }

        // Process individual violations first
        if (!empty($violationIds)) {
            Log::info('=== PROCESSING INDIVIDUAL VIOLATIONS ===');
            foreach ($violationIds as $violationId) {
                // Skip if already processed
                if (in_array($violationId, $processedViolationIds)) {
                    Log::info("❌ SKIPPING - Already processed individual violation: $violationId");
                    continue;
                }

                // Check if appointment already exists for this violation
                // Updated: Check for any status, not just 'Scheduled'
                $existingAppointment = ViolationAppointment::where('violation_id', $violationId)
                    ->whereIn('violation_app_status', ['Pending', 'Scheduled', 'Rescheduled'])
                    ->first();

                if ($existingAppointment) {
                    Log::warning("❌ SKIPPING - Appointment already exists for violation: $violationId", [
                        'existing_status' => $existingAppointment->violation_app_status
                    ]);
                    $processedViolationIds[] = $violationId;
                    continue;
                }

                $violation = ViolationRecord::with(['student', 'offense'])
                    ->where('violation_id', $violationId)
                    ->first();

                if ($violation) {
                    // CHANGED: Set status to 'Pending' instead of 'Scheduled'
                    $appointment = ViolationAppointment::create([
                        'violation_id' => $violationId,
                        'violation_app_date' => $request->schedule_date,
                        'violation_app_time' => $request->schedule_time,
                        'violation_app_notes' => $request->violation_app_notes,
                        'violation_app_status' => 'Pending', // CHANGED HERE
                        'handled_by' => 'prefect',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $createdAppointments[] = $appointment;
                    $processedViolationIds[] = $violationId;

                    // REMOVED: SMS notification storage
                    /*
                    $appointmentsForSMS[] = [
                        'appointment_id' => $appointment->violation_app_id,
                        'violation_id' => $violationId,
                        'violator_id' => $violation->violator_id,
                        'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                        'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                        'violation_status' => $violation->status,
                        'appointment_date' => $request->schedule_date,
                        'appointment_time' => $request->schedule_time,
                        'notes' => $request->violation_app_notes,
                        'appointment_status' => 'Pending'
                    ];
                    */

                    Log::info("✅ CREATED - Appointment for individual violation: $violationId", [
                        'appointment_id' => $appointment->violation_app_id,
                        'violation_status' => $violation->status,
                        'appointment_status' => 'Pending'
                    ]);
                } else {
                    Log::warning("❌ VIOLATION NOT FOUND: $violationId");
                }
            }
        }

        // Process group violations - but skip violations already processed from individual selection
        if (!empty($groupKeys)) {
            Log::info('=== PROCESSING GROUP VIOLATIONS ===');
            foreach ($groupKeys as $groupKey) {
                Log::info("Processing group key: $groupKey");

                // Parse the group key to match your grouping logic
                $groupParts = explode('|', $groupKey);
                Log::info("Group parts:", $groupParts);

                if (count($groupParts) >= 5) {
                    $incident = $groupParts[0];
                    $offenseType = $groupParts[1];
                    $sanction = $groupParts[2];
                    $date = $groupParts[3];
                    $timeGroup = $groupParts[4];

                    Log::info("Searching for group violations with:", [
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
                        ->whereHas('offense', function ($query) use ($offenseType) {
                            $query->where('offense_type', $offenseType);
                        })
                        ->whereHas('sanction', function ($query) use ($sanction) {
                            $query->where('sanction_consequences', $sanction);
                        })
                        ->get();

                    Log::info("Found " . $groupViolations->count() . " violations in group");

                    foreach ($groupViolations as $violation) {
                        // Skip if this violation was already processed from individual selection
                        if (in_array($violation->violation_id, $processedViolationIds)) {
                            Log::info("❌ SKIPPING - Already processed group violation: " . $violation->violation_id);
                            continue;
                        }

                        // Check if appointment already exists for this violation
                        // Updated: Check for any status, not just 'Scheduled'
                        $existingAppointment = ViolationAppointment::where('violation_id', $violation->violation_id)
                            ->whereIn('violation_app_status', ['Pending', 'Scheduled', 'Rescheduled'])
                            ->first();

                        if ($existingAppointment) {
                            Log::warning("❌ SKIPPING - Appointment already exists for group violation: " . $violation->violation_id, [
                                'existing_status' => $existingAppointment->violation_app_status
                            ]);
                            $processedViolationIds[] = $violation->violation_id;
                            continue;
                        }

                        // CHANGED: Set status to 'Pending' instead of 'Scheduled'
                        $appointment = ViolationAppointment::create([
                            'violation_id' => $violation->violation_id,
                            'violation_app_date' => $request->schedule_date,
                            'violation_app_time' => $request->schedule_time,
                            'violation_app_notes' => $request->violation_app_notes,
                            'violation_app_status' => 'Pending', // CHANGED HERE
                            'handled_by' => 'prefect',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $createdAppointments[] = $appointment;
                        $processedViolationIds[] = $violation->violation_id;

                        // REMOVED: SMS notification storage
                        /*
                        $appointmentsForSMS[] = [
                            'appointment_id' => $appointment->violation_app_id,
                            'violation_id' => $violation->violation_id,
                            'violator_id' => $violation->violator_id,
                            'violator_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                            'offense_name' => $violation->offense->offense_type ?? 'Unknown Offense',
                            'violation_status' => $violation->status,
                            'appointment_date' => $request->schedule_date,
                            'appointment_time' => $request->schedule_time,
                            'notes' => $request->violation_app_notes,
                            'appointment_status' => 'Pending'
                        ];
                        */

                        Log::info("✅ CREATED - Appointment for group violation: " . $violation->violation_id, [
                            'appointment_id' => $appointment->violation_app_id,
                            'violation_status' => $violation->status,
                            'appointment_status' => 'Pending'
                        ]);
                    }
                } else {
                    Log::warning("❌ INVALID GROUP KEY FORMAT: $groupKey");
                }
            }
        }

        DB::commit();

        Log::info("=== FINAL RESULTS ===");
        Log::info("Total appointments created: " . count($createdAppointments));
        Log::info("Processed violation IDs: ", $processedViolationIds);
        // REMOVED: Log SMS appointments

        if (empty($createdAppointments)) {
            return response()->json([
                'success' => false,
                'message' => 'No appointments were created. Please check if the selected violations exist or if appointments already exist.'
            ], 400);
        }

        // REMOVED: SMS notification sending
        /*
        if (!empty($appointmentsForSMS)) {
            Log::info("Sending SMS notifications for appointments", [
                'total_appointments' => count($appointmentsForSMS)
            ]);
            $this->sendAppointmentNotifications($appointmentsForSMS);
        }
        */

        return response()->json([
            'success' => true,
            'message' => count($createdAppointments) . ' appointment(s) created successfully with Pending status',
            'data' => $createdAppointments
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating appointments: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Error creating appointments: ' . $e->getMessage()
        ], 500);
    }
}

    public function updateGroup(Request $request)
    {
        try {
            // Your group update logic here
            $groupKey = $request->input('group_key');

            // Update the group violations
            // Your implementation...

            return response()->json([
                'success' => true,
                'message' => 'Group violation updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating group violation: ' . $e->getMessage()
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
        Log::info('=== storeMultipleAnecdotals START ===');
        Log::info('Request data:', $request->all());

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
            Log::error('Anecdotal validation errors:', $validator->errors()->toArray());
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

            Log::info('Processing individual violations...');
            // Process individual violations
            if ($request->has('violation_ids')) {
                $allViolationIds = array_merge($allViolationIds, $request->violation_ids);
                Log::info('Individual violation IDs:', $request->violation_ids);
            }

            Log::info('Processing group violations...');
            // Process group violations
            if ($request->has('group_keys')) {
                Log::info('Group keys found:', $request->group_keys);

                foreach ($request->group_keys as $groupKey) {
                    Log::info("Processing group key: $groupKey");

                    // Parse the group key to match your grouping logic
                    $groupParts = explode('|', $groupKey);
                    Log::info("Group parts:", $groupParts);

                    if (count($groupParts) >= 5) {
                        $incident = $groupParts[0];
                        $offenseType = $groupParts[1];
                        $sanction = $groupParts[2];
                        $date = $groupParts[3];
                        $timeGroup = $groupParts[4];

                        Log::info("Searching for group with:", [
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
                            ->whereHas('offense', function ($query) use ($offenseType) {
                                $query->where('offense_type', $offenseType);
                            })
                            ->whereHas('sanction', function ($query) use ($sanction) {
                                $query->where('sanction_consequences', $sanction);
                            })
                            ->get();

                        Log::info("Found " . $groupViolations->count() . " violations in this group");

                        if ($groupViolations->count() > 0) {
                            foreach ($groupViolations as $violation) {
                                $allViolationIds[] = $violation->violation_id;
                                Log::info("Added violation ID: " . $violation->violation_id . " with status: " . $violation->status);
                            }
                        } else {
                            Log::warning("No violations found for group key: $groupKey");
                        }
                    } else {
                        Log::warning("Invalid group key format: $groupKey");
                    }
                }
            }

            // Remove duplicates
            $allViolationIds = array_unique($allViolationIds);
            Log::info('All unique violation IDs to process:', $allViolationIds);

            if (empty($allViolationIds)) {
                Log::warning('No violation IDs to process after filtering');
                return response()->json([
                    'success' => false,
                    'message' => 'No valid violations found to process.'
                ], 400);
            }

            foreach ($allViolationIds as $violationId) {
                Log::info("Processing violation ID: $violationId");

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
                    Log::warning("Violation not found: $violationId");
                    continue;
                }

                Log::info("Violation found with status: " . $violation->status);

                // ALWAYS CREATE NEW ANECDOTAL RECORD - regardless of existing anecdotal or violation status
                Log::info("Creating new anecdotal record for violation: $violationId");

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

                Log::info("✅ Created new anecdotal record for violation $violationId", [
                    'anecdotal_id' => $anecdotal->violation_anec_id,
                    'violation_id' => $violationId,
                    'violation_status' => $violation->status,
                    'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
                    'anecdotal_status' => 'completed'
                ]);
            }

            DB::commit();

            $totalProcessed = count($createdAnecdotals);
            Log::info("✅ Total created: $totalProcessed anecdotal records");

            if ($totalProcessed === 0) {
                Log::error('No anecdotal records were created.');
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
            Log::error('❌ Error creating anecdotal records: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

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

            $pdf = PDF::loadView('adviser.NewAdviser.pdf.multiple_anecdotal', $data);

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
    // Handle status-only updates (from Settlement modal)
    if ($request->has('status') && !$request->has('violator_id')) {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,resolved,dismissed,closed,noncompliant'
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
            $currentSanctionStatus = $violation->sanction_status;
            $newViolationStatus = $request->input('status');

            // ============================================
            // ADDED: CHECKERS FOR STATUS UPDATES
            // ============================================

            // 1. Cannot update to "resolved" if sanction_status is not "completed"
            if ($newViolationStatus === 'resolved' && $currentSanctionStatus !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot mark as resolved: Sanction status must be "Completed" first. Current sanction status is "' . ucfirst($currentSanctionStatus) . '".'
                ], 400);
            }

            // 2. Cannot update to "dismissed" if sanction_status is not "dismissed"
            if ($newViolationStatus === 'dismissed' && $currentSanctionStatus !== 'dismissed') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot dismiss violation: Sanction must be dismissed first. Current sanction status is "' . ucfirst($currentSanctionStatus) . '".'
                ], 400);
            }

            // 3. Additional logical checks (optional but recommended)

            // a. Cannot update to "resolved" if current status is already "dismissed"
            if ($newViolationStatus === 'resolved' && $violation->status === 'dismissed') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot mark as resolved: This violation has already been dismissed.'
                ], 400);
            }

            // b. Cannot update to "dismissed" if current status is already "resolved"
            if ($newViolationStatus === 'dismissed' && $violation->status === 'resolved') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Cannot dismiss: This violation has already been resolved.'
                ], 400);
            }

            // c. Check if status change follows allowed transitions
            $allowedTransitions = [
                'pending' => ['in_progress', 'dismissed'],
                'in_progress' => ['resolved', 'noncompliant', 'dismissed'],
                'noncompliant' => ['resolved', 'dismissed'],
                'resolved' => [], // No further changes allowed
                'dismissed' => [], // No further changes allowed
                'closed' => [], // No further changes allowed
            ];

            $currentStatus = $violation->status;
            if (isset($allowedTransitions[$currentStatus]) &&
                !in_array($newViolationStatus, $allowedTransitions[$currentStatus])) {
                $allowedOptions = implode(', ', array_map('ucfirst', $allowedTransitions[$currentStatus]));
                return response()->json([
                    'success' => false,
                    'message' => '❌ Invalid status transition from "' . ucfirst($currentStatus) . '" to "' . ucfirst($newViolationStatus) . '". Allowed transitions: ' . $allowedOptions
                ], 400);
            }


            // Update the violation
            $violation->status = $newViolationStatus;
    
            $violation->save();

            // Log the status change
            \Log::info('Violation status updated', [
                'violation_id' => $violationId,
                'old_status' => $currentStatus,
                'new_status' => $newViolationStatus,
                'sanction_status' => $currentSanctionStatus,
                'user_id' => auth()->user()->id ?? 'unknown',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Violation status updated successfully!',
                'data' => [
                    'violation_id' => $violationId,
                    'new_status' => $newViolationStatus,
                    'sanction_status' => $currentSanctionStatus,
                    'updated_at' => $violation->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error updating violation status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Validation for full record updates
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

        // Check if violation is already resolved or dismissed (cannot edit)
        if (in_array($violation->status, ['resolved', 'dismissed'])) {
            return response()->json([
                'success' => false,
                'message' => '❌ Cannot edit ' . ucfirst($violation->status) . ' violations. The status must be changed first.'
            ], 400);
        }

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
    // Get the logged-in adviser's ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification counts
    $notificationCounts = $this->getNotificationCounts();

    try {
        // Filter students by the logged-in adviser
        $students = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('adviser_id', $adviserId)
            ->orderBy('student_lname')
            ->orderBy('student_fname')
            ->get();

        // Debug: Check if students are loading for this adviser
        Log::info('Students loaded for adviser:', [
            'adviser_id' => $adviserId,
            'count' => $students->count(),
            'sample' => $students->take(3)->map(function($student) {
                return $student->student_fname . ' ' . $student->student_lname;
            })
        ]);

        // Load offenses that are actually in the database
        $offenses = Offense::whereNull('deleted_at')
            ->orderBy('offense_type')
            ->get();

        // Debug: Check if offenses are loading
        Log::info('Offenses loaded:', [
            'count' => $offenses->count(),
            'sample' => $offenses->take(3)->pluck('offense_type')
        ]);

        return view('adviser.NewAdviser.create-violation', array_merge(
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

  // ==========================================
// UPDATED: GET OFFENSE HISTORY FOR DISPLAY
// ==========================================
public function getOffenseHistory(Request $request)
{
    try {
        $studentIds = $request->input('student_ids');
        $offenseId = $request->input('offense_id');

        Log::info('🔍 getOffenseHistory called:', [
            'student_ids' => $studentIds,
            'offense_id' => $offenseId
        ]);

        if (!$studentIds || !$offenseId) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        if (!is_array($studentIds)) {
            $studentIds = [$studentIds];
        }

        $history = [];

        foreach ($studentIds as $studentId) {
            Log::info("Processing student ID: {$studentId} for offense ID: {$offenseId}");

            // Count violations for THIS SPECIFIC offense
            $offenseCount = $this->getOffenseCountForStudent($studentId, $offenseId);

            Log::info("Student {$studentId} has {$offenseCount} violations for offense ID {$offenseId}");

            // Get previous sanction
            $previousSanction = $this->getPreviousSanctionForStudent($studentId, $offenseId);

            // Determine current sanction based on offense count
            $currentSanctionData = $this->determineCurrentSanction($studentId, $offenseId, $offenseCount);

            // Get next sanction
            $nextSanction = $this->getNextSanction($offenseId, $currentSanctionData['stage_number'] ?? 1);

            // Get recent violation records
            $violationRecords = DB::table('tbl_violation_record as vr')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->where('vr.violator_id', $studentId)
                ->where('vr.offense_id', $offenseId)
                ->whereIn('vr.status', ['in_progress', 'pending', 'resolved'])
                ->select(
                    'vr.violation_id',
                    'vr.violation_date as date',
                    'vr.violation_time as time',
                    'vr.violation_incident as description',
                    'o.offense_type',
                    's.sanction_consequences as sanction',
                    'o.offense_id',
                    'vr.created_at'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->limit(5)
                ->get();

            $formattedRecords = $violationRecords->map(function ($record) {
                return [
                    'violation_id' => $record->violation_id,
                    'date' => Carbon::parse($record->date)->format('M d, Y'),
                    'time' => $record->time,
                    'description' => $record->description,
                    'offense_type' => $record->offense_type,
                    'sanction' => $record->sanction,
                    'offense_id' => $record->offense_id,
                ];
            })->toArray();

            $history[$studentId] = [
                'count' => $offenseCount,
                'records' => $formattedRecords,
                'previous_sanction' => $previousSanction,
                'current_sanction' => $currentSanctionData['sanction_consequences'],
                'current_sanction_id' => $currentSanctionData['sanction_id'],
                'next_sanction' => $nextSanction,
                'stage_number' => $currentSanctionData['stage_number'] ?? 1
            ];
        }

        return response()->json($history);
    } catch (\Exception $e) {
        Log::error('❌ Error in getOffenseHistory: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        $fallbackHistory = [];
        foreach ($request->input('student_ids', []) as $studentId) {
            if (!is_array($studentId)) {
                $fallbackHistory[$studentId] = [
                    'count' => 0,
                    'records' => [],
                    'previous_sanction' => 'None',
                    'current_sanction' => 'Verbal Warning',
                    'current_sanction_id' => null,
                    'next_sanction' => null,
                    'stage_number' => 1
                ];
            }
        }

        return response()->json($fallbackHistory);
    }
}

// ==========================================
// UPDATED: GET PREVIOUS SANCTION FOR STUDENT AND OFFENSE
// ==========================================
private function getPreviousSanctionForStudent($studentId, $offenseId)
{
    try {
        // Get the most recent sanction for this student and offense type
        $previousViolation = DB::table('tbl_violation_record as vr')
            ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
            ->where('vr.violator_id', $studentId)
            ->where('vr.offense_id', $offenseId)
            ->whereIn('vr.status', ['in_progress', 'pending', 'resolved'])
            ->orderBy('vr.violation_date', 'desc')
            ->orderBy('vr.violation_time', 'desc')
            ->select('s.sanction_consequences')
            ->first();

        return $previousViolation ? $previousViolation->sanction_consequences : 'None';
    } catch (\Exception $e) {
        Log::error("Error getting previous sanction: " . $e->getMessage());
        return 'None';
    }
}

// ==========================================
// UPDATED: DETERMINE CURRENT SANCTION BASED ON OFFENSE COUNT
// ==========================================
private function determineCurrentSanction($studentId, $offenseId, $offenseCount)
{
    try {
        // Get sanction stages for this offense
        $sanctionStages = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->where('owss.offense_id', $offenseId)
            ->whereNull('owss.deleted_at')
            ->whereNull('s.deleted_at')
            ->orderBy('owss.owss_id', 'asc')
            ->select(
                'owss.owss_id as stage_number',
                's.sanction_id',
                's.sanction_consequences',
                's.sanction_description'
            )
            ->get();

        if ($sanctionStages->isEmpty()) {
            // Fallback to default sanction
            return [
                'sanction_id' => null,
                'sanction_consequences' => 'Verbal Warning',
                'sanction_description' => 'Default sanction'
            ];
        }

        // Determine which stage to use (stage 1 = 1st offense, stage 2 = 2nd offense, etc.)
        // If offenseCount is 0 (first offense), use stage 1
        // If offenseCount is 1 (second offense), use stage 2, etc.
        $stageIndex = min($offenseCount, $sanctionStages->count() - 1);
        $currentStage = $sanctionStages[$stageIndex];

        return [
            'sanction_id' => $currentStage->sanction_id,
            'sanction_consequences' => $currentStage->sanction_consequences,
            'sanction_description' => $currentStage->sanction_description,
            'stage_number' => $currentStage->stage_number
        ];
    } catch (\Exception $e) {
        Log::error("Error determining current sanction: " . $e->getMessage());
        return [
            'sanction_id' => null,
            'sanction_consequences' => 'Verbal Warning',
            'sanction_description' => 'Error determining sanction'
        ];
    }
}


/**
 * Determine sanction ID based on offense count using the stages table
 */
private function determineSanctionByOffenseCount($offenseId, $offenseCount)
{
    try {
        // Get all sanction stages for this offense ordered by owss_id
        $sanctionStages = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->where('owss.offense_id', $offenseId)
            ->whereNull('owss.deleted_at')
            ->whereNull('s.deleted_at')
            ->orderBy('owss.owss_id', 'asc')
            ->get(['owss.owss_id', 's.sanction_id', 's.sanction_consequences']);

        if ($sanctionStages->isEmpty()) {
            Log::warning("No sanction stages found for offense ID: {$offenseId}");

            // Fallback: Use the first sanction from the general sanction table
            $fallbackSanction = DB::table('tbl_sanction')
                ->whereNull('deleted_at')
                ->orderBy('sanction_id')
                ->first();

            return $fallbackSanction ? $fallbackSanction->sanction_id : 'not_assigned';
        }

        // Determine which stage to use based on offense count
        // owss_id 1 = 1st offense (count 0 = new offender gets stage 1)
        // owss_id 2 = 2nd offense (count 1 = gets stage 2)
        // etc.
        $stageIndex = min($offenseCount, $sanctionStages->count() - 1);
        $selectedSanction = $sanctionStages[$stageIndex];

        Log::info("Auto-determined sanction for offense count {$offenseCount}:", [
            'offense_id' => $offenseId,
            'stage_index' => $stageIndex,
            'sanction_id' => $selectedSanction->sanction_id,
            'sanction_name' => $selectedSanction->sanction_consequences,
            'total_stages' => $sanctionStages->count()
        ]);

        return $selectedSanction->sanction_id;
    } catch (\Exception $e) {
        Log::error("Error determining sanction: " . $e->getMessage());
        return 'not_assigned';
    }
}

/**
 * Update sanction timing and status for violations
 */
public function updateSanction(Request $request)
{
    Log::info('=== UPDATE SANCTION START ===');
    Log::info('Request data:', $request->all());

    // Validate the request - UPDATED: added 'dismissed' option
    $validator = Validator::make($request->all(), [
        'violation_ids' => 'sometimes|array',
        'violation_ids.*' => 'exists:tbl_violation_record,violation_id',
        'group_keys' => 'sometimes|array',
        'group_keys.*' => 'string',
        'sanction_start_date' => 'required|date',
        'sanction_start_time' => 'nullable|date_format:H:i',
        'sanction_end_date' => 'nullable|date',
        'sanction_end_time' => 'nullable|date_format:H:i',
        'sanction_status' => 'required|in:pending,ongoing,neglected,completed,dismissed' // ADDED 'dismissed'
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $updatedCount = 0;
        $violationIds = $request->violation_ids ?? [];
        $groupKeys = $request->group_keys ?? [];
        $processedViolationIds = [];
        $newStatus = $request->sanction_status;

        Log::info('Initial - Violation IDs:', ['violation_ids' => $violationIds]);
        Log::info('Initial - Group Keys:', ['group_keys' => $groupKeys]);
        Log::info('New Status:', ['status' => $newStatus]);

        // Check if at least one violation or group is selected
        if (empty($violationIds) && empty($groupKeys)) {
            Log::warning('No violations or groups selected');
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one violation or group'
            ], 422);
        }

        // Define which statuses CANNOT be changed to dismissed
        $cannotBeDismissed = ['ongoing', 'neglected', 'completed'];

        // Combine start date and time
        $sanctionStartDateTime = $request->sanction_start_date;
        if ($request->sanction_start_time) {
            $sanctionStartDateTime .= ' ' . $request->sanction_start_time;
        } else {
            $sanctionStartDateTime .= ' 00:00:00';
        }

        // Combine end date and time (if provided)
        $sanctionEndDateTime = null;
        if ($request->sanction_end_date) {
            $sanctionEndDateTime = $request->sanction_end_date;
            if ($request->sanction_end_time) {
                $sanctionEndDateTime .= ' ' . $request->sanction_end_time;
            } else {
                $sanctionEndDateTime .= ' 23:59:59';
            }
        }

        Log::info('Sanction datetime values:', [
            'start' => $sanctionStartDateTime,
            'end' => $sanctionEndDateTime,
            'status' => $newStatus
        ]);

        // Process individual violations first
        if (!empty($violationIds)) {
            Log::info('=== PROCESSING INDIVIDUAL VIOLATIONS ===');
            foreach ($violationIds as $violationId) {
                // Skip if already processed
                if (in_array($violationId, $processedViolationIds)) {
                    Log::info("SKIPPING - Already processed individual violation", ['violation_id' => $violationId]);
                    continue;
                }

                $violation = ViolationRecord::find($violationId);

                if ($violation) {
                    // Get current status
                    $currentStatus = $violation->sanction_status ?? 'pending';
                    Log::info("Violation status check", [
                        'violation_id' => $violationId,
                        'current_status' => $currentStatus,
                        'new_status' => $newStatus
                    ]);

                    // VALIDATE STATUS TRANSITION DIRECTLY
                    $isValidTransition = true;

                    // Define allowed transitions
                    $allowedTransitions = [
                        'pending' => ['ongoing', 'dismissed'],
                        'ongoing' => ['completed', 'neglected'],
                        'neglected' => ['completed'],
                        'completed' => [],
                        'dismissed' => []
                    ];

                    // Check if trying to change to dismissed when not allowed
                    if ($newStatus === 'dismissed' && in_array($currentStatus, $cannotBeDismissed)) {
                        $isValidTransition = false;
                        Log::warning("CANNOT DISMISS - Violation cannot be dismissed", [
                            'violation_id' => $violationId,
                            'current_status' => $currentStatus
                        ]);
                    }
                    // Check standard transitions
                    elseif (array_key_exists($currentStatus, $allowedTransitions)) {
                        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
                            $isValidTransition = false;
                            Log::warning("INVALID STATUS TRANSITION", [
                                'violation_id' => $violationId,
                                'from' => $currentStatus,
                                'to' => $newStatus
                            ]);
                        }
                    }
                    // If current status not in list, allow transition to dismissed only from pending
                    elseif ($newStatus === 'dismissed' && $currentStatus === 'pending') {
                        // Allow dismissed from pending
                        $isValidTransition = true;
                    }
                    // For unknown current statuses, allow the transition but log it
                    else {
                        Log::warning("Unknown current status, allowing transition", [
                            'violation_id' => $violationId,
                            'current_status' => $currentStatus,
                            'new_status' => $newStatus
                        ]);
                        $isValidTransition = true;
                    }

                    if (!$isValidTransition) {
                        Log::warning("INVALID STATUS TRANSITION - Skipping violation", [
                            'violation_id' => $violationId,
                            'from' => $currentStatus,
                            'to' => $newStatus
                        ]);
                        continue; // Skip this violation
                    }

                    // Prepare update data
                    $updateData = [
                        'sanction_status' => $newStatus
                    ];

                    // For dismissed status, clear dates. For other statuses, set the dates
                    if ($newStatus !== 'dismissed') {
                        $updateData['sanction_start_at'] = $sanctionStartDateTime;
                        $updateData['sanction_end_at'] = $sanctionEndDateTime;
                    } else {
                        // For dismissed, we can either keep existing dates or clear them
                        // Clearing them makes sense since the sanction is dismissed
                        $updateData['sanction_start_at'] = null;
                        $updateData['sanction_end_at'] = null;
                    }

                    $violation->update($updateData);

                    $updatedCount++;
                    $processedViolationIds[] = $violationId;

                    Log::info("UPDATED - Sanction for violation", [
                        'violation_id' => $violationId,
                        'start' => $updateData['sanction_start_at'] ?? 'null',
                        'end' => $updateData['sanction_end_at'] ?? 'null',
                        'status' => $newStatus,
                        'previous_status' => $currentStatus
                    ]);
                } else {
                    Log::warning("VIOLATION NOT FOUND", ['violation_id' => $violationId]);
                }
            }
        }

        // Process group violations - but skip violations already processed from individual selection
        if (!empty($groupKeys)) {
            Log::info('=== PROCESSING GROUP VIOLATIONS ===');
            foreach ($groupKeys as $groupKey) {
                Log::info("Processing group key", ['group_key' => $groupKey]);

                // Parse the group key to match your grouping logic
                $groupParts = explode('|', $groupKey);
                Log::info("Group parts", ['parts' => $groupParts]);

                if (count($groupParts) >= 5) {
                    $incident = $groupParts[0];
                    $offenseType = $groupParts[1];
                    $sanction = $groupParts[2];
                    $date = $groupParts[3];
                    $timeGroup = $groupParts[4];

                    Log::info("Searching for group violations", [
                        'incident' => $incident,
                        'offense_type' => $offenseType,
                        'sanction' => $sanction,
                        'date' => $date,
                        'time_group' => $timeGroup
                    ]);

                    // Find violations that match this exact group
                    $groupViolations = ViolationRecord::with(['offense', 'sanction'])
                        ->where('violation_incident', $incident)
                        ->where('violation_date', $date)
                        ->whereHas('offense', function ($query) use ($offenseType) {
                            $query->where('offense_type', $offenseType);
                        })
                        ->whereHas('sanction', function ($query) use ($sanction) {
                            $query->where('sanction_consequences', $sanction);
                        })
                        ->get();

                    Log::info("Found violations in group", ['count' => $groupViolations->count()]);

                    foreach ($groupViolations as $violation) {
                        // Skip if this violation was already processed from individual selection
                        if (in_array($violation->violation_id, $processedViolationIds)) {
                            Log::info("SKIPPING - Already processed group violation", ['violation_id' => $violation->violation_id]);
                            continue;
                        }

                        // Get current status
                        $currentStatus = $violation->sanction_status ?? 'pending';
                        Log::info("Group Violation status check", [
                            'violation_id' => $violation->violation_id,
                            'current_status' => $currentStatus,
                            'new_status' => $newStatus
                        ]);

                        // VALIDATE STATUS TRANSITION DIRECTLY
                        $isValidTransition = true;

                        // Define allowed transitions (same as above)
                        $allowedTransitions = [
                            'pending' => ['ongoing', 'dismissed'],
                            'ongoing' => ['completed', 'neglected'],
                            'neglected' => ['completed'],
                            'completed' => [],
                            'dismissed' => []
                        ];

                        // Check if trying to change to dismissed when not allowed
                        if ($newStatus === 'dismissed' && in_array($currentStatus, $cannotBeDismissed)) {
                            $isValidTransition = false;
                            Log::warning("CANNOT DISMISS - Group violation cannot be dismissed", [
                                'violation_id' => $violation->violation_id,
                                'current_status' => $currentStatus
                            ]);
                        }
                        // Check standard transitions
                        elseif (array_key_exists($currentStatus, $allowedTransitions)) {
                            if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
                                $isValidTransition = false;
                                Log::warning("INVALID STATUS TRANSITION for group", [
                                    'violation_id' => $violation->violation_id,
                                    'from' => $currentStatus,
                                    'to' => $newStatus
                                ]);
                            }
                        }
                        // If current status not in list, allow transition to dismissed only from pending
                        elseif ($newStatus === 'dismissed' && $currentStatus === 'pending') {
                            // Allow dismissed from pending
                            $isValidTransition = true;
                        }
                        // For unknown current statuses, allow the transition but log it
                        else {
                            Log::warning("Unknown current status for group, allowing transition", [
                                'violation_id' => $violation->violation_id,
                                'current_status' => $currentStatus,
                                'new_status' => $newStatus
                            ]);
                            $isValidTransition = true;
                        }

                        if (!$isValidTransition) {
                            Log::warning("INVALID STATUS TRANSITION - Skipping group violation", [
                                'violation_id' => $violation->violation_id,
                                'from' => $currentStatus,
                                'to' => $newStatus
                            ]);
                            continue; // Skip this violation
                        }

                        // Prepare update data
                        $updateData = [
                            'sanction_status' => $newStatus
                        ];

                        // For dismissed status, clear dates. For other statuses, set the dates
                        if ($newStatus !== 'dismissed') {
                            $updateData['sanction_start_at'] = $sanctionStartDateTime;
                            $updateData['sanction_end_at'] = $sanctionEndDateTime;
                        } else {
                            // For dismissed, clear dates
                            $updateData['sanction_start_at'] = null;
                            $updateData['sanction_end_at'] = null;
                        }

                        $violation->update($updateData);

                        $updatedCount++;
                        $processedViolationIds[] = $violation->violation_id;

                        Log::info("UPDATED - Sanction for group violation", [
                            'violation_id' => $violation->violation_id,
                            'start' => $updateData['sanction_start_at'] ?? 'null',
                            'end' => $updateData['sanction_end_at'] ?? 'null',
                            'status' => $newStatus,
                            'previous_status' => $currentStatus
                        ]);
                    }
                } else {
                    Log::warning("INVALID GROUP KEY FORMAT", ['group_key' => $groupKey]);
                }
            }
        }

        DB::commit();

        Log::info("=== FINAL RESULTS ===", [
            'total_updated' => $updatedCount,
            'processed_ids' => $processedViolationIds
        ]);

        if ($updatedCount === 0) {
            $message = 'No sanctions were updated. ';
            if ($newStatus === 'dismissed') {
                $message .= 'Dismissed option is only available for pending sanctions. ';
            }
            $message .= 'Please check if the selected violations exist or if the status transition is allowed.';

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $updatedCount . ' sanction(s) updated successfully',
            'updated_count' => $updatedCount
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating sanctions: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Error updating sanctions: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * NEW: Validate sanction status transition
 * Implements one-way flow: pending → ongoing → (completed or neglected) → completed
 */
private function validateSanctionStatusTransition($currentStatus, $newStatus)
{
    // Define allowed transitions
    $allowedTransitions = [
        'pending' => ['ongoing'],
        'ongoing' => ['completed', 'neglected'],
        'neglected' => ['completed'],
        'completed' => [] // No transitions allowed from completed
    ];

    $currentStatus = strtolower($currentStatus);
    $newStatus = strtolower($newStatus);

    // If current status is not in the list, allow any transition
    if (!array_key_exists($currentStatus, $allowedTransitions)) {
        Log::warning("Unknown current status: $currentStatus. Allowing transition to: $newStatus");
        return true;
    }

    // Check if transition is allowed
    $isAllowed = in_array($newStatus, $allowedTransitions[$currentStatus]);

    Log::info("Status transition validation: From '$currentStatus' to '$newStatus' = " . ($isAllowed ? 'ALLOWED' : 'NOT ALLOWED'));

    return $isAllowed;
}

  public function getRecommendedSanctionStage(Request $request)
{
    try {
        $offenseId = $request->input('offense_id');
        $offenseCount = $request->input('offense_count', 0);

        Log::info('Getting recommended sanction for:', [
            'offense_id' => $offenseId,
            'offense_count' => $offenseCount
        ]);

        // Get ALL sanctions linked to this offense (no order assumed)
        $sanctions = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->where('owss.offense_id', $offenseId)
            ->whereNull('owss.deleted_at')
            ->whereNull('s.deleted_at')
            ->get();

        if ($sanctions->isEmpty()) {
            Log::warning("No sanctions found for offense ID: {$offenseId}");

            // Fallback: get the first sanction from the general list
            $fallbackSanction = DB::table('tbl_sanction')
                ->whereNull('deleted_at')
                ->orderBy('sanction_id')
                ->first();

            return response()->json([
                'success' => true,
                'recommended_sanction' => $fallbackSanction ? $fallbackSanction->sanction_consequences : 'Verbal Warning',
                'offense_count' => $offenseCount,
                'sanction_id' => $fallbackSanction ? $fallbackSanction->sanction_id : null
            ]);
        }

        // FIXED: Use a simple mapping based on offense count
        // If no stages defined, just cycle through available sanctions

        $availableSanctions = $sanctions->values(); // Reset array keys

        if ($availableSanctions->isEmpty()) {
            // Fallback
            return response()->json([
                'success' => true,
                'recommended_sanction' => 'Verbal Warning',
                'sanction_id' => null
            ]);
        }

        // Simple approach: Use offense count modulo number of sanctions
        // This will cycle through the available sanctions
        $sanctionIndex = $offenseCount % $availableSanctions->count();
        $recommendedSanction = $availableSanctions[$sanctionIndex];

        return response()->json([
            'success' => true,
            'recommended_sanction' => $recommendedSanction->sanction_consequences,
            'sanction_id' => $recommendedSanction->sanction_id,
            'offense_count' => $offenseCount,
            'sanction_index' => $sanctionIndex,
            'total_sanctions' => $availableSanctions->count()
        ]);
    } catch (\Exception $e) {
        Log::error('Error getting recommended sanction: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error getting recommended sanction'
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
        $pdf = PDF::loadView('adviser.NewAdviser.pdf.anecdotal', $data);

        return $pdf->output();

    } catch (\Exception $e) {
        Log::error('Error generating single anecdotal PDF: ' . $e->getMessage());
        throw $e; // Re-throw to be caught by the parent method
    }
}


public function getCurrentSanction(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,student_id',
        'offense_id' => 'required|exists:offenses,offense_id',
        'offense_count' => 'required|integer|min:0'
    ]);

    $studentId = $request->student_id;
    $offenseId = $request->offense_id;
    $offenseCount = $request->offense_count;

    // Get the sanction stage based on offense count
    $sanctionStage = OffenseWithSanctionStage::where('offense_id', $offenseId)
        ->where('stage_number', $offenseCount + 1) // +1 because stage 1 is for first offense
        ->first();

    if ($sanctionStage) {
        return response()->json([
            'success' => true,
            'current_sanction' => $sanctionStage->sanction_consequences ?? $sanctionStage->sanction_name
        ]);
    }

    // If no specific stage found, get the highest applicable stage
    $applicableStage = OffenseWithSanctionStage::where('offense_id', $offenseId)
        ->where('stage_number', '<=', $offenseCount + 1)
        ->orderBy('stage_number', 'desc')
        ->first();

    if ($applicableStage) {
        return response()->json([
            'success' => true,
            'current_sanction' => $applicableStage->sanction_consequences ?? $applicableStage->sanction_name
        ]);
    }

    // Default to Verbal Warning
    return response()->json([
        'success' => true,
        'current_sanction' => 'Verbal Warning'
    ]);
}
}
