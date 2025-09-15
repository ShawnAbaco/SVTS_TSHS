<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            ['Juan', 'Dela Cruz', '1975-03-12', '09171234567'],
            ['Maria', 'Santos', '1980-07-22', '09281234567'],
            ['Antonio', 'Reyes', '1978-11-05', '09391234567'],
            ['Sofia', 'Cruz', '1982-02-15', '09451234567'],
        ];

        foreach ($parents as $p) {
            ParentModel::create([
                'parent_fname' => $p[0],
                'parent_lname' => $p[1],
                'parent_birthdate' => $p[2],
                'parent_contactinfo' => $p[3],
            ]);
        }
    }
}
