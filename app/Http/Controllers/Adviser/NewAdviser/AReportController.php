<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Support\Facades\DB;

class AReportController extends Controller
{
    public function reportgenerate()
    {
        // No need to pass variables here - they're handled by AppServiceProvider
        return view('adviser.NewAdviser.reportgenerate');
    }

    public function generateReportData($reportId, Request $request)
    {
        $dateFilter = $request->get('date_filter', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = collect();

        // Build date filter conditions
        $dateCondition = $this->buildDateCondition($dateFilter, $startDate, $endDate);

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
                    WHERE 1=1 {$dateCondition['comp_anecdotal']}
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
                    WHERE 1=1 {$dateCondition['violation_anecdotal']}
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
                    WHERE 1=1 {$dateCondition['comp_appointment']}
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
                    WHERE 1=1 {$dateCondition['violation_appointment']}
                "));
                break;

            case 5: // Complaint Records by Adviser
                $data = collect(DB::select("
                    SELECT
                        CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS adviser_name,
                        CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                        CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                        o.offense_type AS type_of_offense,
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
                        tbl_offense o ON c.offense_id = o.offense_id
                    WHERE 1=1 {$dateCondition['complaints']}
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
                    WHERE 1=1 {$dateCondition['complaints']}
                "));
                break;

            case 7: // Complaints Filed within the Last 30 Days
                $baseCondition = "c.complaints_date >= CURDATE() - INTERVAL 30 DAY";
                $additionalCondition = $dateFilter !== 'all' ? " AND c.complaints_date >= CURDATE() - INTERVAL 30 DAY {$dateCondition['complaints']}" : "";

                $data = collect(DB::select("
                    SELECT
                        CONCAT(s1.student_fname, ' ', s1.student_lname) AS complainant_name,
                        CONCAT(s2.student_fname, ' ', s2.student_lname) AS respondent_name,
                        o.offense_type,
                        DATE_FORMAT(c.complaints_date, '%M %d, %Y') AS complaint_date,
                        TIME_FORMAT(c.complaints_time, '%h:%i %p') AS complaint_time
                    FROM
                        tbl_complaints c
                    JOIN
                        tbl_student s1 ON c.complainant_id = s1.student_id
                    JOIN
                        tbl_student s2 ON c.respondent_id = s2.student_id
                    JOIN
                        tbl_offense o ON c.offense_id = o.offense_id
                    WHERE {$baseCondition} {$additionalCondition}
                "));
                break;

            case 8: // Common Offenses by Frequency
                $data = collect(DB::select("
                    SELECT
                        o.offense_type,
                        o.offense_description,
                        COUNT(v.violation_id) AS total_occurrences
                    FROM tbl_violation_record v
                    JOIN tbl_offense o ON v.offense_id = o.offense_id
                    WHERE 1=1 {$dateCondition['violation_record']}
                    GROUP BY
                        v.offense_id,
                        o.offense_id,
                        o.offense_type,
                        o.offense_description
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
                    WHERE 1=1 {$dateCondition['violation_record']}
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
                        o.offense_type,
                        o.offense_description,
                        GROUP_CONCAT(DISTINCT s.sanction_consequences ORDER BY owss.owss_id SEPARATOR ', ') as sanction_consequences
                    FROM
                        tbl_offense_with_sanction_stages owss
                    JOIN
                        tbl_offense o ON owss.offense_id = o.offense_id
                    JOIN
                        tbl_sanction s ON owss.sanction_id = s.sanction_id
                    GROUP BY o.offense_type, o.offense_description
                    ORDER BY MIN(owss.owss_id)
                "));
                break;

            case 11: // Parent Contact Info for Students with Active Violations
                $baseCondition = "v.violation_date >= CURDATE() - INTERVAL 30 DAY";
                $additionalCondition = $dateFilter !== 'all' ? " AND v.violation_date >= CURDATE() - INTERVAL 30 DAY {$dateCondition['violation_record']}" : "";

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
                    WHERE {$baseCondition} {$additionalCondition}
                "));
                break;

            case 12: // Sanction Trends Across Time Periods
                $data = collect(DB::select("
                    SELECT
                        o.offense_type,
                        s.sanction_consequences,
                        DATE_FORMAT(v.violation_date, '%M %Y') AS month_and_year,
                        COUNT(v.violation_id) AS number_of_sanctions_given
                    FROM
                        tbl_violation_record v
                    JOIN
                        tbl_offense o ON v.offense_id = o.offense_id
                    JOIN
                        tbl_sanction s ON v.sanction_id = s.sanction_id
                    WHERE 1=1 {$dateCondition['violation_record']}
                    GROUP BY
                        o.offense_id,
                        o.offense_type,
                        s.sanction_consequences,
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
                    WHERE 1=1
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
                    WHERE 1=1
                "));
                break;

            case 15: // Students with Both Violation and Complaint Records
                $violationCondition = $dateFilter !== 'all' ? "AND v.violation_date {$dateCondition['violation_record_date_only']}" : "";
                $complaintCondition = $dateFilter !== 'all' ? "AND c.complaints_date {$dateCondition['complaints_date_only']}" : "";

                $data = collect(DB::select("
                    SELECT
                        s.student_fname AS first_name,
                        s.student_lname AS last_name,
                        COUNT(DISTINCT v.violation_id) AS violation_count,
                        COUNT(DISTINCT c.complaints_id) AS complaint_involvement_count
                    FROM
                        tbl_student s
                    LEFT JOIN
                        tbl_violation_record v ON s.student_id = v.violator_id {$violationCondition}
                    LEFT JOIN
                        tbl_complaints c ON (s.student_id = c.complainant_id OR s.student_id = c.respondent_id) {$complaintCondition}
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
                    WHERE 1=1 {$dateCondition['violation_record']}
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
                        o.offense_type,
                        COUNT(v.violation_id) AS number_of_violations
                    FROM
                        tbl_violation_record v
                    JOIN
                        tbl_student s ON v.violator_id = s.student_id
                    JOIN
                        tbl_adviser a ON s.adviser_id = a.adviser_id
                    JOIN
                        tbl_offense o ON v.offense_id = o.offense_id
                    WHERE 1=1 {$dateCondition['violation_record']}
                    GROUP BY
                        a.adviser_gradelevel, o.offense_type
                    ORDER BY
                        a.adviser_gradelevel, number_of_violations DESC
                "));
                break;

            case 18: // Violation Records and Assigned Adviser
                $data = collect(DB::select("
                    SELECT
                        CONCAT(s.student_fname, ' ', s.student_lname) AS student_name,
                        CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS adviser_name,
                        o.offense_type AS type_of_offense,
                        DATE_FORMAT(v.violation_date, '%M %d, %Y') AS violation_date,
                        TIME_FORMAT(v.violation_time, '%h:%i %p') AS violation_time,
                        v.violation_incident AS incident_description
                    FROM
                        tbl_violation_record v
                    JOIN tbl_student s ON v.violator_id = s.student_id
                    JOIN tbl_adviser adv ON s.adviser_id = adv.adviser_id
                    JOIN tbl_offense o ON v.offense_id = o.offense_id
                    WHERE 1=1 {$dateCondition['violation_record']}
                "));
                break;

            case 19: // Violation Records with Violator Information
                $data = collect(DB::select("
                    SELECT
                        CONCAT(st.student_fname, ' ', st.student_lname) AS student_name,
                        o.offense_type AS offense_type,
                        s.sanction_consequences AS sanction,
                        v.violation_incident AS incident_description,
                        DATE_FORMAT(v.violation_date, '%M %d, %Y') AS violation_date,
                        TIME_FORMAT(v.violation_time, '%h:%i %p') AS violation_time
                    FROM
                        tbl_violation_record v
                    JOIN tbl_student st ON v.violator_id = st.student_id
                    JOIN tbl_offense o ON v.offense_id = o.offense_id
                    JOIN tbl_sanction s ON v.sanction_id = s.sanction_id
                    WHERE 1=1 {$dateCondition['violation_record']}
                "));
                break;
        }

        return response()->json($data);
    }

    /**
     * Build date condition based on filter type
     */
    private function buildDateCondition($dateFilter, $startDate, $endDate)
    {
        $conditions = [
            'violation_record' => '',
            'violation_record_date_only' => '',
            'complaints' => '',
            'complaints_date_only' => '',
            'comp_anecdotal' => '',
            'violation_anecdotal' => '',
            'comp_appointment' => '',
            'violation_appointment' => ''
        ];

        if ($dateFilter === 'all') {
            return $conditions;
        }

        $dateFieldMapping = [
            'violation_record' => 'v.violation_date',
            'violation_record_date_only' => 'v.violation_date',
            'complaints' => 'c.complaints_date',
            'complaints_date_only' => 'c.complaints_date',
            'comp_anecdotal' => 'ca.comp_anec_date',
            'violation_anecdotal' => 'va.violation_anec_date',
            'comp_appointment' => 'ca.comp_app_date',
            'violation_appointment' => 'va.violation_app_date'
        ];

        $sqlCondition = '';

        switch ($dateFilter) {
            case 'daily':
                $sqlCondition = "= CURDATE()";
                break;
            case 'weekly':
                $sqlCondition = "BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()";
                break;
            case 'monthly':
                $sqlCondition = "BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()";
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $sqlCondition = "BETWEEN '{$startDate}' AND '{$endDate}'";
                }
                break;
        }

        foreach ($conditions as $key => &$value) {
            if (isset($dateFieldMapping[$key]) && $sqlCondition) {
                $value = "AND {$dateFieldMapping[$key]} {$sqlCondition}";
            }
        }

        return $conditions;
    }
}
