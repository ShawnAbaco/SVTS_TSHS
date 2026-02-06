<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;
use App\Models\Sanction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class POffenseSanctionController extends Controller
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

    // GET OFFENSE STATISTICS
    private function getOffenseStatistics()
    {
        return DB::table('tbl_violation_record as vr')
            ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
            ->select(
                'o.offense_type',
                'o.offense_description',
                DB::raw('COUNT(vr.violation_id) as violation_count'),
                DB::raw('COUNT(DISTINCT vr.violator_id) as unique_students'),
                DB::raw('MAX(vr.violation_date) as last_occurrence'),
                DB::raw('MIN(vr.violation_date) as first_occurrence')
            )
            ->groupBy('o.offense_id', 'o.offense_type', 'o.offense_description')
            ->orderBy('violation_count', 'desc')
            ->get();
    }

    // GET TOP OFFENSES
    private function getTopOffenses($limit = 5)
    {
        return DB::table('tbl_violation_record as vr')
            ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
            ->select(
                'o.offense_type',
                DB::raw('COUNT(vr.violation_id) as count'),
                DB::raw('COUNT(DISTINCT vr.violator_id) as students_affected')
            )
            ->groupBy('o.offense_id', 'o.offense_type')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    // GET OVERALL STATISTICS
    private function getOverallStatistics()
    {
        $totalViolations = DB::table('tbl_violation_record')->count();
        $studentsWithViolations = DB::table('tbl_violation_record')->distinct('violator_id')->count('violator_id');
        $totalStudents = DB::table('tbl_student')->where('status', 'active')->count();
        $totalOffenseTypes = DB::table('tbl_offense')->count();

        $violationRate = $totalStudents > 0 ? round(($studentsWithViolations / $totalStudents) * 100, 2) : 0;

        return [
            'totalViolations' => $totalViolations,
            'studentsWithViolations' => $studentsWithViolations,
            'totalStudents' => $totalStudents,
            'totalOffenseTypes' => $totalOffenseTypes,
            'violationRate' => $violationRate
        ];
    }

    // AJAX: GET OFFENSE DETAILS
    public function getOffenseDetails($offenseType)
{
    try {
        // Double decode to handle double encoding from frontend
        $offenseType = urldecode(urldecode($offenseType));

        \Log::info('Fetching offense details for: ' . $offenseType);

        // Get offense information
        $offense = DB::table('tbl_offense')
            ->where('offense_type', $offenseType)
            ->first();

        if (!$offense) {
            return response()->json([
                'success' => false,
                'error' => 'Offense not found',
                'message' => 'The specified offense type does not exist: ' . $offenseType
            ], 404);
        }

            // Get basic statistics
            $stats = DB::table('tbl_violation_record as vr')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->where('o.offense_type', $offenseType)
                ->select(
                    DB::raw('COUNT(vr.violation_id) as total_violations'),
                    DB::raw('COUNT(DISTINCT vr.violator_id) as students_affected'),
                    DB::raw('MIN(vr.violation_date) as first_occurrence'),
                    DB::raw('MAX(vr.violation_date) as last_occurrence'),
                    DB::raw('COUNT(CASE WHEN vr.status = "pending" THEN 1 END) as pending_count'),
                    DB::raw('COUNT(CASE WHEN vr.status = "resolved" THEN 1 END) as resolved_count')
                )
                ->first();

            // Get recent violations for this offense
            $recentViolations = DB::table('tbl_violation_record as vr')
                ->join('tbl_student as stu', 'vr.violator_id', '=', 'stu.student_id')
                ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->leftJoin('tbl_adviser as a', 'stu.adviser_id', '=', 'a.adviser_id')
                ->where('vr.offense_id', $offense->offense_id)
                ->select(
                    'vr.violation_date',
                    'stu.student_fname',
                    'stu.student_lname',
                    DB::raw('COALESCE(a.adviser_gradelevel, "N/A") as grade_level'),
                    's.sanction_consequences',
                    'vr.status'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->limit(10)
                ->get();

            // Get students who committed this offense multiple times
            $repeatOffenders = DB::table('tbl_violation_record as vr')
                ->join('tbl_student as stu', 'vr.violator_id', '=', 'stu.student_id')
                ->leftJoin('tbl_adviser as a', 'stu.adviser_id', '=', 'a.adviser_id')
                ->where('vr.offense_id', $offense->offense_id)
                ->select(
                    'stu.student_fname',
                    'stu.student_lname',
                    DB::raw('COALESCE(a.adviser_gradelevel, "N/A") as grade_level'),
                    DB::raw('COALESCE(a.adviser_section, "N/A") as section'),
                    DB::raw('COUNT(vr.violation_id) as violation_count'),
                    DB::raw('MAX(vr.violation_date) as last_offense')
                )
                ->groupBy('vr.violator_id', 'stu.student_fname', 'stu.student_lname', 'a.adviser_gradelevel', 'a.adviser_section')
                ->having('violation_count', '>', 1)
                ->orderByDesc('violation_count')
                ->limit(10)
                ->get();

            // Get sanctions distribution
            $sanctionsDistribution = DB::table('tbl_violation_record as vr')
                ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->where('vr.offense_id', $offense->offense_id)
                ->select(
                    's.sanction_consequences',
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('s.sanction_id', 's.sanction_consequences')
                ->orderByDesc('count')
                ->get()
                ->map(function($item) use ($stats) {
                    $item->percentage = $stats->total_violations > 0
                        ? round(($item->count / $stats->total_violations) * 100, 1)
                        : 0;
                    return $item;
                });

            // Get monthly trend
            $monthlyTrend = DB::table('tbl_violation_record')
                ->where('offense_id', $offense->offense_id)
                ->select(
                    DB::raw('YEAR(violation_date) as year'),
                    DB::raw('MONTH(violation_date) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('YEAR(violation_date), MONTH(violation_date)'))
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            // Get common sanctions
            $commonSanctions = DB::table('tbl_violation_record as vr')
                ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->where('vr.offense_id', $offense->offense_id)
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT s.sanction_consequences SEPARATOR "|") as sanctions')
                )
                ->first();

            // Format response data
            $response = [
                'success' => true,
                'offense' => $offense,
                'statistics' => $stats,
                'recentViolations' => $recentViolations,
                'repeatOffenders' => $repeatOffenders,
                'sanctionsDistribution' => $sanctionsDistribution,
                'monthlyTrend' => $monthlyTrend,
                'summary' => [
                    'has_data' => $stats && $stats->total_violations > 0,
                    'total_violations' => $stats ? $stats->total_violations : 0,
                    'students_affected' => $stats ? $stats->students_affected : 0,
                    'pending_cases' => $stats ? $stats->pending_count : 0,
                    'resolved_cases' => $stats ? $stats->resolved_count : 0
                ]
            ];

            \Log::info('Offense details fetched successfully for: ' . $offenseType);
            return response()->json($response);

         } catch (\Exception $e) {
        \Log::error('Offense details error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => 'An error occurred while fetching offense details'
        ], 500);
    }
    }

    // MAIN INDEX METHOD
    public function index()
    {
        // Get enhanced notification data with detailed information
        $notificationData = $this->getNotificationData();

        // Get offenses with sanctions (paginated)
        $offenses = DB::table('tbl_offense_with_sanction_stages as owss')
            ->join('tbl_offense as o', 'owss.offense_id', '=', 'o.offense_id')
            ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
            ->select(
                'o.offense_type',
                'o.offense_description',
                DB::raw('GROUP_CONCAT(DISTINCT s.sanction_consequences ORDER BY owss.owss_id SEPARATOR ", ") as sanctions'),
                DB::raw('MIN(owss.owss_id) as min_id')
            )
            ->groupBy('o.offense_type', 'o.offense_description')
            ->orderBy('min_id', 'ASC')
            ->paginate(10);

        // Get all sanctions for the sanction list (non-paginated)
        $allSanctions = DB::table('tbl_sanction')
            ->where('sanction_consequences', '!=', 'NOT ASSIGNED')
            ->orderBy('sanction_consequences')
            ->get();

        // Get all offenses for the dropdown in offense details panel (non-paginated)
        $allOffenses = DB::table('tbl_offense')
            ->select('offense_id', 'offense_type')
            ->orderBy('offense_type')
            ->get();

        // Get paginated sanctions (for backward compatibility)
        $sanctions = DB::table('tbl_sanction')
            ->where('sanction_consequences', '!=', 'NOT ASSIGNED')
            ->paginate(10);

        // Get statistics
        $offenseStats = $this->getOffenseStatistics();
        $topOffenses = $this->getTopOffenses(5);
        $topViolators = $this->getTopViolators(10);
        $overallStats = $this->getOverallStatistics();

        return view('prefect.offensesandsanctions', array_merge(
            compact(
                'offenses',
                'sanctions',
                'allSanctions',
                'allOffenses',
                'offenseStats',
                'topOffenses',
                'topViolators',
                'overallStats'
            ),
            $notificationData
        ));
    }

    /**
     * Get all offenses for PDF export
     */
    public function getAllOffenses()
    {
        try {
            // Get offenses with sanctions
            $offenses = DB::table('tbl_offense_with_sanction_stages as owss')
                ->join('tbl_offense as o', 'owss.offense_id', '=', 'o.offense_id')
                ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
                ->select(
                    'o.offense_type',
                    'o.offense_description',
                    DB::raw('GROUP_CONCAT(DISTINCT s.sanction_consequences ORDER BY owss.owss_id SEPARATOR ", ") as sanctions')
                )
                ->groupBy('o.offense_id', 'o.offense_type', 'o.offense_description')
                ->orderBy('o.offense_type')
                ->get();

            return response()->json($offenses);
        } catch (\Exception $e) {
            \Log::error('Error fetching all offenses: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch offenses',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all sanctions for PDF export
     */
    public function getAllSanctions()
    {
        try {
            $sanctions = DB::table('tbl_sanction')
                ->select('sanction_consequences', 'sanction_description')
                ->where('sanction_consequences', '!=', 'NOT ASSIGNED')
                ->orderBy('sanction_consequences')
                ->get();

            return response()->json($sanctions);
        } catch (\Exception $e) {
            \Log::error('Error fetching all sanctions: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch sanctions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // GET TOP VIOLATORS
    private function getTopViolators($limit = 10)
    {
        return DB::table('tbl_violation_record as vr')
            ->join('tbl_student as s', 'vr.violator_id', '=', 's.student_id')
            ->leftJoin('tbl_adviser as a', 's.adviser_id', '=', 'a.adviser_id')
            ->select(
                's.student_id',
                's.student_fname',
                's.student_lname',
                'a.adviser_gradelevel',
                'a.adviser_section',
                DB::raw('COUNT(vr.violation_id) as violation_count'),
                DB::raw('COUNT(CASE WHEN vr.status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(CASE WHEN vr.status = "resolved" THEN 1 END) as resolved_count'),
                DB::raw('MAX(vr.violation_date) as last_offense')
            )
            ->groupBy('s.student_id', 's.student_fname', 's.student_lname', 'a.adviser_gradelevel', 'a.adviser_section')
            ->orderByDesc('violation_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get student violations for modal
     */
    public function getStudentViolations($studentId)
    {
        try {
            \Log::info('Fetching violations for student ID: ' . $studentId);

            // Get student info
            $student = DB::table('tbl_student as s')
                ->leftJoin('tbl_adviser as a', 's.adviser_id', '=', 'a.adviser_id')
                ->where('s.student_id', $studentId)
                ->select(
                    's.student_id',
                    's.student_fname',
                    's.student_lname',
                    'a.adviser_gradelevel as grade_level',
                    'a.adviser_section as section'
                )
                ->first();

            if (!$student) {
                \Log::error('Student not found: ' . $studentId);
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Get student violations
            $violations = DB::table('tbl_violation_record as vr')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->leftJoin('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->where('vr.violator_id', $studentId)
                ->select(
                    'vr.violation_id',
                    'vr.violation_date',
                    'o.offense_type',
                    's.sanction_consequences as sanction',
                    'vr.status',
                    'vr.description'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->get();

            // Get counts
            $totalViolations = $violations->count();
            $pendingViolations = $violations->where('status', 'pending')->count();
            $resolvedViolations = $violations->where('status', 'resolved')->count();

            \Log::info('Found ' . $totalViolations . ' violations for student: ' . $studentId);

            return response()->json([
                'success' => true,
                'student' => $student,
                'violations' => $violations,
                'total_violations' => $totalViolations,
                'pending_violations' => $pendingViolations,
                'resolved_violations' => $resolvedViolations
            ]);

        } catch (\Exception $e) {
            \Log::error('Student violations error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Server error',
                'message' => 'An error occurred while fetching student violations'
            ], 500);
        }
    }

    /**
     * Get all violation records for report generation
     */
    public function getAllViolations()
    {
        try {
            $violations = DB::table('tbl_violation_record as vr')
                ->join('tbl_student as s', 'vr.violator_id', '=', 's.student_id')
                ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
                ->leftJoin('tbl_sanction as sn', 'vr.sanction_id', '=', 'sn.sanction_id')
                ->leftJoin('tbl_adviser as a', 's.adviser_id', '=', 'a.adviser_id')
                ->select(
                    'vr.violation_date',
                    's.student_fname',
                    's.student_lname',
                    'o.offense_type',
                    'sn.sanction_consequences',
                    'vr.status',
                    'a.adviser_gradelevel',
                    'a.adviser_section'
                )
                ->orderBy('vr.violation_date', 'desc')
                ->get();

            return response()->json($violations);
        } catch (\Exception $e) {
            \Log::error('Error fetching all violations: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch violations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

   /**
 * Get all violations for a specific offense type with filters - UPDATED
 */
public function getAllViolationsByOffense($offenseType, Request $request)
{
    try {
        // Single decode is enough since we're only encoding once in JS
        $offenseType = urldecode($offenseType);

        \Log::info('Fetching all violations for offense: ' . $offenseType . ' with filters: ' . json_encode($request->all()));

        // Get offense information
        $offense = DB::table('tbl_offense')
            ->where('offense_type', $offenseType)
            ->first();

        if (!$offense) {
            return response()->json([
                'success' => false,
                'message' => 'Offense not found: ' . $offenseType
            ], 404);
        }

        // Start building query
        $query = DB::table('tbl_violation_record as vr')
            ->join('tbl_student as stu', 'vr.violator_id', '=', 'stu.student_id')
            ->join('tbl_offense as o', 'vr.offense_id', '=', 'o.offense_id')
            ->leftJoin('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
            ->leftJoin('tbl_adviser as a', 'stu.adviser_id', '=', 'a.adviser_id')
            ->where('vr.offense_id', $offense->offense_id)
            ->select(
                'vr.violation_id',
                'vr.violation_date',
                'vr.status',
                DB::raw('TIME(vr.created_at) as time'),
                'stu.student_fname',
                'stu.student_lname',
                'o.offense_type',
                's.sanction_consequences',
                DB::raw('COALESCE(a.adviser_gradelevel, "N/A") as grade_level'),
                DB::raw('COALESCE(a.adviser_section, "N/A") as section')
            );

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('vr.violation_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('vr.violation_date', '<=', $request->end_date);
        }

        if ($request->has('time_range') && $request->time_range) {
            switch ($request->time_range) {
                case 'morning':
                    $query->whereRaw('TIME(vr.created_at) BETWEEN "06:00:00" AND "11:59:59"');
                    break;
                case 'afternoon':
                    $query->whereRaw('TIME(vr.created_at) BETWEEN "12:00:00" AND "17:59:59"');
                    break;
                case 'evening':
                    $query->whereRaw('TIME(vr.created_at) BETWEEN "18:00:00" AND "23:59:59"');
                    break;
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stu.student_fname', 'LIKE', "%{$search}%")
                  ->orWhere('stu.student_lname', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(stu.student_fname, ' ', stu.student_lname) LIKE ?", ["%{$search}%"]);
            });
        }

        // NEW: Status filter
        if ($request->has('status') && $request->status) {
            $query->where('vr.status', $request->status);
        }

        // NEW: Sanction filter
        if ($request->has('sanction') && $request->sanction) {
            $query->where('s.sanction_consequences', $request->sanction);
        }

        // Order by violation date
        $query->orderBy('vr.violation_date', 'desc');

        // Get all violations
        $violations = $query->get();

        // Get statistics (apply same filters)
        $statsQuery = clone $query;

        $totalViolations = $violations->count();
        $uniqueStudents = $violations->unique('violator_id')->count();

        // Count by status
        $pendingCount = $violations->where('status', 'pending')->count();
        $resolvedCount = $violations->where('status', 'resolved')->count();
        $inProgressCount = $violations->where('status', 'in_progress')->count();
        $noncompliantCount = $violations->where('status', 'noncompliant')->count();
        $dismissedCount = $violations->where('status', 'dismissed')->count();

        $statistics = [
            'total_violations' => $totalViolations,
            'unique_students' => $uniqueStudents,
            'pending_count' => $pendingCount,
            'resolved_count' => $resolvedCount,
            'in_progress_count' => $inProgressCount,
            'noncompliant_count' => $noncompliantCount,
            'dismissed_count' => $dismissedCount
        ];

        \Log::info('Found ' . $totalViolations . ' violations for offense: ' . $offenseType);

        return response()->json([
            'success' => true,
            'offense' => $offense,
            'violations' => $violations,
            'statistics' => $statistics,
            'filters' => $request->all()
        ]);

    } catch (\Exception $e) {
        \Log::error('Get all violations error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => 'An error occurred while fetching violations'
        ], 500);
    }
}

public function getAllSanctionsForDropdown()
    {
        try {
            // Get all sanctions from tbl_sanction (not from sanction stages)
            $sanctions = DB::table('tbl_sanction')
                ->select('sanction_consequences')
                ->where('sanction_consequences', '!=', 'NOT ASSIGNED')
                ->whereNotNull('sanction_consequences')
                ->where('sanction_consequences', '!=', '')
                ->distinct()
                ->orderBy('sanction_consequences')
                ->get()
                ->map(function($sanction) {
                    return [
                        'value' => $sanction->sanction_consequences,
                        'display' => $sanction->sanction_consequences
                    ];
                });

            return response()->json([
                'success' => true,
                'sanctions' => $sanctions
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching sanctions dropdown: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch sanctions'
            ], 500);
        }
    }

    /**
     * Get sanctions used in violations for a specific offense (for dropdown)
     */
    public function getSanctionsByOffense($offenseType)
    {
        try {
            $offenseType = urldecode($offenseType);

            $offense = DB::table('tbl_offense')
                ->where('offense_type', $offenseType)
                ->first();

            if (!$offense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offense not found'
                ], 404);
            }

            // Get sanctions that have been used for this specific offense
            $sanctions = DB::table('tbl_violation_record as vr')
                ->join('tbl_sanction as s', 'vr.sanction_id', '=', 's.sanction_id')
                ->where('vr.offense_id', $offense->offense_id)
                ->where('s.sanction_consequences', '!=', 'NOT ASSIGNED')
                ->whereNotNull('s.sanction_consequences')
                ->where('s.sanction_consequences', '!=', '')
                ->select('s.sanction_consequences')
                ->distinct()
                ->orderBy('s.sanction_consequences')
                ->get()
                ->map(function($sanction) {
                    return [
                        'value' => $sanction->sanction_consequences,
                        'display' => $sanction->sanction_consequences
                    ];
                });

            return response()->json([
                'success' => true,
                'sanctions' => $sanctions
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching sanctions by offense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch sanctions'
            ], 500);
        }
    }

    /**
     * Get all violation statuses for dropdown filter
     */
    public function getViolationStatuses()
    {
        try {
            $statuses = ViolationRecord::select('status')
                ->distinct()
                ->orderBy('status')
                ->get()
                ->map(function($item) {
                    // Format status display names
                    $statusName = $item->status;
                    $displayName = '';

                    switch ($statusName) {
                        case 'pending':
                            $displayName = 'Pending - Awaiting Action';
                            break;
                        case 'in_progress':
                            $displayName = 'In Progress - Being Handled';
                            break;
                        case 'resolved':
                            $displayName = 'Resolved - Issue Settled';
                            break;
                        case 'noncompliant':
                            $displayName = 'Noncompliant - Student Failed to Comply';
                            break;
                        case 'dismissed':
                            $displayName = 'Dismissed - Not Substantiated';
                            break;
                        default:
                            $displayName = ucfirst($statusName);
                    }

                    return [
                        'value' => $statusName,
                        'display' => $displayName
                    ];
                });

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching statuses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch statuses'
            ], 500);
        }
    }
}
