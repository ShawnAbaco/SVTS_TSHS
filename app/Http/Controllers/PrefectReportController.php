<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrefectReportController extends Controller
{
    public function fetchReportData($reportId)
    {
        $data = collect();

        switch ($reportId) {
            case 1: // Violation Records with Violator Information
                $data = collect(DB::select("
                    SELECT
                        v.violation_id AS `Violation ID`,
                        CONCAT(s.student_fname, ' ', s.student_lname) AS `Student Name`,
                        ows.offense_type AS `Offense Type`,
                        ows.sanction_consequences AS `Sanction`,
                        v.violation_incident AS `Incident Description`,
                        DATE_FORMAT(v.violation_date, '%M %d, %Y') AS `Violation Date`,
                        TIME_FORMAT(v.violation_time, '%h:%i %p') AS `Violation Time`
                    FROM
                        tbl_violation_record v
                    JOIN tbl_student s ON v.student_id = s.student_id
                    JOIN tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                "));
                break;

            case 2: // Students and Their Parents
                $data = collect(DB::select("
                    SELECT
                        CONCAT(s.student_fname, ' ', s.student_lname) AS `Student Name`,
                        CONCAT(p.parent_fname, ' ', p.parent_lname) AS `Parent Name`,
                        p.parent_contactinfo AS `Parent Contact Info`
                    FROM tbl_student s
                    JOIN tbl_parent p ON s.parent_id = p.parent_id
                "));
                break;

            case 3: // Complaint Records with Complainant and Respondent
                $data = collect(DB::select("
                    SELECT
                        c.complaints_id AS `Complaint ID`,
                        CONCAT(s1.student_fname, ' ', s1.student_lname) AS `Complainant`,
                        CONCAT(s2.student_fname, ' ', s2.student_lname) AS `Respondent`,
                        c.complaints_incident AS `Incident`,
                        DATE_FORMAT(c.complaints_date, '%M %d, %Y') AS `Complaint Date`,
                        TIME_FORMAT(c.complaints_time, '%h:%i %p') AS `Complaint Time`
                    FROM tbl_complaints c
                    JOIN tbl_student s1 ON c.complainant_id = s1.student_id
                    JOIN tbl_student s2 ON c.respondent_id = s2.student_id
                "));
                break;

            case 4: // Offenses and Their Sanction Consequences
                $data = collect(DB::select("
                    SELECT
                        offense_type AS `Offense Type`,
                        offense_description AS `Offense Description`,
                        sanction_consequences AS `Sanction`
                    FROM tbl_offenses_with_sanction
                "));
                break;

            case 5: // Violation Records and Assigned Adviser
                $data = collect(DB::select("
                    SELECT
                        v.violation_id AS `Violation ID`,
                        CONCAT(s.student_fname, ' ', s.student_lname) AS `Student Name`,
                        CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS `Adviser Name`,
                        ows.offense_type AS `Offense Type`,
                        DATE_FORMAT(v.violation_date, '%M %d, %Y') AS `Violation Date`,
                        TIME_FORMAT(v.violation_time, '%h:%i %p') AS `Violation Time`,
                        v.violation_incident AS `Incident Details`
                    FROM tbl_violation_record v
                    JOIN tbl_student s ON v.student_id = s.student_id
                    JOIN tbl_adviser adv ON s.adviser_id = adv.adviser_id
                    JOIN tbl_offenses_with_sanction ows ON v.offense_sanc_id = ows.offense_sanc_id
                "));
                break;

            // ... Continue like this for cases 6 to 20
            // Make sure to DATE_FORMAT() and TIME_FORMAT() all date/time fields
            // and CONCAT() names where possible for better readability.
        }

        return response()->json($data);
    }
}
