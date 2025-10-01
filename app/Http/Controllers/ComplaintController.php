<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaints;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('prefect.create-complaints');
    }

    // Store multiple complaints - ENHANCED DEBUGGING
    public function store(Request $request)
    {
        \Log::info('=== COMPLAINT STORE METHOD STARTED ===');
        \Log::info('Full Request Data:', $request->all());
        
        try {
            DB::beginTransaction();

            $messages = [];
            $prefect_id = Auth::id() ?? 1;
            $savedCount = 0;

            // Get all complaints data
            $complaintsData = $request->input('complaints', []);
            
            \Log::info('Complaints Data Structure:', $complaintsData);
            \Log::info('Number of complaints to process:', ['count' => count($complaintsData)]);

            // Check if we have any data
            if (empty($complaintsData)) {
                \Log::warning('No complaints data found in request');
                DB::rollBack();
                return back()->with('error', 'No complaint data found. Please make sure you added complaints to the summary.');
            }

            // Loop through each complaint
            foreach ($complaintsData as $complaintIndex => $complaint) {
                \Log::info("Processing complaint index: {$complaintIndex}", $complaint);
                
                $complainant_id = $complaint['complainant_id'] ?? null;
                $respondent_id = $complaint['respondent_id'] ?? null;
                $offense_sanc_id = $complaint['offense_sanc_id'] ?? null;
                $date = $complaint['date'] ?? null;
                $time = $complaint['time'] ?? null;
                $incident = $complaint['incident'] ?? null;

                // Validate required fields
                if (!$complainant_id || !$respondent_id || !$offense_sanc_id || !$date || !$time || !$incident) {
                    \Log::warning("Skipping complaint {$complaintIndex} - missing required fields", [
                        'complainant_id' => $complainant_id,
                        'respondent_id' => $respondent_id,
                        'offense_sanc_id' => $offense_sanc_id,
                        'date' => $date,
                        'time' => $time,
                        'incident' => $incident
                    ]);
                    continue;
                }

                // Validate that students and offense exist
                $complainantExists = DB::table('tbl_student')->where('student_id', $complainant_id)->exists();
                $respondentExists = DB::table('tbl_student')->where('student_id', $respondent_id)->exists();
                $offenseExists = DB::table('tbl_offenses_with_sanction')->where('offense_sanc_id', $offense_sanc_id)->exists();

                if (!$complainantExists) {
                    \Log::warning("Complainant does not exist: {$complainant_id}");
                    continue;
                }
                
                if (!$respondentExists) {
                    \Log::warning("Respondent does not exist: {$respondent_id}");
                    continue;
                }
                
                if (!$offenseExists) {
                    \Log::warning("Offense does not exist: {$offense_sanc_id}");
                    continue;
                }

                // Get student names for success message
                $complainant = DB::table('tbl_student')->where('student_id', $complainant_id)->first();
                $respondent = DB::table('tbl_student')->where('student_id', $respondent_id)->first();
                
                $complainantName = $complainant ? $complainant->student_fname . ' ' . $complainant->student_lname : 'Unknown';
                $respondentName = $respondent ? $respondent->student_fname . ' ' . $respondent->student_lname : 'Unknown';

                \Log::info("Creating complaint record: {$complainantName} vs {$respondentName}");

                // Create the complaint record
                try {
                    $newComplaint = Complaints::create([
                        'complainant_id' => $complainant_id,
                        'respondent_id' => $respondent_id,
                        'prefect_id' => $prefect_id,
                        'offense_sanc_id' => $offense_sanc_id,
                        'complaints_incident' => $incident,
                        'complaints_date' => $date,
                        'complaints_time' => $time,
                        'status' => 'active'
                    ]);

                    \Log::info("Complaint created successfully with ID: {$newComplaint->id}");
                    $savedCount++;
                    $messages[] = "✅ {$complainantName} vs {$respondentName}";
                    
                } catch (\Exception $e) {
                    \Log::error("Failed to create complaint record: " . $e->getMessage());
                    continue;
                }
            }

            DB::commit();

            \Log::info("Complaint storage completed. Saved: {$savedCount}, Attempted: " . count($complaintsData));

            if ($savedCount === 0) {
                return back()->with('error', 
                    'No complaints were saved. Please check that:<br>'
                    . '1. All students exist in the database<br>'
                    . '2. The offense exists in the database<br>'
                    . '3. All fields are properly filled'
                );
            }

            $successMessage = "Successfully saved $savedCount complaint record(s)!<br><br>" . implode('<br>', array_slice($messages, 0, 10));
            if (count($messages) > 10) {
                $successMessage .= "<br>... and " . (count($messages) - 10) . " more";
            }

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complaint storage error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error saving complaints: ' . $e->getMessage());
        }
    }

    // AJAX search for students
    public function searchStudents(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return '<div class="no-results">Type at least 2 characters</div>';
        }

        $students = DB::table('tbl_student')
            ->select('student_id', 'student_fname', 'student_lname')
            ->where(function($q) use ($query) {
                $q->where('student_fname', 'like', "%$query%")
                  ->orWhere('student_lname', 'like', "%$query%")
                  ->orWhere(DB::raw("CONCAT(student_fname, ' ', student_lname)"), 'like', "%$query%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($students as $student) {
            $name = $student->student_fname . ' ' . $student->student_lname;
            $html .= "<div class='student-item' data-id='{$student->student_id}'>$name</div>";
        }

        return $html ?: '<div class="no-results">No students found</div>';
    }

    // AJAX search for offenses
    public function searchOffenses(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return '<div class="no-results">Type at least 2 characters</div>';
        }

        $offenses = DB::table('tbl_offenses_with_sanction')
            ->select('offense_sanc_id', 'offense_type', 'offense_description')
            ->where(function($q) use ($query) {
                $q->where('offense_type', 'like', "%$query%")
                  ->orWhere('offense_description', 'like', "%$query%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($offenses as $offense) {
            $html .= "<div class='offense-item' data-id='{$offense->offense_sanc_id}' title='{$offense->offense_description}'>{$offense->offense_type}</div>";
        }

        return $html ?: '<div class="no-results">No offenses found</div>';
    }
}