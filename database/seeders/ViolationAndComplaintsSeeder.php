<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ViolationAndComplaintsSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // VIOLATION RECORD
        // =========================
        $violation1 = DB::table('tbl_violation_record')->insertGetId([
            'violator_id' => 1,
            'prefect_id' => 1,
            'offense_sanc_id' => 1, // as per instruction
            'violation_incident' => 'Late to class',
            'violation_date' => Carbon::now()->subDays(3)->toDateString(),
            'violation_time' => '08:15:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $violation2 = DB::table('tbl_violation_record')->insertGetId([
            'violator_id' => 2,
            'prefect_id' => 1,
            'offense_sanc_id' => 2,
            'violation_incident' => 'Improper uniform',
            'violation_date' => Carbon::now()->subDays(2)->toDateString(),
            'violation_time' => '08:30:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =========================
        // VIOLATION APPOINTMENT
        // =========================
        DB::table('tbl_violation_appointment')->insert([
            [
                'violation_id' => $violation1,
                'violation_app_date' => Carbon::now()->addDays(1)->toDateString(),
                'violation_app_time' => '10:00:00',
                'violation_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'violation_id' => $violation2,
                'violation_app_date' => Carbon::now()->addDays(2)->toDateString(),
                'violation_app_time' => '11:00:00',
                'violation_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // =========================
        // VIOLATION ANECDOTAL
        // =========================
        DB::table('tbl_violation_anecdotal')->insert([
            [
                'violation_id' => $violation1,
                'violation_anec_solution' => 'Student counseled about punctuality',
                'violation_anec_recommendation' => 'Monitor attendance for 1 week',
                'violation_anec_date' => Carbon::now()->toDateString(),
                'violation_anec_time' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'violation_id' => $violation2,
                'violation_anec_solution' => 'Student reminded of proper uniform',
                'violation_anec_recommendation' => 'Check uniform daily',
                'violation_anec_date' => Carbon::now()->toDateString(),
                'violation_anec_time' => '09:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // =========================
        // COMPLAINTS
        // =========================
        $complaint1 = DB::table('tbl_complaints')->insertGetId([
            'complainant_id' => 1,
            'respondent_id' => 2,
            'prefect_id' => 1,
            'offense_sanc_id' => 3, // as per instruction
            'complaints_incident' => 'Bullying during recess',
            'complaints_date' => Carbon::now()->subDays(1)->toDateString(),
            'complaints_time' => '12:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $complaint2 = DB::table('tbl_complaints')->insertGetId([
            'complainant_id' => 2,
            'respondent_id' => 1,
            'prefect_id' => 1,
            'offense_sanc_id' => 4,
            'complaints_incident' => 'Verbal argument in class',
            'complaints_date' => Carbon::now()->subDays(1)->toDateString(),
            'complaints_time' => '13:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =========================
        // COMPLAINTS APPOINTMENT
        // =========================
        DB::table('tbl_complaints_appointment')->insert([
            [
                'complaints_id' => $complaint1,
                'comp_app_date' => Carbon::now()->addDays(1)->toDateString(),
                'comp_app_time' => '14:00:00',
                'comp_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'complaints_id' => $complaint2,
                'comp_app_date' => Carbon::now()->addDays(2)->toDateString(),
                'comp_app_time' => '15:00:00',
                'comp_app_status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // =========================
        // COMPLAINTS ANECDOTAL
        // =========================
        DB::table('tbl_complaints_anecdotal')->insert([
            [
                'complaints_id' => $complaint1,
                'comp_anec_solution' => 'Mediated between students',
                'comp_anec_recommendation' => 'Observe interactions for 1 week',
                'comp_anec_date' => Carbon::now()->toDateString(),
                'comp_anec_time' => '14:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'complaints_id' => $complaint2,
                'comp_anec_solution' => 'Warned both students about behavior',
                'comp_anec_recommendation' => 'Counseling session recommended',
                'comp_anec_date' => Carbon::now()->toDateString(),
                'comp_anec_time' => '15:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
