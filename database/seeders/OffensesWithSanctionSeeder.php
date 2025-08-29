<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OffensesWithSanction;

class OffensesWithSanctionSeeder extends Seeder
{
    public function run(): void
    {
        // ========== Tardiness ==========
        OffensesWithSanction::create([
            'offense_type'        => 'Tardiness',
            'offense_description' => 'Failure to arrive at school or class on time without valid reason.',
            'sanction_consequences' => 'Verbal Warning',
        ]);

        OffensesWithSanction::create([
            'offense_type'        => 'Tardiness',
            'offense_description' => 'Failure to arrive at school or class on time without valid reason.',
            'sanction_consequences' => 'Parent Notification',
        ]);

        // ========== Bullying / Harassment ==========
        OffensesWithSanction::create([
            'offense_type'        => 'Bullying/Harassment',
            'offense_description' => 'Any act of intimidation, harassment, or bullying of a fellow student.',
            'sanction_consequences' => 'Verbal Warning',
        ]);

        OffensesWithSanction::create([
            'offense_type'        => 'Bullying/Harassment',
            'offense_description' => 'Any act of intimidation, harassment, or bullying of a fellow student.',
            'sanction_consequences' => 'Parent Notification',
        ]);
    }
}
