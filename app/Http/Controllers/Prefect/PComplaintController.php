<?php
namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaints; // Make sure you have a Complaint model

class PComplaintController extends Controller
{
    /**
     * Search students for complainant/respondent.
     */
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

    /**
     * Search offenses for complaints.
     */
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

    /**
     * Get sanction for selected offense (optional, for reference).
     */
    public function getSanction(Request $request)
    {
        $offense_id = $request->input('offense_id');
        if (!$offense_id) return "Missing parameters";

        $sanction = DB::table('tbl_offenses_with_sanction')
            ->where('offense_sanc_id', $offense_id)
            ->value('sanction_consequences');

        return $sanction ?: 'No sanction found';
    }

    /**
     * Store multiple complaints
     */
public function store(Request $request)
{

    dd($request->all());

    try {
        // ✅ Debug helper (you can remove this once tested)
        // dd($request->all());

        // ✅ Loop through each group
        foreach ($request->complainant_id as $groupId => $complainants) {
            $respondents = $request->respondent_id[$groupId] ?? [];
            $offenseId   = $request->offense_sanc_id[$groupId] ?? null;
            $date        = $request->complaints_date[$groupId] ?? null;
            $time        = $request->complaints_time[$groupId] ?? null;
            $incident    = $request->complaints_incident[$groupId] ?? null;
            $prefectId   = auth()->prefect_id ?? 1; // or get dynamically

            foreach ($complainants as $compId) {
                foreach ($respondents as $respId) {
                    Complaints::create([
                        'complainant_id'      => $compId,
                        'respondent_id'       => $respId,
                        'prefect_id'          => $prefectId,
                        'offense_sanc_id'     => $offenseId,
                        'complaints_incident' => $incident,
                        'complaints_date'     => $date,
                        'complaints_time'     => $time,
                        'status'              => 'active',
                    ]);
                }
            }
        }

        return redirect()->back()->with('messages', ['✅ All complaints stored successfully!']);
    } catch (\Exception $e) {
        return redirect()->back()->with('messages', [
            '❌ Error saving complaints: ' . $e->getMessage()
        ]);
    }
}
}
