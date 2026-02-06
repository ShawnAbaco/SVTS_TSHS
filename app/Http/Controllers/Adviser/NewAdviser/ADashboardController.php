<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

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
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ADashboardController extends Controller
{
    // Helper method to get notification counts and data
    private function getNotificationData()
    {
        // Get recent violations (last 24 hours) - both pending and in_progress
        $newViolations = ViolationRecord::with(['student.adviser', 'offense'])
            ->whereIn('status', ['pending', 'in_progress']) // Include both statuses
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
                    'created_at' => $violation->created_at,
                    'status' => $violation->status
                ];
            });

        $newViolationsCount = $newViolations->count();

        // Get recent students (last 24 hours)
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

        $newParentsCount = 0; // You can add parent model when available

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

public function dashboard()
{
    try {
        // Get the logged-in adviser's ID
        $adviserId = Auth::guard('adviser')->id();

        // Get notification data
        $notificationData = $this->getNotificationData();

        // Total Students for this adviser
        $totalStudents = Student::where('status', 'active')
            ->where('adviser_id', $adviserId)
            ->count();

        // Weekly Students (new students in the last 7 days) for this adviser
        $weeklyStudents = Student::where('status', 'active')
            ->where('adviser_id', $adviserId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        // Total Violations for this adviser's students (both pending and in_progress)
        $totalViolations = ViolationRecord::whereIn('status', ['pending', 'in_progress'])
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->count();

        // Weekly Violations for this adviser's students (violations in the last 7 days)
        $weeklyViolations = ViolationRecord::whereIn('status', ['pending', 'in_progress'])
            ->where('violation_date', '>=', Carbon::now()->subDays(7))
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->count();

        // Pending Violations for this adviser's students (only pending status)
        $pendingViolations = ViolationRecord::where('status', 'pending')
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->count();

        // In Progress Violations for this adviser's students
        $inProgressViolations = ViolationRecord::where('status', 'in_progress')
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->count();

        // Resolved Violations for this adviser's students
        $resolvedViolations = ViolationRecord::whereIn('status', ['resolved', 'completed'])
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
            ->count();

        // Get all active students for this adviser for modal
        $students = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('adviser_id', $adviserId)
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

        // Get violations for this adviser's students with student details for table
        $violations = ViolationRecord::with(['student.adviser', 'offense'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId);
            })
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
                    'status' => $violation->status,
                    'is_recent' => $isRecent,
                    'handled_by' => $violation->handled_by ?? 'adviser'
                ];
            });

        // Violation Types for Chart - only for this adviser's students
        $violationTypes = Offense::select(
                'offense_type',
                DB::raw('COUNT(tbl_violation_record.violation_id) as count')
            )
            ->leftJoin('tbl_violation_record', function($join) use ($adviserId) {
                $join->on('tbl_offense.offense_id', '=', 'tbl_violation_record.offense_id')
                     ->whereIn('tbl_violation_record.status', ['pending', 'in_progress']);
            })
            ->leftJoin('tbl_student', function($join) use ($adviserId) {
                $join->on('tbl_violation_record.student_id', '=', 'tbl_student.student_id')
                     ->where('tbl_student.adviser_id', $adviserId);
            })
            ->groupBy('tbl_offense.offense_type')
            ->having('count', '>', 0)
            ->orderBy('count', 'desc')
            ->get();

        // Recent Activity for trend chart (last 5 days) - only for this adviser's students
        $recentDates = [];
        $violationCounts = [];

        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateFormatted = $date->format('D');
            $recentDates[] = $dateFormatted;

            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();

            $violationCounts[] = ViolationRecord::whereIn('status', ['pending', 'in_progress'])
                ->whereBetween('violation_date', [$dateStart, $dateEnd])
                ->whereHas('student', function($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->count();
        }

        $recentActivity = [
            'dates' => $recentDates,
            'violations' => $violationCounts
        ];

        // Get only the logged-in adviser's details
        $adviser = Adviser::find($adviserId);

        return view('adviser.NewAdviser.dashboard', array_merge(
            compact(
                'adviser',
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

        return view('adviser.NewAdviser.dashboard', array_merge([
            'adviser' => null,
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

            return view('adviser.NewAdviser.notifications', array_merge(
                compact('allNotifications'),
                $notificationData
            ));

        } catch (\Exception $e) {
            \Log::error('Notifications Error: ' . $e->getMessage());

            $notificationData = $this->getNotificationData();

            return view('adviser.NewAdviser.notifications', array_merge([
                'allNotifications' => collect()
            ], $notificationData));
        }
    }
}
