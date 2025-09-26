<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OffensesWithSanctionSeeder extends Seeder
{
    public function run(): void
    {
        $offenses = [
            [
                'offense_type' => 'Tardiness',
                'offense_description' => 'Late arrival to class',
                'sanctions' => [
                    ['Verbal Warning', 1, 3],
                    ['Written Warning', 2, 3],
                    ['Detention', 3, 1],
                    ['Parent Notification', 4, 1],
                    ['Restorative Action', 5, 1],
                    ['Counseling', 6, 1],
                ],
            ],
            [
                'offense_type' => 'Incomplete homework',
                'offense_description' => 'Failure to submit assigned homework',
                'sanctions' => [
                    ['Verbal Warning', 1, 3],
                    ['Written Warning', 2, 3],
                    ['Detention', 3, 1],
                    ['Parent Notification', 4, 1],
                    ['Restorative Action', 5, 1],
                    ['Counseling', 6, 1],
                ],
            ],
            [
                'offense_type' => 'Disruptive behavior',
                'offense_description' => 'Behaviors that disrupt the learning environment',
                'sanctions' => [
                    ['Verbal Warning', 1, 3],
                    ['Written Warning', 2, 3],
                    ['Detention', 3, 1],
                    ['Parent Notification', 4, 1],
                    ['Restorative Action', 5, 1],
                    ['Counseling', 6, 1],
                ],
            ],
            [
                'offense_type' => 'Bullying/harassment',
                'offense_description' => 'Intimidation, teasing, or harassment of others',
                'sanctions' => [
                    ['Verbal Warning', 1, 3],
                    ['Written Warning', 2, 3],
                    ['Detention', 3, 1],
                    ['Parent Notification', 4, 1],
                    ['Restorative Action', 5, 1],
                    ['Counseling', 6, 1],
                    ['Suspension', 7, 1],
                    ['Expulsion', 8, 1],
                ],
            ],
            [
                'offense_type' => 'Cheating/plagiarism',
                'offense_description' => 'Unauthorized use of others\' work or ideas',
                'sanctions' => [
                    ['Verbal Warning', 1, 3],
                    ['Written Warning', 2, 3],
                    ['Detention', 3, 1],
                    ['Parent Notification', 4, 1],
                    ['Restorative Action', 5, 1],
                    ['Counseling', 6, 1],
                ],
            ],
        ];

        foreach ($offenses as $offense) {
            foreach ($offense['sanctions'] as [$sanction, $group, $max_stage]) {
                for ($stage = 1; $stage <= $max_stage; $stage++) {
                    DB::table('tbl_offenses_with_sanction')->insert([
                        'offense_type' => $offense['offense_type'],
                        'offense_description' => $offense['offense_description'],
                        'sanction_consequences' => $sanction,
                        'group_number' => $group,
                        'stage_number' => $stage,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
