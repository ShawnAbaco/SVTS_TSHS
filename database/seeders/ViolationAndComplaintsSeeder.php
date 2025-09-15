<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViolationAndComplaintsSeeder extends Seeder
{
    public function run(): void
    {
        $violations = [];
        $complaints = [];

        $violation_incidents = [
            'Late to class', 'Improper uniform', 'Disruptive behavior',
            'Late submission of homework', 'Unprepared for class'
        ];

        $complaint_incidents = [
            'Bullying during recess', 'Verbal argument in class',
            'Cheating during exam', 'Minor dispute', 'Property damage'
        ];

        // VIOLATIONS (20)
        for ($i = 1; $i <= 20; $i++) {
            $violations[$i] = DB::table('tbl_violation_record')->insertGetId([
                'violator_id' => ($i % 15) + 1,
                'prefect_id' => 1,
                'offense_sanc_id' => ($i % 10) + 1,
                'violation_incident' => $violation_incidents[$i % 5],
                'violation_date' => Carbon::now()->subDays($i)->toDateString(),
                'violation_time' => '08:' . str_pad(($i * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // VIOLATION APPOINTMENTS & ANECDOTAL
        foreach ($violations as $id => $violation_id) {
            DB::table('tbl_violation_appointment')->insert([
                'violation_id' => $violation_id,
                'violation_app_date' => Carbon::now()->addDays($id % 5)->toDateString(),
                'violation_app_time' => '10:' . str_pad(($id * 2) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'violation_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tbl_violation_anecdotal')->insert([
                'violation_id' => $violation_id,
                'violation_anec_solution' => 'Counseled student regarding incident',
                'violation_anec_recommendation' => 'Monitor for 1 week',
                'violation_anec_date' => Carbon::now()->toDateString(),
                'violation_anec_time' => '09:' . str_pad(($id * 2) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // COMPLAINTS (10)
        for ($i = 1; $i <= 10; $i++) {
            $complaints[$i] = DB::table('tbl_complaints')->insertGetId([
                'complainant_id' => ($i % 15) + 1,
                'respondent_id' => (($i + 3) % 15) + 1,
                'prefect_id' => 1,
                'offense_sanc_id' => ($i % 10) + 1,
                'complaints_incident' => $complaint_incidents[$i % 5],
                'complaints_date' => Carbon::now()->subDays($i)->toDateString(),
                'complaints_time' => '12:' . str_pad(($i * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // COMPLAINT APPOINTMENTS & ANECDOTAL
        foreach ($complaints as $id => $complaint_id) {
            DB::table('tbl_complaints_appointment')->insert([
                'complaints_id' => $complaint_id,
                'comp_app_date' => Carbon::now()->addDays($id % 5)->toDateString(),
                'comp_app_time' => '14:' . str_pad(($id * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'comp_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tbl_complaints_anecdotal')->insert([
                'complaints_id' => $complaint_id,
                'comp_anec_solution' => 'Mediated student conflict',
                'comp_anec_recommendation' => 'Observe interactions for 1 week',
                'comp_anec_date' => Carbon::now()->toDateString(),
                'comp_anec_time' => '15:' . str_pad(($id * 3) % 60, 2, '0', STR_PAD_LEFT) . ':00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
