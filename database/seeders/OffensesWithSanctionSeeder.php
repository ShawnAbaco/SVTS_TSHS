<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OffensesWithSanction;

class OffensesWithSanctionSeeder extends Seeder
{
    public function run(): void
    {
        $offenses = [
            ['Tardiness', 'Late to school or class without valid reason', 'Verbal Warning'],
            ['Tardiness', 'Late to school or class without valid reason', 'Parent Notification'],
            ['Improper Uniform', 'Wearing incomplete or improper school uniform', 'Verbal Warning'],
            ['Improper Uniform', 'Wearing incomplete or improper school uniform', 'Parent Notification'],
            ['Bullying/Harassment', 'Intimidation, harassment, or bullying of fellow students', 'Verbal Warning'],
            ['Bullying/Harassment', 'Intimidation, harassment, or bullying of fellow students', 'Parent Notification'],
            ['Late Homework', 'Submitting homework past the deadline', 'Verbal Warning'],
            ['Late Homework', 'Submitting homework past the deadline', 'Parent Notification'],
            ['Disruptive Behavior', 'Interrupting or disturbing the class', 'Verbal Warning'],
            ['Disruptive Behavior', 'Interrupting or disturbing the class', 'Parent Notification'],
        ];

        foreach ($offenses as $offense) {
            OffensesWithSanction::create([
                'offense_type' => $offense[0],
                'offense_description' => $offense[1],
                'sanction_consequences' => $offense[2],
            ]);
        }
    }
}
