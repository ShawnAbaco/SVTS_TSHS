<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Adviser;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $parent1 = ParentModel::first();
        $adviser1 = Adviser::first();

        Student::create([
            'parent_id'          => $parent1->parent_id,
            'adviser_id'         => $adviser1->adviser_id,
            'student_fname'      => 'Pedro',
            'student_lname'      => 'Garcia',
            'student_birthdate'  => '2007-01-15',
            'student_address'    => 'Tagoloan, Misamis Oriental',
            'student_contactinfo'=> '09301234567',
        ]);

        $parent2 = ParentModel::skip(1)->first();
        $adviser2 = Adviser::skip(1)->first();

        Student::create([
            'parent_id'          => $parent2->parent_id,
            'adviser_id'         => $adviser2->adviser_id,
            'student_fname'      => 'Luisa',
            'student_lname'      => 'Lopez',
            'student_birthdate'  => '2008-03-22',
            'student_address'    => 'Cagayan de Oro City',
            'student_contactinfo'=> '09324567890',
        ]);
    }
}
