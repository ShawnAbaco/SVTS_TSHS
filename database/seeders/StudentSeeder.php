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
        $parents = ParentModel::all();
        $advisers = Adviser::all();

        $students = [
            ['Miguel', 'Garcia', 'male', '2007-01-10'],
            ['Angelica', 'Lopez', 'female', '2006-03-15'],
            ['Joshua', 'Reyes', 'male', '2007-05-22'],
            ['Samantha', 'Cruz', 'female', '2006-07-30'],
            ['Mark', 'Dela Rosa', 'male', '2007-02-12'],
            ['Nicole', 'Santos', 'female', '2006-09-05'],
            ['Ryan', 'Torres', 'male', '2007-08-18'],
            ['Isabella', 'Velasco', 'female', '2006-11-12'],
            ['Daniel', 'Ramos', 'male', '2007-04-25'],
            ['Stephanie', 'Gonzales', 'female', '2006-06-17'],
            ['Kevin', 'Diaz', 'male', '2007-03-09'],
            ['Jessica', 'Mendoza', 'female', '2006-12-02'],
            ['Christian', 'Navarro', 'male', '2007-10-14'],
            ['Alexa', 'Villanueva', 'female', '2006-05-23'],
            ['Patrick', 'Flores', 'male', '2007-07-01'],
        ];

        foreach ($students as $i => $s) {
            $parent = $parents[$i % $parents->count()];
            $adviser = $advisers[$i % $advisers->count()];

            Student::create([
                'parent_id' => $parent->parent_id,
                'adviser_id' => $adviser->adviser_id,
                'student_fname' => $s[0],
                'student_lname' => $s[1],
                'student_sex' => $s[2],
                'student_birthdate' => $s[3],
                'student_address' => 'Brgy. ' . ($i+1) . ', Tagoloan, Misamis Oriental',
                'student_contactinfo' => '0917' . str_pad($i+1000, 7, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
