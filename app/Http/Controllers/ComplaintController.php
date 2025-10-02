<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaints;
use Illuminate\Support\Facades\Log;


class ComplaintController extends Controller
{
    
    // AJAX search for students
    public function searchStudents(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return '<div class="no-results">Type at least 2 characters</div>';
        }

        $students = DB::table('tbl_student')
            ->select('student_id', 'student_fname', 'student_lname')
            ->where(function($q) use ($query) {
                $q->where('student_fname', 'like', "%$query%")
                  ->orWhere('student_lname', 'like', "%$query%")
                  ->orWhere(DB::raw("CONCAT(student_fname, ' ', student_lname)"), 'like', "%$query%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($students as $student) {
            $name = $student->student_fname . ' ' . $student->student_lname;
            $html .= "<div class='student-item' data-id='{$student->student_id}'>$name</div>";
        }

        return $html ?: '<div class="no-results">No students found</div>';
    }

    // AJAX search for offenses
    public function searchOffenses(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return '<div class="no-results">Type at least 2 characters</div>';
        }

        $offenses = DB::table('tbl_offenses_with_sanction')
            ->select('offense_sanc_id', 'offense_type', 'offense_description')
            ->where(function($q) use ($query) {
                $q->where('offense_type', 'like', "%$query%")
                  ->orWhere('offense_description', 'like', "%$query%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($offenses as $offense) {
            $html .= "<div class='offense-item' data-id='{$offense->offense_sanc_id}' title='{$offense->offense_description}'>{$offense->offense_type}</div>";
        }

        return $html ?: '<div class="no-results">No offenses found</div>';
    }
}