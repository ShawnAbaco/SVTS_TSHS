<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaints;

class ComplaintController extends Controller
{

    public function create()
    {
        return view('prefect.create-complaints'); // Blade file
    }

    // Store multiple complaints
    public function store(Request $request)
    {
        $student_ids = $request->input('student_id', []);
        $complaint_ids = $request->input('complaint', []);
        $dates = $request->input('date', []);
        $times = $request->input('time', []);
        $incidents = $request->input('incident', []);

        $messages = [];

        foreach ($complaint_ids as $i => $complaint_id) {
            $student_id = $student_ids[$i] ?? null;
            $date       = $dates[$i] ?? null;
            $time       = $times[$i] ?? null;
            $incident   = $incidents[$i] ?? null;

            if (!$student_id || !$complaint_id) continue;

            DB::table('tbl_complaints')->insert([
                'student_id'        => $student_id,
                'complaint_type_id' => $complaint_id,
                'complaint_date'    => $date,
                'complaint_time'    => $time,
                'complaint_details' => $incident
            ]);

            $messages[] = "✅ Complaint recorded for student ID: $student_id";
        }

        return back()->with('messages', $messages);
    }

    // AJAX search for students
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

    // AJAX search for complaint types
    public function searchTypes(Request $request)
    {
        $query = $request->input('query', '');
        $types = DB::table('tbl_complaint_types')
            ->select('type_name', DB::raw('MIN(complaint_type_id) as complaint_type_id'))
            ->where('type_name', 'like', "%$query%")
            ->groupBy('type_name')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($types as $type) {
            $html .= "<div class='complaint-item' data-id='{$type->complaint_type_id}'>{$type->type_name}</div>";
        }

        return $html ?: '<div>No results found</div>';
    }

    // Optional: Get default action or recommendation for complaint (if needed)
    public function getAction(Request $request)
    {
        $student_id = $request->input('student_id');
        $complaint_id = $request->input('complaint_id');

        if (!$student_id || !$complaint_id) return "Missing parameters";

        $previous_count = DB::table('tbl_complaints')
            ->where('student_id', $student_id)
            ->where('complaint_type_id', $complaint_id)
            ->count();

        // Example: Determine stage/action based on previous count
        $action = DB::table('tbl_complaint_types')
            ->where('complaint_type_id', $complaint_id)
            ->value('default_action');

        return $action ?: 'No action found';
    }


    // OLDDDDDDDDDDDDDDDDDDDDDDDDD
//     public function storeComplaint(Request $request)
// {
//     $validated = $request->validate([
//         'complainant_id'       => 'required|exists:tbl_student,student_id',
//         'respondent_id'        => 'required|exists:tbl_student,student_id',
//         'prefect_id'           => 'required|exists:tbl_prefect_of_discipline,prefect_id',
//         'offense_sanc_id'      => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
//         'complaints_incident'  => 'required|string',
//         'complaints_date'      => 'required|date',
//         'complaints_time'      => 'required',
//     ]);

//     Complaints::create($validated);

//     return redirect()->route('complaints.index')
//                      ->with('success', 'Complaint created successfully.');
// }
// public function updateComplaint(Request $request, $id)
// {
//     // Validate input
//     $validated = $request->validate([
//         'complainant_id'       => 'required|exists:tbl_student,student_id',
//         'respondent_id'        => 'required|exists:tbl_student,student_id',
//         'prefect_id'           => 'required|exists:tbl_prefect_of_discipline,prefect_id',
//         'offense_sanc_id'      => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
//         'complaints_incident'  => 'required|string',
//         'complaints_date'      => 'required|date',
//         'complaints_time'      => 'required',
//     ]);

//     $complaint = Complaints::findOrFail($id);

//     $complaint->update($validated);

//     return redirect()->route('complaints.index')
//                      ->with('success', 'Complaint updated successfully.');
// }

}
