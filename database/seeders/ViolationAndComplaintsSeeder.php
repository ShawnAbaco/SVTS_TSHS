<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Carbon\Carbon;

class ViolationAndComplaintsSeeder extends Seeder
{
    public function run(): void
    {
        $violation_incidents = [
            'Late to class', 'Improper uniform', 'Disruptive behavior',
            'Late submission of homework', 'Unprepared for class'
        ];

        $complaint_incidents = [
            'Bullying during recess', 'Verbal argument in class',
            'Cheating during exam', 'Minor dispute', 'Property damage'
        ];

        $students = Student::all();

        // Divide students into 2 adviser groups
        $adviserGroups = $students->groupBy('adviser_id')->take(2);

        $violationCount = 0;
        foreach ($adviserGroups as $groupId => $groupStudents) {

            $studentIds = $groupStudents->pluck('student_id')->toArray();

            // --------------------
            // VIOLATIONS for this group
            // --------------------
            foreach ($studentIds as $id) {
                $violationCount++;
                $violation_id = DB::table('tbl_violation_record')->insertGetId([
                    'violator_id' => $id,
                    'prefect_id' => 1,
                    'offense_sanc_id' => ($violationCount % 10) + 1,
                    'violation_incident' => $violation_incidents[$violationCount % 5],
                    'violation_date' => Carbon::now()->subDays($violationCount)->toDateString(),
                    'violation_time' => '08:' . str_pad(($violationCount * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // VIOLATION APPOINTMENT
                DB::table('tbl_violation_appointment')->insert([
                    'violation_id' => $violation_id,
                    'violation_app_date' => Carbon::now()->addDays($violationCount % 5)->toDateString(),
                    'violation_app_time' => '10:' . str_pad(($violationCount * 2) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'violation_app_status' => 'Scheduled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // VIOLATION ANECDOTAL
                DB::table('tbl_violation_anecdotal')->insert([
                    'violation_id' => $violation_id,
                    'violation_anec_solution' => 'Counseled student regarding incident',
                    'violation_anec_recommendation' => 'Monitor for 1 week',
                    'violation_anec_date' => Carbon::now()->toDateString(),
                    'violation_anec_time' => '09:' . str_pad(($violationCount * 2) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // --------------------
            // COMPLAINTS within this adviser group
            // --------------------
            $complaintCount = 0;
            for ($i = 0; $i < count($studentIds) - 1; $i += 2) {
                $complainant_id = $studentIds[$i];
                $respondent_id = $studentIds[$i + 1];

                $complaintCount++;
                $complaint_id = DB::table('tbl_complaints')->insertGetId([
                    'complainant_id' => $complainant_id,
                    'respondent_id' => $respondent_id,
                    'prefect_id' => 1,
                    'offense_sanc_id' => ($complaintCount % 10) + 1,
                    'complaints_incident' => $complaint_incidents[$complaintCount % 5],
                    'complaints_date' => Carbon::now()->subDays($complaintCount)->toDateString(),
                    'complaints_time' => '12:' . str_pad(($complaintCount * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // COMPLAINT APPOINTMENT
                DB::table('tbl_complaints_appointment')->insert([
                    'complaints_id' => $complaint_id,
                    'comp_app_date' => Carbon::now()->addDays($complaintCount % 5)->toDateString(),
                    'comp_app_time' => '14:' . str_pad(($complaintCount * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'comp_app_status' => 'Scheduled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // COMPLAINT ANECDOTAL
                DB::table('tbl_complaints_anecdotal')->insert([
                    'complaints_id' => $complaint_id,
                    'comp_anec_solution' => 'Mediated student conflict',
                    'comp_anec_recommendation' => 'Observe interactions for 1 week',
                    'comp_anec_date' => Carbon::now()->toDateString(),
                    'comp_anec_time' => '15:' . str_pad(($complaintCount * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
