<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        ParentModel::create([
            'parent_fname'     => 'Carlos',
            'parent_lname'     => 'Garcia',
            'parent_birthdate' => '1980-05-10',
            'parent_contactinfo' => '09194567890',
        ]);

        ParentModel::create([
            'parent_fname'     => 'Ana',
            'parent_lname'     => 'Lopez',
            'parent_birthdate' => '1985-08-20',
            'parent_contactinfo' => '09201234567',
        ]);
    }
}
