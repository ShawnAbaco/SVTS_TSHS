<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrefectOfDiscipline;

class PrefectOfDisciplineSeeder extends Seeder
{
    public function run(): void
    {
        PrefectOfDiscipline::create([
            'prefect_fname'       => 'Prefect of',
            'prefect_lname'       => 'Discipline',
            'prefect_sex'         => 'male',
            'prefect_email'       => 'tshssvts@gmail.com',
            'prefect_password'    => bcrypt('prefect'),
            'prefect_contactinfo' => '09154240619',
            'status'              => 'active',
        ]);

        PrefectOfDiscipline::create([
            'prefect_fname'       => 'Kent Zyrone',
            'prefect_lname'       => 'Flores',
            'prefect_sex'         => 'male',
            'prefect_email'       => 'k.zyroneflores@gmail.com',
            'prefect_password'    => bcrypt('prefect'),
            'prefect_contactinfo' => '09093246917',
            'status'              => 'active',
        ]);
    }
}
