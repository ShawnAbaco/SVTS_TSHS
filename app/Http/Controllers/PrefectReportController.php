<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrefectReportController extends Controller
{
public function fetchReportData($reportId)
{
    $data = collect(); // default empty collection

    switch($reportId) {

        case 1: // Anecdotal Records per Complaint Case
            $data = DB::table('tbl_complaints_anecdotal as ca')
                ->join('tbl_complaints as c','ca.complaints_id','=','c.complaints_id')
                ->join('tbl_student as s1','c.complainant_id','=','s1.student_id')
                ->join('tbl_student as s2','c.respondent_id','=','s2.student_id')
                ->select(
                    'ca.comp_anec_id','c.complaints_id',
                    's1.student_id as complainant_id','s1.student_fname as complainant_fname',
                    's2.student_id as respondent_id','s2.student_fname as respondent_fname',
                    'ca.comp_anec_solution','ca.comp_anec_recommendation',
                    'ca.comp_anec_date','ca.comp_anec_time'
                )
                ->get();
            break;

        case 2: // Anecdotal Records per Violation Case
            $data = DB::table('tbl_violation_anecdotal as va')
                ->join('tbl_violation_record as v','va.violation_id','=','v.violation_id')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->select(
                    'va.violation_anec_id','v.violation_id','s.student_id',
                    's.student_fname','s.student_lname',
                    'va.violation_anec_solution','va.violation_anec_recommendation',
                    'va.violation_anec_date','va.violation_anec_time'
                )
                ->get();
            break;

        case 3: // Appointments Scheduled for Complaints
            $data = DB::table('tbl_complaints_appointment as ca')
                ->join('tbl_complaints as c','ca.complaints_id','=','c.complaints_id')
                ->join('tbl_student as s1','c.complainant_id','=','s1.student_id')
                ->join('tbl_student as s2','c.respondent_id','=','s2.student_id')
                ->select(
                    'ca.comp_app_id','c.complaints_id',
                    's1.student_id as complainant_id','s1.student_fname as complainant_fname',
                    's2.student_id as respondent_id','s2.student_fname as respondent_fname',
                    'ca.comp_app_date','ca.comp_app_time','ca.comp_app_status'
                )
                ->get();
            break;

        case 4: // Appointments Scheduled for Violation Cases
            $data = DB::table('tbl_violation_appointment as va')
                ->join('tbl_violation_record as v','va.violation_id','=','v.violation_id')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->select(
                    'va.violation_app_id','v.violation_id','s.student_id',
                    's.student_fname','s.student_lname',
                    'va.violation_app_date','va.violation_app_time','va.violation_app_status'
                )
                ->get();
            break;

        case 5: // Complaints Filed within the Last 30 Days
            $data = DB::table('tbl_complaints as c')
                ->join('tbl_student as s1','c.complainant_id','=','s1.student_id')
                ->join('tbl_student as s2','c.respondent_id','=','s2.student_id')
                ->join('tbl_offenses_with_sanction as ows','c.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'c.complaints_id','s1.student_id as complainant_id','s1.student_fname as complainant_fname',
                    's2.student_id as respondent_id','s2.student_fname as respondent_fname',
                    'ows.offense_type','c.complaints_date','c.complaints_time'
                )
                ->where('c.complaints_date','>=', DB::raw('CURDATE() - INTERVAL 30 DAY'))
                ->get();
            break;

        case 6: // Complaint Records by Adviser
            $data = DB::table('tbl_complaints as c')
                ->join('tbl_student as resp','c.respondent_id','=','resp.student_id')
                ->join('tbl_adviser as adv','resp.adviser_id','=','adv.adviser_id')
                ->join('tbl_offenses_with_sanction as ows','c.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'c.complaints_id','adv.adviser_id','adv.adviser_fname','adv.adviser_lname',
                    'c.complainant_id','c.respondent_id','ows.offense_type',
                    'c.complaints_date','c.complaints_time'
                )
                ->get();
            break;

        case 7: // Complaint Records with Complainant and Respondent
            $data = DB::table('tbl_complaints as c')
                ->join('tbl_student as s1','c.complainant_id','=','s1.student_id')
                ->join('tbl_student as s2','c.respondent_id','=','s2.student_id')
                ->select(
                    'c.complaints_id',
                    's1.student_id as complainant_id','s1.student_fname as complainant_fname','s1.student_lname as complainant_lname',
                    's2.student_id as respondent_id','s2.student_fname as respondent_fname','s2.student_lname as respondent_lname',
                    'c.complaints_incident','c.complaints_date','c.complaints_time'
                )
                ->get();
            break;

        case 8: // Common Offenses by Frequency
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_offenses_with_sanction as ows','v.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'ows.offense_sanc_id','ows.offense_type','ows.offense_description',
                    DB::raw('COUNT(v.violation_id) as offense_count')
                )
                ->groupBy('v.offense_sanc_id','ows.offense_type','ows.offense_description')
                ->orderByDesc('offense_count')
                ->get();
            break;

        case 9: // List of Violators with Repeat Offenses
            $data = DB::table('tbl_violation_record as v')
                ->select('v.violator_id', DB::raw('COUNT(v.violation_id) as violation_count'))
                ->groupBy('v.violator_id')
                ->havingRaw('COUNT(v.violation_id) > 1')
                ->get();
            break;

        case 10: // Offenses and Their Sanction Consequences
            $data = DB::table('tbl_offenses_with_sanction')
                ->select('offense_sanc_id','offense_type','offense_description','sanction_consequences')
                ->get();
            break;

        case 11: // Parent Contact Info for Students with Active Violations
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_parent as p','s.parent_id','=','p.parent_id')
                ->select(
                    's.student_id','s.student_fname','s.student_lname',
                    'p.parent_id','p.parent_fname','p.parent_contactinfo',
                    'v.violation_date','v.violation_time',
                    DB::raw("'Active' as violation_status")
                )
                ->where('v.violation_date','>=', DB::raw('CURDATE() - INTERVAL 30 DAY'))
                ->get();
            break;

case 12: // Sanction Trends Across Time Periods
    $data = collect(DB::select("
        SELECT
            CONCAT(s.student_fname, ' ', s.student_lname) AS 'Student Name',
            c.incident_details AS 'Incident Details',
            v.violation_record_id AS 'Violation Record ID',
            DATE_FORMAT(c.date, '%M %d, %Y') AS 'Date Reported',
            DATE_FORMAT(c.time, '%h:%i %p') AS 'Time Reported'
        FROM
            tbl_complainant c
        JOIN
            tbl_violation_record v ON c.complainant_id = v.complainant_id
        JOIN
            tbl_student s ON c.student_id = s.student_id
    "));
    break;


        case 13: // Students and Their Class Advisers
            $data = DB::table('tbl_student as s')
                ->join('tbl_adviser as a','s.adviser_id','=','a.adviser_id')
                ->select(
                    's.student_id','s.student_fname','s.student_lname',
                    'a.adviser_id','a.adviser_fname','a.adviser_lname',
                    'a.adviser_section','a.adviser_gradelevel'
                )
                ->get();
            break;

        case 14: // Students and Their Parents
            $data = DB::table('tbl_student as s')
                ->join('tbl_parent as p','s.parent_id','=','p.parent_id')
                ->select(
                    's.student_id','s.student_fname','s.student_lname',
                    'p.parent_id','p.parent_fname','p.parent_lname','p.parent_contactinfo'
                )
                ->get();
            break;

  case 15: // Students with Both Violation and Complaint Records
            $data = collect(DB::select("
                SELECT
                    CONCAT(adv.adviser_fname, ' ', adv.adviser_lname) AS adviser_name,
                    a.solution AS solution_taken,
                    a.recommendation AS recommendation,
                    v.violation_record_id AS violation_record_id
                FROM tbl_anecdotal_record a
                JOIN tbl_violation_record v ON a.violation_record_id = v.violation_record_id
                JOIN tbl_adviser adv ON v.adviser_id = adv.adviser_id
            "));
            break;

        case 16: // Students with the Most Violation Records
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_adviser as a','s.adviser_id','=','a.adviser_id')
                ->select(
                    's.student_id','s.student_fname','s.student_lname',
                    'a.adviser_section','a.adviser_gradelevel',
                    DB::raw('COUNT(v.violation_id) as violation_count')
                )
                ->groupBy('s.student_id','s.student_fname','s.student_lname','a.adviser_section','a.adviser_gradelevel')
                ->orderByDesc('violation_count')
                ->get();
            break;

        case 17: // Summary of Violations per Grade Level
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_adviser as a','s.adviser_id','=','a.adviser_id')
                ->join('tbl_offenses_with_sanction as ows','v.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'a.adviser_gradelevel','ows.offense_type',
                    DB::raw('COUNT(v.violation_id) as violation_count')
                )
                ->groupBy('a.adviser_gradelevel','ows.offense_type')
                ->orderBy('a.adviser_gradelevel')
                ->orderByDesc('violation_count')
                ->get();
            break;

        case 18: // Violation Records Involving Specific Offense Types
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_offenses_with_sanction as ows','v.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'v.violation_id','s.student_id','s.student_fname','s.student_lname',
                    'ows.offense_type','v.violation_date','v.violation_time','v.violation_incident'
                )
                ->where('ows.offense_type','=','Tardiness')
                ->get();
            break;

        case 19: // Violation Records and Assigned Adviser
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_adviser as adv','s.adviser_id','=','adv.adviser_id')
                ->join('tbl_offenses_with_sanction as ows','v.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'v.violation_id','s.student_id','s.student_fname','s.student_lname',
                    'adv.adviser_id','adv.adviser_fname','adv.adviser_lname',
                    'ows.offense_type','v.violation_date','v.violation_time','v.violation_incident'
                )
                ->get();
            break;

        case 20: // Violation Records with Violator Information
            $data = DB::table('tbl_violation_record as v')
                ->join('tbl_student as s','v.violator_id','=','s.student_id')
                ->join('tbl_offenses_with_sanction as ows','v.offense_sanc_id','=','ows.offense_sanc_id')
                ->select(
                    'v.violation_id','s.student_id','s.student_fname','s.student_lname',
                    'ows.offense_type','ows.offense_description',
                    'v.violation_date','v.violation_time','v.violation_incident'
                )
                ->get();
            break;
    }

    return response()->json($data);
}

}
