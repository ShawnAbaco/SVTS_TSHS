<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;

class PViolationController extends Controller
{

    public function index()
    {
// Get the actual dates from your violation records
$mostRecentViolationDate = DB::table('tbl_violation_record')->max('violation_date');
$earliestViolationDate = DB::table('tbl_violation_record')->min('violation_date');

// Use the most recent violation date for calculations, or today if no records exist
$referenceDate = $mostRecentViolationDate ? Carbon::parse($mostRecentViolationDate) : Carbon::today();

// Calculate date ranges based on the actual violation dates
$today = $referenceDate->copy(); // This is the date from violation_date, not necessarily today
$startOfWeek = $referenceDate->copy()->startOfWeek();
$endOfWeek = $referenceDate->copy()->endOfWeek();
$startOfMonth = $referenceDate->copy()->startOfMonth();
$endOfMonth = $referenceDate->copy()->endOfMonth();

// Daily violations - count violations that happened on the most recent violation date
$dailyViolations = DB::table('tbl_violation_record')
    ->whereDate('violation_date', $today)
    ->count();

// Weekly violations - count violations in the week of the most recent violation
$weeklyViolations = DB::table('tbl_violation_record')
    ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
    ->count();

// Monthly violations - count violations in the month of the most recent violation
$monthlyViolations = DB::table('tbl_violation_record')
    ->whereBetween('violation_date', [$startOfMonth, $endOfMonth])
    ->count();
        // Other data
        $violations = ViolationRecord::with(['student', 'offense'])->paginate(10);
        $offenses = OffensesWithSanction::all();

        // ✅ don’t overwrite $dailyViolations again
        return view('prefect.violation', compact(
            'violations',
            'offenses',
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
        ));
    }


// Store violations
public function store(Request $request)
{
    try {
        foreach ($request->violations as $v) {
            ViolationRecord::create([
                'violator_id' => $v['violator_id'],
                'prefect_id' => auth()->prefect_id ?? 1,
                'offense_sanc_id' => $v['offense_sanc_id'],
                'violation_incident' => $v['violation_incident'],
                'violation_date' => $v['violation_date'],
                'violation_time' => $v['violation_time'],
                'status' => 'active'
            ]);
        }
        return redirect()->route('violations.index')->with('success', '✅ All violations stored successfully!');
    } catch (\Exception $e) {
        return redirect()->route('violations.index')->with('error', '❌ Error saving violations: '.$e->getMessage());
    }
}


public function update(Request $request, $violationId)
{
    // Validation
    $validator = Validator::make($request->all(), [
        'violator_id'        => 'required|exists:tbl_student,student_id',
        'offense_sanc_id'    => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
        'violation_incident' => 'required|string|max:255',
        'violation_date'     => 'required|date',
        'violation_time'     => 'required|date_format:H:i',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput()
                         ->with('error', '❌ Please correct the errors and try again.');
    }

    try {
        $violation = ViolationRecord::findOrFail($violationId);

        $violation->violator_id        = $request->input('violator_id'); // <--- corrected
        $violation->offense_sanc_id    = $request->input('offense_sanc_id');
        $violation->violation_incident = $request->input('violation_incident');
        $violation->violation_date     = $request->input('violation_date');
        $violation->violation_time     = $request->input('violation_time');

        $violation->save();

        return redirect()->route('violations.index')
                         ->with('success', '✅ Violation updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', '❌ Error updating violation: ' . $e->getMessage())
                         ->withInput();
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
        $offenses = DB::table('tbl_offenses_with_sanction')
            ->select('offense_type', DB::raw('MIN(offense_sanc_id) as offense_sanc_id'))
            ->where('offense_type', 'like', "%$query%")
            ->groupBy('offense_type')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($offenses as $offense) {
            $html .= "<div class='offense-item' data-id='{$offense->offense_sanc_id}'>{$offense->offense_type}</div>";
        }
        return $html ?: '<div>No results found</div>';
    }


 public function create()
    {
        return view('prefect.create-violation'); // Blade file
    }




}
