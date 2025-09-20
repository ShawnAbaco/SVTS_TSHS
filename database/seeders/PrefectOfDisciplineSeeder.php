<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrefectOfDiscipline;

class PrefectOfDisciplineSeeder extends Seeder
{
    public function run(): void
    {
        PrefectOfDiscipline::create([
            'prefect_fname'       => 'Juan',
            'prefect_lname'       => 'Dela Cruz',
            'prefect_sex'         => 'male',
            'prefect_email'       => 'juan.delacruz@gmail.com',
            'prefect_password'    => bcrypt('password123'),
            'prefect_contactinfo' => '09171234567',
        ]);
    }
}
