<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class PStudentController extends Controller
{
    /**
     * Store newly created students in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validate all student fields
        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.student_fname' => 'required|string|max:255',
            'students.*.student_lname' => 'required|string|max:255',
            'students.*.student_sex' => 'nullable|string|in:male,female,other',
            'students.*.student_birthdate' => 'required|date',
            'students.*.student_address' => 'required|string|max:255',
            'students.*.student_contactinfo' => 'required|string|max:50',
            'students.*.parent_id' => 'required|exists:tbl_parent,parent_id',
            'students.*.adviser_id' => 'required|exists:tbl_adviser,adviser_id',
            'students.*.status' => 'nullable|string|in:active,inactive,transferred,graduated',
        ]);

        // ✅ Store each student
        foreach ($validated['students'] as $studentData) {
            Student::create([
                'student_fname' => $studentData['student_fname'],
                'student_lname' => $studentData['student_lname'],
                'student_sex' => $studentData['student_sex'] ?? null,
                'student_birthdate' => $studentData['student_birthdate'],
                'student_address' => $studentData['student_address'],
                'student_contactinfo' => $studentData['student_contactinfo'],
                'parent_id' => $studentData['parent_id'],
                'adviser_id' => $studentData['adviser_id'],
                'status' => $studentData['status'] ?? 'active',
            ]);
        }

        return redirect()->route('student.management')->with('success', 'Students saved successfully!');

    }
}
