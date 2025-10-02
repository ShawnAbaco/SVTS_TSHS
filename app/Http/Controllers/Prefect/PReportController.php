<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Support\Facades\DB;

class PReportController extends Controller
{

public function reportgenerate()
    {
        return view('prefect.reportgenerate');
    }

    
public function generateReportData($reportId)
{
    $data = collect();

    switch ($reportId) {
        case 1: // Anecdotal Records per Complaint Case
            $data = collect(DB::select("
                SELECT
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
            "));
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
            "));
            break;

        case 3: // Appointments Scheduled for Complaints
            $data = collect(DB::select("
                SELECT
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
            "));
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
            "));
            break;

        case 5: // Complaint Records by Adviser
            $data = collect(DB::select("
                SELECT
                    CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS adviser_name,
                    CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                    CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                    ows.offense_type AS type_of_offense,
                    DATE_FORMAT(c.complaints_date, '%M %d, %Y') AS complaint_date,
                    TIME_FORMAT(c.complaints_time, '%h:%i %p') AS complaint_time
                FROM
                    tbl_complaints c
                JOIN
                    tbl_student s1 ON c.complainant_id = s1.student_id
                JOIN
                    tbl_student s2 ON c.respondent_id = s2.student_id
                JOIN
                    tbl_adviser adv ON s2.adviser_id = adv.adviser_id
                JOIN
                    tbl_offenses_with_sanction ows ON c.offense_sanc_id = ows.offense_sanc_id
            "));
            break;

        case 6: // Complaint Records with Complainant and Respondent
            $data = collect(DB::select("
                SELECT
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
            "));
            break;

        case 7: // Complaints Filed within the Last 30 Days
            $data = collect(DB::select("
                SELECT
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
                    c.complaints_date >= CURDATE() - INTERVAL 30 DAY
            "));
            break;

        case 8: // Common Offenses by Frequency
    $data = collect(DB::select("
        SELECT
            ows.offense_type,
            ows.offense_description,
            COUNT(v.violation_id) AS total_occurrences
        FROM tbl_violation_record v
        JOIN tbl_offenses_with_sanction ows
            ON v.offense_sanc_id = ows.offense_sanc_id
        GROUP BY
            v.offense_sanc_id,
            ows.offense_sanc_id,
            ows.offense_type,
            ows.offense_description
        ORDER BY total_occurrences DESC
    "));
    break;

        case 9: // List of Violators with Repeat Offenses
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
    "));
    break;

        case 10: // Offenses and Their Sanction Consequences
            $data = collect(DB::select("
                SELECT
                    offense_type, offense_description, sanction_consequences
                FROM
                    tbl_offenses_with_sanction
            "));
            break;

        case 11: // Parent Contact Info for Students with Active Violations
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
            "));
            break;

         case 12: // Sanction Trends Across Time Periods
    $data = collect(DB::select("
        SELECT
            ows.offense_type,
            ows.sanction_consequences,
            DATE_FORMAT(v.violation_date, '%M %Y') AS month_and_year,
            COUNT(v.violation_id) AS number_of_sanctions_given
        FROM
            tbl_violation_record v
        JOIN
            tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
        GROUP BY
            ows.offense_sanc_id,
            ows.offense_type,
            ows.sanction_consequences,
            month_and_year
        ORDER BY
            month_and_year DESC,
            number_of_sanctions_given DESC
    "));
    break;

        case 13: // Students and Their Class Advisers
            $data = collect(DB::select("
                SELECT
                    CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                    CONCAT(a.adviser_fname, ' ', a.adviser_lname) AS adviser_name,
                    a.adviser_section AS section,
                    a.adviser_gradelevel AS grade_level
                FROM
                    tbl_student s
                JOIN
                    tbl_adviser a ON s.adviser_id = a.adviser_id
            "));
            break;

        case 14: // Students and Their Parents
            $data = collect(DB::select("
                SELECT
                    CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                    CONCAT(p.parent_fname, ' ', p.parent_lname) AS parent_name,
                    p.parent_contactinfo AS parent_contactinfo
                FROM
                    tbl_student s
                JOIN
                    tbl_parent p ON s.parent_id = p.parent_id
            "));
            break;

       case 15: // Students with Both Violation and Complaint Records
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
        GROUP BY
            s.student_id, s.student_fname, s.student_lname
        HAVING
            violation_count > 0 AND complaint_involvement_count > 0
    "));
    break;


case 16: // Students with the Most Violation Records
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
        GROUP BY
            s.student_id, s.student_fname, s.student_lname, a.adviser_section, a.adviser_gradelevel
        ORDER BY
            total_violations DESC
    "));
    break;


        case 17: // Summary of Violations per Grade Level
            $data = collect(DB::select("
                SELECT
                    a.adviser_gradelevel AS grade_level,
                    ows.offense_type,
                    COUNT(v.violation_id) AS number_of_violations
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
                JOIN
                    tbl_adviser a ON s.adviser_id = a.adviser_id
                JOIN
                    tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                GROUP BY
                    a.adviser_gradelevel, ows.offense_type
                ORDER BY
                    a.adviser_gradelevel, number_of_violations DESC
            "));
            break;

        case 18: // Violation Records and Assigned Adviser
            $data = collect(DB::select("
                SELECT
                    CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                    CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS adviser_name,
                    ows.offense_type AS type_of_offense,
                    DATE_FORMAT(v.violation_date, '%M %d, %Y') AS violation_date,
                    TIME_FORMAT(v.violation_time, '%h:%i %p') AS violation_time,
                    v.violation_incident AS incident_description
                FROM
                    tbl_violation_record v
                JOIN tbl_student s ON v.violator_id = s.student_id
                JOIN tbl_adviser adv ON s.adviser_id = adv.adviser_id
                JOIN tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
            "));
            break;

        case 19: // Violation Records with Violator Information
            $data = collect(DB::select("
                SELECT
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
            "));
            break;
    }

    return response()->json($data);
}
}