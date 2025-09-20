<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            ['Juan', 'Dela Cruz', 'male', '1975-03-12', 'juan.delacruz@gmail.com', '09171234567', 'Father'],
            ['Maria', 'Santos', 'female', '1980-07-22', 'maria.santos@gmail.com', '09281234567', 'Mother'],
            ['Antonio', 'Reyes', 'male', '1978-11-05', 'antonio.reyes@gmail.com', '09391234567', 'Father'],
            ['Sofia', 'Cruz', 'female', '1982-02-15', 'sofia.cruz@gmail.com', '09451234567', 'Mother'],
        ];

        foreach ($parents as $p) {
            ParentModel::create([
                'parent_fname' => $p[0],
                'parent_lname' => $p[1],
                'parent_sex' => $p[2],
                'parent_birthdate' => $p[3],
                'parent_email' => $p[4],
                'parent_contactinfo' => $p[5],
                'parent_relationship' => $p[6],
            ]);
        }
    }
}
