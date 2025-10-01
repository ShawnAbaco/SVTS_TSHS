<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationRecord;

class ViolationRecordController extends Controller
{
// NEWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW

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

    public function getSanction(Request $request)
    {
        $student_id = $request->input('student_id');
        $offense_id = $request->input('offense_id');

        if (!$student_id || !$offense_id) return "Missing parameters";

        $previous_count = DB::table('tbl_violation_record')
            ->where('violator_id', $student_id)
            ->where('offense_sanc_id', $offense_id)
            ->count();

        $stage_number = $previous_count + 1;

        $sanction = DB::table('tbl_offenses_with_sanction')
            ->where('offense_sanc_id', $offense_id)
            ->where('stage_number', $stage_number)
            ->value('sanction_consequences');

        if (!$sanction) {
            $sanction = DB::table('tbl_offenses_with_sanction')
                ->where('offense_sanc_id', $offense_id)
                ->orderByDesc('stage_number')
                ->value('sanction_consequences');
        }

        return $sanction ?: 'No sanction found';
    }


//NEWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW FOR ADVISER

 public function Acreate()
    {
        return view('adviser.create-violation'); // Blade file
    }


    public function Astore(Request $request)
    {
        $student_ids = $request->input('student_id', []);
        $offense_ids = $request->input('offense', []);
        $dates       = $request->input('date', []);
        $times       = $request->input('time', []);
        $incidents   = $request->input('incident', []);

        $messages = [];

        foreach ($offense_ids as $i => $offense_id) {
            $student_id = $student_ids[$i] ?? null;
            $date       = $dates[$i] ?? null;
            $time       = $times[$i] ?? null;
            $incident   = $incidents[$i] ?? null;

            if (!$student_id || !$offense_id) continue;

            $previous_count = DB::table('tbl_violation_record')
                ->where('student_id', $student_id)
                ->where('offense_id', $offense_id)
                ->count();

            $stage_number = $previous_count + 1;

            $sanction_row = DB::table('tbl_offenses_with_sanction')
                ->where('offense_sanc_id', $offense_id)
                ->where('stage_number', $stage_number)
                ->first();

            if (!$sanction_row) {
                $sanction_row = DB::table('tbl_offenses_with_sanction')
                    ->where('offense_sanc_id', $offense_id)
                    ->orderByDesc('stage_number')
                    ->first();
            }

            $sanction = $sanction_row->sanction_consequences ?? 'No sanction found';

            DB::table('tbl_violation_record')->insert([
                'student_id' => $student_id,
                'offense_id' => $offense_id,
                'violation_date' => $date,
                'violation_time' => $time,
                'violation_details' => $incident
            ]);

            $messages[] = "✅ Violation recorded. Sanction: $sanction";
        }

        return back()->with('messages', $messages);
    }

    public function AsearchStudents(Request $request)
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

    public function AsearchOffenses(Request $request)
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

    public function AgetSanction(Request $request)
    {
        $student_id = $request->input('student_id');
        $offense_id = $request->input('offense_id');

        if (!$student_id || !$offense_id) return "Missing parameters";

        $previous_count = DB::table('tbl_violation_record')
            ->where('violator_id', $student_id)
            ->where('offense_sanc_id', $offense_id)
            ->count();

        $stage_number = $previous_count + 1;

        $sanction = DB::table('tbl_offenses_with_sanction')
            ->where('offense_sanc_id', $offense_id)
            ->where('stage_number', $stage_number)
            ->value('sanction_consequences');

        if (!$sanction) {
            $sanction = DB::table('tbl_offenses_with_sanction')
                ->where('offense_sanc_id', $offense_id)
                ->orderByDesc('stage_number')
                ->value('sanction_consequences');
        }

        return $sanction ?: 'No sanction found';
    }







// OLDDDDDDDDDDDDDDDDDDDDDD

 // Store a new violation
// public function storeViolation(Request $request)
// {
//     $request->validate([
//         'student_id' => 'required|exists:tbl_student,student_id',
//         'offense_sanc_id' => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
//         'violation_incident' => 'required|string',
//         'violation_date' => 'required|date',
//         'violation_time' => 'required',
//     ]);

//     $violation = ViolationRecord::create([
//         'violator_id' => $request->student_id,
//         'prefect_id' => 1,
//         'offense_sanc_id' => $request->offense_sanc_id,
//         'violation_incident' => $request->violation_incident,
//         'violation_date' => $request->violation_date,
//         'violation_time' => $request->violation_time,
//     ]);


//     $violation = ViolationRecord::with([
//         'student.parent',
//         'offense'
//     ])->find($violation->violation_id);

//     return response()->json([
//         'success' => true,
//         'violation' => $violation
//     ]);
// }


//     // Update a violation
//     public function updateViolation(Request $request, $id)
//     {
//         $violation = ViolationRecord::findOrFail($id);

//         $request->validate([
//             'student_id' => 'required|exists:tbl_student,student_id',
//             'offense_sanc_id' => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
//             'violation_incident' => 'required|string',
//             'violation_date' => 'required|date',
//             'violation_time' => 'required',
//         ]);

//         $violation->update([
//             'student_id' => $request->student_id,
//             'offense_sanc_id' => $request->offense_sanc_id,
//             'violation_incident' => $request->violation_incident,
//             'violation_date' => $request->violation_date,
//             'violation_time' => $request->violation_time,
//         ]);

//         return response()->json(['success' => true, 'violation' => $violation]);
//     }

//     // Delete a violation
//     public function destroyViolation($id)
//     {
//         $violation = ViolationRecord::findOrFail($id);
//         $violation->delete();

//         return response()->json(['success' => true]);
//     }

}
