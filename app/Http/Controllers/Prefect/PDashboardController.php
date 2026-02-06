<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\ViolationRecord;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ViolationAppointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PDashboardController extends Controller
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


    public function dashboard()
    {
        try {
            // Get notification data
            $notificationData = $this->getNotificationData();

            // Total Students
            $totalStudents = Student::where('status', 'active')->count();

            // Weekly Students (new students in the last 7 days)
            $weeklyStudents = Student::where('status', 'active')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            // Total Violations (both pending and in_progress)
            $totalViolations = ViolationRecord::whereIn('status', ['pending', 'in_progress'])->count();

            // Weekly Violations (violations in the last 7 days)
            $weeklyViolations = ViolationRecord::whereIn('status', ['pending', 'in_progress'])
                ->where('violation_date', '>=', Carbon::now()->subDays(7))
                ->count();

            // Pending Violations (only pending status)
            $pendingViolations = ViolationRecord::where('status', 'pending')->count();

            // In Progress Violations
            $inProgressViolations = ViolationRecord::where('status', 'in_progress')->count();

            // Resolved Violations (assuming 'resolved' or 'completed' status)
            $resolvedViolations = ViolationRecord::whereIn('status', ['resolved', 'completed'])->count();

            // Get all active students for modal
            $students = Student::with(['adviser'])
                ->where('status', 'active')
                ->orderBy('student_lname')
                ->get()
                ->map(function($student) {
                    $isRecent = Carbon::parse($student->created_at)->greaterThanOrEqualTo(Carbon::now()->subDays(7));

                    $gradeLevel = 'N/A';
                    $section = 'N/A';

                    if ($student->adviser) {
                        $gradeLevel = $student->adviser->adviser_gradelevel ?? 'N/A';
                        $section = $student->adviser->adviser_section ?? 'N/A';
                    }

                    return [
                        'student_id' => $student->student_id,
                        'student_name' => $student->student_fname . ' ' . $student->student_lname,
                        'student_fname' => $student->student_fname,
                        'student_lname' => $student->student_lname,
                        'grade_level' => $gradeLevel,
                        'section' => $section,
                        'status' => $student->status,
                        'is_recent' => $isRecent
                    ];
                });

            // Get violations with student details for table - both pending and in_progress
            $violations = ViolationRecord::with(['student.adviser', 'offense'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('violation_date', 'desc')
                ->get()
                ->map(function($violation) {
                    $isRecent = Carbon::parse($violation->violation_date)->greaterThanOrEqualTo(Carbon::now()->subDays(7));

                    $gradeLevel = 'N/A';
                    $studentName = 'Unknown Student';
                    $violationType = 'N/A';
                    $description = 'N/A';

                    if ($violation->student) {
                        $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;

                        if ($violation->student->adviser) {
                            $gradeLevel = $violation->student->adviser->adviser_gradelevel ?? 'N/A';
                        }
                    }

                    if ($violation->offense) {
                        $violationType = $violation->offense->offense_type ?? 'N/A';
                        $description = $violation->offense->offense_description ?? 'N/A';
                    }

                    return [
                        'violation_id' => $violation->violation_id,
                        'student_id' => $violation->student ? $violation->student->student_id : null,
                        'student_name' => $studentName,
                        'grade_level' => $gradeLevel,
                        'violation_type' => $violationType,
                        'description' => $description,
                        'date' => Carbon::parse($violation->violation_date)->format('Y-m-d'),
                        'violation_date' => $violation->violation_date,
                        'violation_time' => $violation->violation_time,
                        'status' => $violation->status, // Keep original status
                        'is_recent' => $isRecent,
                        'handled_by' => $violation->handled_by ?? 'adviser'
                    ];
                });

            // Violation Types for Chart - count both pending and in_progress
            $violationTypes = Offense::select(
                    'offense_type',
                    DB::raw('COUNT(tbl_violation_record.violation_id) as count')
                )
                ->leftJoin('tbl_violation_record', function($join) {
                    $join->on('tbl_offense.offense_id', '=', 'tbl_violation_record.offense_id')
                         ->whereIn('tbl_violation_record.status', ['pending', 'in_progress']);
                })
                ->groupBy('tbl_offense.offense_type')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->get();

            // Recent Activity for trend chart (last 5 days) - both pending and in_progress
            $recentDates = [];
            $violationCounts = [];

            for ($i = 4; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateFormatted = $date->format('D'); // Mon, Tue, etc.
                $recentDates[] = $dateFormatted;

                $dateStart = $date->copy()->startOfDay();
                $dateEnd = $date->copy()->endOfDay();

                $violationCounts[] = ViolationRecord::whereIn('status', ['pending', 'in_progress'])
                    ->whereBetween('violation_date', [$dateStart, $dateEnd])
                    ->count();
            }

            $recentActivity = [
                'dates' => $recentDates,
                'violations' => $violationCounts
            ];

            $advisers = Adviser::all();

            return view('prefect.dashboard', array_merge(
                compact(
                    'advisers',
                    'totalStudents',
                    'totalViolations',
                    'weeklyStudents',
                    'weeklyViolations',
                    'pendingViolations',
                    'inProgressViolations',
                    'resolvedViolations',
                    'students',
                    'violations',
                    'violationTypes',
                    'recentActivity'
                ),
                $notificationData
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());

            $notificationData = $this->getNotificationData();

            return view('prefect.dashboard', array_merge([
                'advisers' => collect(),
                'totalStudents' => 0,
                'totalViolations' => 0,
                'weeklyStudents' => 0,
                'weeklyViolations' => 0,
                'pendingViolations' => 0,
                'inProgressViolations' => 0,
                'resolvedViolations' => 0,
                'students' => collect(),
                'violations' => collect(),
                'violationTypes' => collect(),
                'recentActivity' => [
                    'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                    'violations' => [0, 0, 0, 0, 0]
                ]
            ], $notificationData));
        }
    }

    /**
     * Display the notifications page
     */
    public function notifications()
    {
        try {
            // Get notification data
            $notificationData = $this->getNotificationData();

            // Get recent violations for notifications page
            $recentViolations = ViolationRecord::with(['student.adviser', 'offense'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('created_at', '>=', Carbon::now()->subDays(7))
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
                        'status' => $violation->status,
                        'date' => Carbon::parse($violation->violation_date)->format('M d, Y'),
                        'created_at' => Carbon::parse($violation->created_at)->format('M d, Y H:i'),
                        'type' => 'violation'
                    ];
                });

            // Get recent students for notifications page
            $recentStudents = Student::with(['adviser'])
                ->where('status', 'active')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
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
                        'created_at' => Carbon::parse($student->created_at)->format('M d, Y H:i'),
                        'type' => 'student'
                    ];
                });

            // Combine all notifications
            $allNotifications = collect()
                ->merge($recentViolations->map(function($item) {
                    $statusIcon = $item['status'] === 'pending' ? '⏳' : '🔄';
                    $statusText = $item['status'] === 'pending' ? 'Pending' : 'In Progress';

                    $item['title'] = "{$statusIcon} {$statusText} Violation";
                    $item['message'] = "{$item['student_name']} - {$item['violation_type']}";
                    $item['icon'] = 'exclamation-circle';
                    $item['color'] = $item['status'] === 'pending' ? 'warning' : 'info';
                    return $item;
                }))
                ->merge($recentStudents->map(function($item) {
                    $item['title'] = 'New Student Registered';
                    $item['message'] = "{$item['name']} - Grade {$item['grade_level']} {$item['section']}";
                    $item['icon'] = 'user-graduate';
                    $item['color'] = 'student';
                    return $item;
                }))
                ->sortByDesc('created_at')
                ->values();

            return view('prefect.notifications', array_merge(
                compact('allNotifications'),
                $notificationData
            ));

        } catch (\Exception $e) {
            \Log::error('Notifications Error: ' . $e->getMessage());

            $notificationData = $this->getNotificationData();

            return view('prefect.notifications', array_merge([
                'allNotifications' => collect()
            ], $notificationData));
        }
    }
}
