<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\ViolationRecord;
use App\Models\Complaints;
use App\Models\Offense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ADashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $adviserId = Auth::guard('adviser')->id();

            if (!$adviserId) {
                return redirect()->route('adviser.login')->with('error', 'Please log in again.');
            }

            // Total Students (only those under this adviser)
            $totalStudents = Student::where('status', 'active')
                ->where('adviser_id', $adviserId)
                ->count();

            // Total Violations (only students under this adviser)
            $totalViolations = ViolationRecord::where('status', 'active')
                ->whereHas('student', function ($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->count();

            // Total Complaints (only students under this adviser)
            $totalComplaints = Complaints::where('status', 'active')
                ->whereHas('complainant', function ($query) use ($adviserId) {
                    $query->where('adviser_id', $adviserId);
                })
                ->count();

            // Violation Types with counts
            $violationTypes = Offense::select(
                    'tbl_offense.offense_type',
                    DB::raw('COUNT(tbl_violation_record.violation_id) as count')
                )
                ->leftJoin('tbl_violation_record', function($join) {
                    $join->on('tbl_offense.offense_id', '=', 'tbl_violation_record.offense_id')
                         ->where('tbl_violation_record.status', 'active');
                })
                ->leftJoin('tbl_student', 'tbl_violation_record.violator_id', '=', 'tbl_student.student_id')
                ->where('tbl_student.adviser_id', $adviserId)
                ->groupBy('tbl_offense.offense_type')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->get();

            // Recent Activity (last 7 days)
            $recentDates = [];
            $violationCounts = [];
            $complaintCounts = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateFormatted = $date->format('M j');
                $recentDates[] = $dateFormatted;

                $dateStart = $date->copy()->startOfDay();
                $dateEnd = $date->copy()->endOfDay();

                // Count violations for this adviser's students for this specific day
                $violationCounts[] = ViolationRecord::where('status', 'active')
                    ->whereBetween('violation_date', [$dateStart, $dateEnd])
                    ->whereHas('student', function ($query) use ($adviserId) {
                        $query->where('adviser_id', $adviserId);
                    })
                    ->count();

                // Count complaints for this adviser's students for this specific day
                $complaintCounts[] = Complaints::where('status', 'active')
                    ->whereBetween('complaints_date', [$dateStart, $dateEnd])
                    ->whereHas('complainant', function ($query) use ($adviserId) {
                        $query->where('adviser_id', $adviserId);
                    })
                    ->count();
            }

            $recentActivity = [
                'dates' => $recentDates,
                'violations' => $violationCounts,
                'complaints' => $complaintCounts
            ];

            return view('adviser.dashboard', compact(
                'totalStudents',
                'totalViolations',
                'totalComplaints',
                'violationTypes',
                'recentActivity'
            ));

        } catch (\Exception $e) {
            \Log::error('Adviser Dashboard Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Return with safe default values
            return view('adviser.dashboard', [
                'totalStudents' => 0,
                'totalViolations' => 0,
                'totalComplaints' => 0,
                'violationTypes' => collect(),
                'recentActivity' => ['dates' => [], 'violations' => [], 'complaints' => []]
            ]);
        }
    }
}