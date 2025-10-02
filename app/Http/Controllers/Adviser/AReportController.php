<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;

class AReportController extends Controller
{


        public function reports()
    {

        $adviserId = Auth::guard('adviser')->id();
        $students = Student::where('adviser_id', $adviserId)->with('violations')->get();

        return view('adviser.reports', compact('students'));
    }

    
    
public function getReportData($reportId)
{
$data = collect();
$adviserId = Auth::guard('adviser')->id();

switch ($reportId) {
    case 1: // Anecdotal Records per Complaint Case
        $data = collect(DB::select("
            SELECT
                ca.comp_anec_id AS anecdotal_id,
                CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                ca.comp_anec_solution AS solution,
                ca.comp_anec_recommendation AS recommendation,
                DATE_FORMAT(ca.comp_anec_date, '%M %d, %Y') AS date_recorded,
                TIME_FORMAT(ca.comp_anec_time, '%h:%i %p') AS time_recorded
            FROM
                tbl_complaints_anecdotal ca
            JOIN
                tbl_complaints c ON ca.complaints_id = c.complaints_id
            JOIN
                tbl_student s1 ON c.complainant_id = s1.student_id
            JOIN
                tbl_student s2 ON c.respondent_id = s2.student_id
    WHERE s1.adviser_id = ? OR s2.adviser_id = ?
", [$adviserId, $adviserId]));
        break;

    case 2: // Anecdotal Records per Violation Case
        $data = collect(DB::select("
            SELECT
                CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                va.violation_anec_solution AS solution,
                va.violation_anec_recommendation AS recommendation,
                DATE_FORMAT(va.violation_anec_date, '%M %d, %Y') AS date,
                TIME_FORMAT(va.violation_anec_time, '%h:%i %p') AS time
            FROM
                tbl_violation_anecdotal va
            JOIN
                tbl_violation_record v ON va.violation_id = v.violation_id
            JOIN
                tbl_student s ON v.violator_id = s.student_id
    WHERE
        s.adviser_id = :adviserId
", ['adviserId' => $adviserId]));
        break;

    case 3: // Appointments Scheduled for Complaints
        $data = collect(DB::select("
            SELECT
                ca.comp_app_id AS appointment_id,
                CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                DATE_FORMAT(ca.comp_app_date, '%M %d, %Y') AS appointment_date,
                ca.comp_app_status AS appointment_status
            FROM
                tbl_complaints_appointment ca
            JOIN
                tbl_complaints c ON ca.complaints_id = c.complaints_id
            JOIN
                tbl_student s1 ON c.complainant_id = s1.student_id
            JOIN
                tbl_student s2 ON c.respondent_id = s2.student_id
    WHERE
        s1.adviser_id = ? OR s2.adviser_id = ?
", [$adviserId, $adviserId]));
        break;

    case 4: // Appointments Scheduled for Violation Cases
        $data = collect(DB::select("
            SELECT
                CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                DATE_FORMAT(va.violation_app_date, '%M %d, %Y') AS appointment_date,
                TIME_FORMAT(va.violation_app_time, '%h:%i %p') AS appointment_time,
                va.violation_app_status AS appointment_status
            FROM
                tbl_violation_appointment va
            JOIN
                tbl_violation_record v ON va.violation_id = v.violation_id
            JOIN
                tbl_student s ON v.violator_id = s.student_id
    WHERE
        s.adviser_id = ?
", [$adviserId]));
        break;

    case 5: // Complaint Records with Complainant and Respondent
        $data = collect(DB::select("
            SELECT
                c.complaints_id,
                CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                c.complaints_incident AS incident_description,
                DATE_FORMAT(c.complaints_date, '%M %d, %Y') AS complaint_date,
                TIME_FORMAT(c.complaints_time, '%h:%i %p') AS complaint_time
            FROM
                tbl_complaints c
            JOIN
                tbl_student s1 ON c.complainant_id = s1.student_id
            JOIN
                tbl_student s2 ON c.respondent_id = s2.student_id
    WHERE
        s1.adviser_id = ? OR s2.adviser_id = ?
", [$adviserId, $adviserId]));
        break;

case 6: // Complaints Filed within the Last 30 Days
    $data = collect(DB::select("
        SELECT
            c.complaints_id,
            CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
            CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
            ows.offense_type,
            DATE_FORMAT(c.complaints_date, '%M %d, %Y') AS complaint_date,
            TIME_FORMAT(c.complaints_time, '%h:%i %p') AS complaint_time
        FROM
            tbl_complaints c
        JOIN
            tbl_student s1 ON c.complainant_id = s1.student_id
        JOIN
            tbl_student s2 ON c.respondent_id = s2.student_id
        JOIN
            tbl_offenses_with_sanction ows ON c.offense_sanc_id = ows.offense_sanc_id
        WHERE
            (s1.adviser_id = ? OR s2.adviser_id = ?)
            AND c.complaints_date >= CURDATE() - INTERVAL 30 DAY
    ", [$adviserId, $adviserId]));
    break;

case 7: // Common Offenses by Frequency for current adviser
    $data = collect(DB::select("
        SELECT
            ows.offense_sanc_id AS offense_id,
            ows.offense_type,
            ows.offense_description,
            COUNT(v.violation_id) AS total_occurrences
        FROM tbl_violation_record v
        JOIN tbl_student s ON v.violator_id = s.student_id
        JOIN tbl_offenses_with_sanction ows
            ON v.offense_sanc_id = ows.offense_sanc_id
        WHERE s.adviser_id = ?
        GROUP BY
            v.offense_sanc_id,
            ows.offense_sanc_id,
            ows.offense_type,
            ows.offense_description
        ORDER BY total_occurrences DESC
    ", [$adviserId]));
    break;



case 8: // List of Violators with Repeat Offenses (Adviser-specific)
    $data = collect(DB::select("
        SELECT
            CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
            a.adviser_section AS section,
            a.adviser_gradelevel AS grade_level,
            COUNT(v.violation_id) AS total_violations,
            MIN(DATE_FORMAT(v.violation_date, '%M %d, %Y')) AS first_violation_date,
            MAX(DATE_FORMAT(v.violation_date, '%M %d, %Y')) AS most_recent_violation_date
        FROM
            tbl_violation_record v
        JOIN
            tbl_student s ON v.violator_id = s.student_id
        JOIN
            tbl_adviser a ON s.adviser_id = a.adviser_id
        WHERE
            s.adviser_id = ?
        GROUP BY
            s.student_id,
            s.student_fname,
            s.student_lname,
            a.adviser_section,
            a.adviser_gradelevel
        HAVING
            COUNT(v.violation_id) > 1
        ORDER BY
            total_violations DESC
    ", [$adviserId]));
    break;


case 9: // Offenses and Their Sanction Consequences
    $data = collect(DB::select("
        SELECT
            offense_type,
            offense_description,
            sanction_consequences
        FROM
            tbl_offenses_with_sanction
    "));
    break;
case 10: // Parent Contact Info for Students with Active Violations
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
            CONCAT(p.parent_fname, ' ', p.parent_lname) AS parent_name,
            p.parent_contactinfo AS parent_contactinfo,
            DATE_FORMAT(v.violation_date, '%M %d, %Y') AS violation_date,
            TIME_FORMAT(v.violation_time, '%h:%i %p') AS violation_time,
            'Active' AS violation_status
        FROM
            tbl_violation_record v
        JOIN
            tbl_student s ON v.violator_id = s.student_id
        JOIN
            tbl_parent p ON s.parent_id = p.parent_id
        WHERE
            v.violation_date >= CURDATE() - INTERVAL 30 DAY
            AND s.adviser_id = ?
    ", [$adviserId]));
    break;

case 11: // Sanction Trends Across Time Periods
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            ows.offense_sanc_id,
            ows.offense_type,
            ows.sanction_consequences,
            DATE_FORMAT(v.violation_date, '%M %Y') AS month_and_year,
            COUNT(v.violation_id) AS number_of_sanctions_given
        FROM
            tbl_violation_record v
        JOIN
            tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
        JOIN
            tbl_student s ON v.violator_id = s.student_id
        WHERE
            s.adviser_id = ?
        GROUP BY
            ows.offense_sanc_id,
            ows.offense_type,
            ows.sanction_consequences,
            DATE_FORMAT(v.violation_date, '%M %Y')
        ORDER BY
            DATE_FORMAT(v.violation_date, '%Y-%m') DESC,
            number_of_sanctions_given DESC
    ", [$adviserId]));
    break;


case 12: // Students and Their Parents (per adviser)
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
            CONCAT(p.parent_fname, ' ', p.parent_lname) AS parent_name,
            p.parent_contactinfo AS parent_contactinfo
        FROM
            tbl_student s
        JOIN
            tbl_parent p ON s.parent_id = p.parent_id
        WHERE
            s.adviser_id = ?
    ", [$adviserId]));
    break;

case 13: // Students with Both Violation and Complaint Records (per adviser)
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            s.student_fname AS first_name,
            s.student_lname AS last_name,
            COUNT(DISTINCT v.violation_id) AS violation_count,
            COUNT(DISTINCT c.complaints_id) AS complaint_involvement_count
        FROM
            tbl_student s
        LEFT JOIN
            tbl_violation_record v ON s.student_id = v.violator_id
        LEFT JOIN
            tbl_complaints c ON (s.student_id = c.complainant_id OR s.student_id = c.respondent_id)
        WHERE
            s.adviser_id = ?
        GROUP BY
            s.student_id, s.student_fname, s.student_lname
        HAVING
            violation_count > 0 AND complaint_involvement_count > 0
    ", [$adviserId]));
    break;

case 14: // Students with the Most Violation Records (per adviser)
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
            a.adviser_section AS adviser_section,
            a.adviser_gradelevel AS grade_level,
            COUNT(v.violation_id) AS total_violations
        FROM
            tbl_violation_record v
        JOIN
            tbl_student s ON v.violator_id = s.student_id
        JOIN
            tbl_adviser a ON s.adviser_id = a.adviser_id
        WHERE
            s.adviser_id = ?
        GROUP BY
            s.student_id, s.student_fname, s.student_lname, a.adviser_section, a.adviser_gradelevel
        ORDER BY
            total_violations DESC
    ", [$adviserId]));
    break;



 case 15: // Violation Records with Violator Information (per adviser)
    $adviserId = Auth::guard('adviser')->id();
    $data = collect(DB::select("
        SELECT
            v.violation_id AS violation_id,
            CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
            ows.offense_type AS offense_type,
            ows.sanction_consequences AS sanction,
            v.violation_incident AS incident_description,
            DATE_FORMAT(v.violation_date, '%M %d, %Y') AS violation_date,
            TIME_FORMAT(v.violation_time, '%h:%i %p') AS violation_time
        FROM
            tbl_violation_record v
        JOIN tbl_student s ON v.violator_id = s.student_id
        JOIN tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
        WHERE
            s.adviser_id = ?
    ", [$adviserId]));
    break;

}

return response()->json($data);
}


}