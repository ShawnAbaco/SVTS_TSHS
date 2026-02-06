<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SanctionStagesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all sanctions
        $sanctions = DB::table('tbl_sanction')->get()->keyBy('sanction_consequences');

        // Define offense-sanction mappings based on your table (using only the available sanctions)
        $offenseSanctionMappings = [
            'Tardiness' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Incomplete homework' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Disruptive behavior' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Bullying/harassment' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Cheating/plagiarism' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Truancy' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Substance abuse' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Physical aggression' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Theft' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Vandalism' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Unauthorized technology use' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Defiance/resisting authority' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension'],
            'Dress code violation' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Academic dishonesty' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Disrespectful language' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Forgery/falsification' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Cyberbullying' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Gambling' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Destruction of property' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Hate speech' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Excessive noise' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Skipping class' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Academic misconduct' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Verbal harassment' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Plagiarism' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Inappropriate use of social media' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Littering' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Skipping school' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Forgery/faking signatures' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Discrimination' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Unauthorized use of school equipment' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Inappropriate physical contact' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Unauthorized materials' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention'],
            'Threats or intimidation' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
            'Use of profanity' => ['Verbal Warning', 'Detention', 'Parent/Guardian Notification', 'Referral to Prefect', 'Restorative Actions', 'Counseling and Intervention', 'Suspension', 'Expulsion'],
        ];

        // Populate tbl_offense_with_sanction_stages with all relationships
        foreach ($offenseSanctionMappings as $offenseType => $sanctionNames) {
            $offense = DB::table('tbl_offense')->where('offense_type', $offenseType)->first();

            if ($offense) {
                foreach ($sanctionNames as $sanctionName) {
                    if (isset($sanctions[$sanctionName])) {
                        DB::table('tbl_offense_with_sanction_stages')->insert([
                            'offense_id' => $offense->offense_id,
                            'sanction_id' => $sanctions[$sanctionName]->sanction_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
