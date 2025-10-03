<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;
use App\Models\ViolationAnecdotal; // Make sure to import this

class PViolationAnecdotalController extends Controller
{
    public function createVAnecdotal()
    {
        return view('prefect.create-VAnecdotal');
    }
public function searchViolations(Request $request)
{
    try {
        \Log::info('=== SEARCH VIOLATIONS START ===');

        $query = $request->input('query');
        \Log::info('Query received:', ['query' => $query]);

        if (empty($query)) {
            return response()->json([]);
        }

        $violations = ViolationRecord::with(['student', 'offense'])
            ->where(function($q) use ($query) {
                $q->whereHas('student', function($studentQuery) use ($query) {
                    $studentQuery->where('student_fname', 'LIKE', "%{$query}%")
                                ->orWhere('student_lname', 'LIKE', "%{$query}%")
                                ->orWhereRaw("CONCAT(student_fname, ' ', student_lname) LIKE ?", ["%{$query}%"]);
                })
                ->orWhereHas('offense', function($offenseQuery) use ($query) {
                    $offenseQuery->where('offense_type', 'LIKE', "%{$query}%");
                });
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        \Log::info('Violations found:', ['count' => $violations->count()]);

        // Use a Set to track unique student names
        $uniqueStudents = [];
        $results = [];

        foreach ($violations as $violation) {
            if (!$violation->student) continue;

            $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;
            $studentId = $violation->student->student_id;

            // Skip if we've already seen this student
            if (in_array($studentId, $uniqueStudents)) {
                continue;
            }

            $uniqueStudents[] = $studentId;

            $offenseType = $violation->offense ?
                $violation->offense->offense_type :
                'Unknown Offense';

            $results[] = [
                'violation_id' => $violation->violation_id,
                'student_id' => $studentId,
                'student_name' => $studentName,
                'offense_type' => $offenseType,
                'violation_date' => $violation->violation_date,
                'violation_incident' => $violation->violation_incident
            ];
        }

        \Log::info('Final results to return:', ['results_count' => count($results)]);

        return response()->json($results);

    } catch (\Exception $e) {
        \Log::error('Error in searchViolations: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        return response()->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
    }
}
}
