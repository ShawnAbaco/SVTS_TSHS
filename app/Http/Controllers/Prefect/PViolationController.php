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
        // Get today's date for reference
$today = Carbon::today();

// Calculate date ranges based on the violation_date field from the database
$startOfWeek = DB::table('tbl_violation_record')->min('violation_date');
$startOfWeek = $startOfWeek ? Carbon::parse($startOfWeek)->startOfWeek() : $today->startOfWeek();
$endOfWeek = $startOfWeek->copy()->endOfWeek();

$startOfMonth = DB::table('tbl_violation_record')->min('violation_date');
$startOfMonth = $startOfMonth ? Carbon::parse($startOfMonth)->startOfMonth() : $today->startOfMonth();
$endOfMonth = $startOfMonth->copy()->endOfMonth();

// Daily violations (for today)
$dailyViolations = DB::table('tbl_violation_record')
    ->whereDate('violation_date', $today)
    ->count();

// Weekly violations (for the week of the earliest violation)
$weeklyViolations = DB::table('tbl_violation_record')
    ->whereBetween('violation_date', [$startOfWeek, $endOfWeek])
    ->count();

// Monthly violations (for the month of the earliest violation)
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
            'today',
            'startOfWeek',
            'startOfWeek',
            'endOfWeek',
            'startOfMonth',
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


// public function edit($id)
//     {
//         $violation = ViolationRecord::with(['student', 'offense'])->findOrFail($id);

//         return response()->json([
//             'violation' => $violation,
//             'student_id' => $violation->student->student_id,
//             'student_name' => $violation->student->student_fname . ' ' . $violation->student->student_lname,
//             'offense_type' => $violation->offense->offense_type,
//             'sanction_consequences' => $violation->offense->sanction_consequences,
//             'violation_incident' => $violation->violation_incident,
//             'violation_date' => $violation->violation_date->format('Y-m-d'),
//             'violation_time' => $violation->violation_time->format('H:i'),
//         ]);
//     }

    // Update Violation
    // Update Violation
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


    // public function store(Request $request)
    // {
    //     $student_ids = $request->input('student_id', []);
    //     $offense_ids = $request->input('offense', []);
    //     $dates       = $request->input('date', []);
    //     $times       = $request->input('time', []);
    //     $incidents   = $request->input('incident', []);

    //     $messages = [];

    //     foreach ($offense_ids as $i => $offense_id) {
    //         $student_id = $student_ids[$i] ?? null;
    //         $date       = $dates[$i] ?? null;
    //         $time       = $times[$i] ?? null;
    //         $incident   = $incidents[$i] ?? null;

    //         if (!$student_id || !$offense_id) continue;

    //         $previous_count = DB::table('tbl_violation_record')
    //             ->where('student_id', $student_id)
    //             ->where('offense_id', $offense_id)
    //             ->count();

    //         $stage_number = $previous_count + 1;

    //         $sanction_row = DB::table('tbl_offenses_with_sanction')
    //             ->where('offense_sanc_id', $offense_id)
    //             ->where('stage_number', $stage_number)
    //             ->first();

    //         if (!$sanction_row) {
    //             $sanction_row = DB::table('tbl_offenses_with_sanction')
    //                 ->where('offense_sanc_id', $offense_id)
    //                 ->orderByDesc('stage_number')
    //                 ->first();
    //         }

    //         $sanction = $sanction_row->sanction_consequences ?? 'No sanction found';

    //         DB::table('tbl_violation_record')->insert([
    //             'student_id' => $student_id,
    //             'offense_id' => $offense_id,
    //             'violation_date' => $date,
    //             'violation_time' => $time,
    //             'violation_details' => $incident
    //         ]);

    //         $messages[] = "✅ Violation recorded. Sanction: $sanction";
    //     }

    //     return back()->with('messages', $messages);
    // }

    // public function searchStudents(Request $request)
    // {
    //     $query = $request->input('query', '');
    //     $students = DB::table('tbl_student')
    //         ->where('student_fname', 'like', "%$query%")
    //         ->orWhere('student_lname', 'like', "%$query%")
    //         ->limit(10)
    //         ->get();

    //     $html = '';
    //     foreach ($students as $student) {
    //         $name = $student->student_fname . ' ' . $student->student_lname;
    //         $html .= "<div class='student-item' data-id='{$student->student_id}'>$name</div>";
    //     }

    //     return $html ?: '<div>No students found</div>';
    // }

    // public function searchOffenses(Request $request)
    // {
    //     $query = $request->input('query', '');
    //     $offenses = DB::table('tbl_offenses_with_sanction')
    //         ->select('offense_type', DB::raw('MIN(offense_sanc_id) as offense_sanc_id'))
    //         ->where('offense_type', 'like', "%$query%")
    //         ->groupBy('offense_type')
    //         ->limit(10)
    //         ->get();

    //     $html = '';
    //     foreach ($offenses as $offense) {
    //         $html .= "<div class='offense-item' data-id='{$offense->offense_sanc_id}'>{$offense->offense_type}</div>";
    //     }

    //     return $html ?: '<div>No results found</div>';
    // }

    // public function getSanction(Request $request)
    // {
    //     $student_id = $request->input('student_id');
    //     $offense_id = $request->input('offense_id');

    //     if (!$student_id || !$offense_id) return "Missing parameters";

    //     $previous_count = DB::table('tbl_violation_record')
    //         ->where('violator_id', $student_id)
    //         ->where('offense_sanc_id', $offense_id)
    //         ->count();

    //     $stage_number = $previous_count + 1;

    //     $sanction = DB::table('tbl_offenses_with_sanction')
    //         ->where('offense_sanc_id', $offense_id)
    //         ->where('stage_number', $stage_number)
    //         ->value('sanction_consequences');

    //     if (!$sanction) {
    //         $sanction = DB::table('tbl_offenses_with_sanction')
    //             ->where('offense_sanc_id', $offense_id)
    //             ->orderByDesc('stage_number')
    //             ->value('sanction_consequences');
    //     }

    //     return $sanction ?: 'No sanction found';
    // }




}
