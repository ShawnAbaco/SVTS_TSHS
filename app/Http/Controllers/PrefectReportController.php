<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrefectReportController extends Controller
{
public function generateReportData($reportId)
{
    $data = collect();

        switch ($reportId) {
            case 1:
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
    "));
    return response()->json($data);

        case 2: // Students and Their Parents
            $data = collect(DB::select("
                SELECT
                    s.student_id, s.student_fname, s.student_lname,
                    p.parent_id, p.parent_fname, p.parent_lname, p.parent_contactinfo
                FROM
                    tbl_student s
                JOIN
                    tbl_parent p ON s.parent_id = p.parent_id
            "));
            break;

        case 3: // Complaint Records with Complainant and Respondent
            $data = collect(DB::select("
                SELECT
                    c.complaints_id,
                    s1.student_id AS complainant_id, s1.student_fname AS complainant_fname, s1.student_lname AS complainant_lname,
                    s2.student_id AS respondent_id, s2.student_fname AS respondent_fname, s2.student_lname AS respondent_lname,
                    c.complaints_incident, c.complaints_date, c.complaints_time
                FROM
                    tbl_complaints c
                JOIN
                    tbl_student s1 ON c.complainant_id = s1.student_id
                JOIN
                    tbl_student s2 ON c.respondent_id = s2.student_id
            "));
            break;

        case 4: // Offenses and Their Sanction Consequences
            $data = collect(DB::select("
                SELECT
                    offense_sanc_id, offense_type, offense_description, sanction_consequences
                FROM
                    tbl_offenses_with_sanction
            "));
            break;

        case 5: // Violation Records and Assigned Adviser
            $data = collect(DB::select("
                SELECT
                    v.violation_id,
                    s.student_id,
                    s.student_fname,
                    s.student_lname,
                    adv.adviser_id,
                    adv.adviser_fname,
                    adv.adviser_lname,
                    ows.offense_type,
                    v.violation_date,
                    v.violation_time,
                    v.violation_incident
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
                JOIN
                    tbl_adviser adv ON s.adviser_id = adv.adviser_id
                JOIN
                    tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
            "));
            break;

        case 6: // Students and Their Class Advisers
            $data = collect(DB::select("
                SELECT
                    s.student_id, s.student_fname, s.student_lname,
                    a.adviser_id, a.adviser_fname, a.adviser_lname,
                    a.adviser_section, a.adviser_gradelevel
                FROM
                    tbl_student s
                JOIN
                    tbl_adviser a ON s.adviser_id = a.adviser_id
            "));
            break;

        case 7: // Anecdotal Records per Violation Case
            $data = collect(DB::select("
                SELECT
                    va.violation_anec_id, v.violation_id, s.student_id, s.student_fname, s.student_lname,
                    va.violation_anec_solution, va.violation_anec_recommendation,
                    va.violation_anec_date, va.violation_anec_time
                FROM
                    tbl_violation_anecdotal va
                JOIN
                    tbl_violation_record v ON va.violation_id = v.violation_id
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
            "));
            break;

        case 8: // Appointments Scheduled for Violation Cases
            $data = collect(DB::select("
                SELECT
                    va.violation_app_id, v.violation_id, s.student_id, s.student_fname, s.student_lname,
                    va.violation_app_date, va.violation_app_time, va.violation_app_status
                FROM
                    tbl_violation_appointment va
                JOIN
                    tbl_violation_record v ON va.violation_id = v.violation_id
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
            "));
            break;

        case 9: // Anecdotal Records per Complaint Case
            $data = collect(DB::select("
                SELECT
                    ca.comp_anec_id, c.complaints_id,
                    s1.student_id AS complainant_id, s1.student_fname AS complainant_fname,
                    s2.student_id AS respondent_id, s2.student_fname AS respondent_fname,
                    ca.comp_anec_solution, ca.comp_anec_recommendation,
                    ca.comp_anec_date, ca.comp_anec_time
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

        case 10: // Appointments Scheduled for Complaints
            $data = collect(DB::select("
                SELECT
                    ca.comp_app_id, c.complaints_id,
                    s1.student_id AS complainant_id, s1.student_fname AS complainant_fname,
                    s2.student_id AS respondent_id, s2.student_fname AS respondent_fname,
                    ca.comp_app_date, ca.comp_app_time, ca.comp_app_status
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

        case 11: // Students with the Most Violation Records
            $data = collect(DB::select("
                SELECT
                    s.student_id, s.student_fname, s.student_lname,
                    a.adviser_section, a.adviser_gradelevel,
                    COUNT(v.violation_id) AS violation_count
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
                JOIN
                    tbl_adviser a ON s.adviser_id = a.adviser_id
                GROUP BY
                    s.student_id
                ORDER BY
                    violation_count DESC
            "));
            break;

        case 12: // Common Offenses by Frequency
            $data = collect(DB::select("
                SELECT
                    ows.offense_sanc_id, ows.offense_type, ows.offense_description,
                    COUNT(v.violation_id) AS offense_count
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                GROUP BY
                    v.offense_sanc_id
                ORDER BY
                    offense_count DESC
            "));
            break;

        case 13: // Complaint Records by Adviser
            $data = collect(DB::select("
                SELECT
                    c.complaints_id,
                    adv.adviser_id,
                    adv.adviser_fname,
                    adv.adviser_lname,
                    c.complainant_id,
                    c.respondent_id,
                    ows.offense_type,
                    c.complaints_date,
                    c.complaints_time
                FROM
                    tbl_complaints c
                JOIN
                    tbl_student resp ON c.respondent_id = resp.student_id
                JOIN
                    tbl_adviser adv ON resp.adviser_id = adv.adviser_id
                JOIN
                    tbl_offenses_with_sanction ows ON c.offense_sanc_id = ows.offense_sanc_id
            "));
            break;
        case 14: // List of Violators with Repeat Offenses
            $data = DB::table('tbl_violation_record as v')
                ->select('v.violator_id', DB::raw('COUNT(v.violation_id) as violation_count'))
                ->groupBy('v.violator_id')
                ->havingRaw('COUNT(v.violation_id) > 1')
                ->get();
            break;

        case 15: // Summary of Violations per Grade Level
            $data = collect(DB::select("
                SELECT
                    a.adviser_gradelevel, ows.offense_type,
                    COUNT(v.violation_id) AS violation_count
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
                    a.adviser_gradelevel, violation_count DESC
            "));
            break;

        case 16: // Parent Contact Info for Students with Active Violations
            $data = collect(DB::select("
                SELECT
                    s.student_id, s.student_fname, s.student_lname,
                    p.parent_id, p.parent_fname, p.parent_contactinfo,
                    v.violation_date, v.violation_time,
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

        case 17: // Complaints Filed within the Last 30 Days
            $data = collect(DB::select("
                SELECT
                    c.complaints_id,
                    s1.student_id AS complainant_id, s1.student_fname AS complainant_fname,
                    s2.student_id AS respondent_id, s2.student_fname AS respondent_fname,
                    ows.offense_type, c.complaints_date, c.complaints_time
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

        case 18: // Violation Records Involving Specific Offense Types
            $data = collect(DB::select("
                SELECT
                    v.violation_id, s.student_id, s.student_fname, s.student_lname,
                    ows.offense_type, v.violation_date, v.violation_time, v.violation_incident
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_student s ON v.violator_id = s.student_id
                JOIN
                    tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                WHERE
                    ows.offense_type = 'Tardiness'
            "));
            break;

        case 19: // Students with Both Violation and Complaint Records
            $data = collect(DB::select("
                SELECT
                    s.student_id, s.student_fname, s.student_lname,
                    COUNT(DISTINCT v.violation_id) AS violation_count,
                    COUNT(DISTINCT c.complaints_id) AS complaint_involvement_count
                FROM
                    tbl_student s
                LEFT JOIN
                    tbl_violation_record v ON s.student_id = v.violator_id
                LEFT JOIN
                    tbl_complaints c ON s.student_id = c.complainant_id OR s.student_id = c.respondent_id
                GROUP BY
                    s.student_id
                HAVING
                    violation_count > 0 AND complaint_involvement_count > 0
            "));
            break;

        case 20: // Sanction Trends Across Time Periods
            $data = collect(DB::select("
                SELECT
                    ows.offense_sanc_id,
                    ows.offense_type,
                    ows.sanction_consequences,
                    DATE_FORMAT(v.violation_date, '%Y-%m') AS period_range,
                    COUNT(v.violation_id) AS sanction_count
                FROM
                    tbl_violation_record v
                JOIN
                    tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                GROUP BY
                    ows.offense_sanc_id, period_range
                ORDER BY
                    period_range DESC, sanction_count DESC
            "));
            break;
    }

    return response()->json($data);
}


}
