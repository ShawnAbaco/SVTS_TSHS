<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Adviser;

class AdviserSeeder extends Seeder
{
    public function run(): void
    {
        Adviser::create([
            'adviser_fname'     => 'Maria',
            'adviser_lname'     => 'Santos',
            'adviser_email'     => 'maria.santos@school.com',
            'adviser_password'  => bcrypt('adviser123'),
            'adviser_contactinfo' => '09181234567',
            'adviser_section'   => 'Section A',
            'adviser_gradelevel'=> 'Grade 11',
        ]);

        Adviser::create([
            'adviser_fname'     => 'Jose',
            'adviser_lname'     => 'Reyes',
            'adviser_email'     => 'jose.reyes@school.com',
            'adviser_password'  => bcrypt('adviser123'),
            'adviser_contactinfo' => '09183456789',
            'adviser_section'   => 'Section B',
            'adviser_gradelevel'=> 'Grade 12',
        ]);
    }
}
