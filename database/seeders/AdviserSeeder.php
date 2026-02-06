<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Adviser;

class AdviserSeeder extends Seeder
{
    public function run(): void
    {
        $advisers = [
            ['Adviser', '', 'male', 'tshssvts@gmail.com', '09171239876', 'Eureka', 11, '09171234568'],
            ['Junald', 'Gonzaga', 'male', 'junaldadviser@gmail.com', '09281239876', 'Formidable', 12, '09281234569'],
        ];

        foreach ($advisers as $a) {
            Adviser::create([
                'adviser_fname' => $a[0],
                'adviser_lname' => $a[1],
                'adviser_sex' => $a[2],
                'adviser_email' => $a[3],
                'adviser_password' => bcrypt('adviser'),
                'adviser_contactinfo' => $a[4],
                'adviser_section' => $a[5],
                'adviser_gradelevel' => $a[6],
                'status' => 'active', // UPDATED: changed from 'adviser_status' to 'status'
            ]);
        }
    }
}
