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
            ['Miguel', 'Garcia', '2007-01-10'],
            ['Angelica', 'Lopez', '2006-03-15'],
            ['Joshua', 'Reyes', '2007-05-22'],
            ['Samantha', 'Cruz', '2006-07-30'],
            ['Mark', 'Dela Rosa', '2007-02-12'],
            ['Nicole', 'Santos', '2006-09-05'],
            ['Ryan', 'Torres', '2007-08-18'],
            ['Isabella', 'Velasco', '2006-11-12'],
            ['Daniel', 'Ramos', '2007-04-25'],
            ['Stephanie', 'Gonzales', '2006-06-17'],
            ['Kevin', 'Diaz', '2007-03-09'],
            ['Jessica', 'Mendoza', '2006-12-02'],
            ['Christian', 'Navarro', '2007-10-14'],
            ['Alexa', 'Villanueva', '2006-05-23'],
            ['Patrick', 'Flores', '2007-07-01'],
        ];

        foreach ($students as $i => $s) {
            $parent = $parents[$i % 4];
            $adviser = $advisers[$i % 2];

            Student::create([
                'parent_id' => $parent->parent_id,
                'adviser_id' => $adviser->adviser_id,
                'student_fname' => $s[0],
                'student_lname' => $s[1],
                'student_birthdate' => $s[2],
                'student_address' => 'Brgy. ' . ($i+1) . ', Tagoloan, Misamis Oriental',
                'student_contactinfo' => '0917' . str_pad($i+1000, 7, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
