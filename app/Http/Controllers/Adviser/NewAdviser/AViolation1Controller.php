<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ActivityLog; // Add this import

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense; // Changed from OffensesWithSanction
use App\Models\Sanction; // Added Sanction model
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Services\PhilSMSService;

use App\Models\Complaints;

use Barryvdh\DomPDF\Facade\Pdf;


class AViolation1Controller extends Controller
{

    public function updateGroup(Request $request)
{
    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'group_key' => 'required|string',
            'violation_incident' => 'required|string|max:1000',
            'violation_date' => 'required|date',
            'violation_time' => 'required|date_format:H:i',
            'offense_type' => 'required|exists:offense_sanctions,offense_sanc_id',
            'sanction' => 'required|string|max:500',
            'status' => 'required|in:pending,active,cleared,inactive'
        ]);

        // Decode group key to find violations
        $keyParts = explode('|', $validated['group_key']);

        if (count($keyParts) < 5) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid group key format'
            ], 400);
        }

        list($incident, $offenseType, $sanction, $date, $timeGroup) = $keyParts;

        // Get all violations with this group criteria
        $violations = ViolationRecord::where('violation_incident', $incident)
            ->where('violation_date', $date)
            ->where('handled_by', 'prefect')
            ->whereHas('offense', function($query) use ($offenseType) {
                $query->where('offense_type', $offenseType);
            })
            ->whereHas('sanction', function($query) use ($sanction) {
                $query->where('sanction_consequences', $sanction);
            })
            ->whereRaw('HOUR(violation_time) = ?', [$timeGroup])
            ->with(['student', 'offense'])
            ->get();

        if ($violations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No violations found for this group.'
            ], 404);
        }

        // Get or create sanction ID
        $sanctionId = $this->getSanctionId($validated['sanction']);

        if (!$sanctionId) {
            throw new \Exception('Could not determine sanction ID');
        }

        $updatedCount = 0;
        $studentNames = [];
        $violationIds = [];

        foreach ($violations as $violation) {
            $violation->update([
                'violation_incident' => $validated['violation_incident'],
                'violation_date' => $validated['violation_date'],
                'violation_time' => $validated['violation_time'],
                'offense_sanc_id' => $validated['offense_type'],
                'sanction_id' => $sanctionId,
                'status' => $validated['status'],
                'updated_at' => now()
            ]);

            $updatedCount++;
            $studentNames[] = $violation->student->student_fname . ' ' . $violation->student->student_lname;
            $violationIds[] = $violation->violation_id;
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} violations in the group.",
            'data' => [
                'updated_count' => $updatedCount,
                'group_key' => $validated['group_key'],
                'violation_ids' => $violationIds,
                'students_affected' => array_unique($studentNames),
                'students_count' => count(array_unique($studentNames))
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating group violation: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error updating group violation: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Helper method to get or create sanction ID from sanction text
 */
private function getSanctionId($sanctionText)
{
    // Try to find existing sanction
    $sanction = \App\Models\Sanction::where('sanction_consequences', $sanctionText)->first();

    if ($sanction) {
        return $sanction->sanction_id;
    }

    // If sanction doesn't exist, you might want to:
    // 1. Return a default sanction ID
    // 2. Create a new sanction (if your business logic allows)
    // 3. Throw an exception

    // For now, let's return the first sanction as fallback
    $fallbackSanction = \App\Models\Sanction::first();
    return $fallbackSanction ? $fallbackSanction->sanction_id : null;
}

public function getGroupStudents($groupKey)
{
    try {
        \Log::info('=== GET GROUP STUDENTS START ===');
        \Log::info('Group key received: ' . $groupKey);

        // Decode the group key to extract components
        $keyParts = explode('|', $groupKey);
        \Log::info('Key parts count: ' . count($keyParts));
        \Log::info('Key parts: ', $keyParts);

        if (count($keyParts) < 5) {
            \Log::error('Invalid group key format. Expected 5 parts, got: ' . count($keyParts));
            return response()->json([
                'success' => false,
                'message' => 'Invalid group key format'
            ], 400);
        }

        list($incident, $offenseType, $sanction, $date, $timeGroup) = $keyParts;

        \Log::info('Parsed components:');
        \Log::info('Incident: ' . $incident);
        \Log::info('Offense Type: ' . $offenseType);
        \Log::info('Sanction: ' . $sanction);
        \Log::info('Date: ' . $date);
        \Log::info('Time Group: ' . $timeGroup);

        // Get violations with matching criteria
        $violations = ViolationRecord::where('violation_incident', $incident)
            ->where('violation_date', $date)
            ->where('handled_by', 'prefect')
            ->whereHas('offense', function($query) use ($offenseType) {
                $query->where('offense_type', $offenseType);
            })
            ->whereHas('sanction', function($query) use ($sanction) {
                $query->where('sanction_consequences', $sanction);
            })
            ->whereRaw('HOUR(violation_time) = ?', [$timeGroup])
            ->with('student')
            ->get();

        \Log::info('Found violations count: ' . $violations->count());

        // Extract unique students
        $students = $violations->map(function($violation) {
            return $violation->student;
        })->filter()->unique('student_id')->values();

        \Log::info('Unique students count: ' . $students->count());

        return response()->json([
            'success' => true,
            'students' => $students,
            'count' => $students->count(),
            'violations_count' => $violations->count()
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getGroupStudents: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => 'Error loading group students: ' . $e->getMessage()
        ], 500);
    }
}


/**
     * Get group violation details for editing
     */
    public function getGroupViolationDetails($groupKey)
    {
        try {
            Log::info('Getting group violation details for key: ' . $groupKey);

            // Parse the group key
            $groupParts = explode('|', $groupKey);

            if (count($groupParts) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group key format'
                ], 400);
            }

            $incident = $groupParts[0];
            $offenseType = $groupParts[1];
            $sanction = $groupParts[2];
            $date = $groupParts[3];
            $timeGroup = $groupParts[4];

            // Get the first violation in this group to get the details
            $violation = ViolationRecord::with(['student', 'offense', 'sanction'])
                ->where('violation_incident', $incident)
                ->where('violation_date', $date)
                ->whereHas('offense', function($query) use ($offenseType) {
                    $query->where('offense_type', $offenseType);
                })
                ->whereHas('sanction', function($query) use ($sanction) {
                    $query->where('sanction_consequences', $sanction);
                })
                ->first();

            if (!$violation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Violation group not found'
                ], 404);
            }

            // Get all students in this group
            $students = ViolationRecord::with(['student'])
                ->where('violation_incident', $incident)
                ->where('violation_date', $date)
                ->whereHas('offense', function($query) use ($offenseType) {
                    $query->where('offense_type', $offenseType);
                })
                ->whereHas('sanction', function($query) use ($sanction) {
                    $query->where('sanction_consequences', $sanction);
                })
                ->get()
                ->pluck('student')
                ->unique('student_id')
                ->values();

            // Get the current offense ID and sanction ID
            $currentOffenseId = $violation->offense_id;
            $currentSanctionId = $violation->sanction_id;

            // Get available sanctions for the current offense using the improved method
            $availableSanctions = $this->getSanctionsForOffense($currentOffenseId);

            return response()->json([
                'success' => true,
                'data' => [
                    'group_key' => $groupKey,
                    'incident' => $violation->violation_incident,
                    'date' => $violation->violation_date,
                    'time' => $violation->violation_time,
                    'status' => $violation->status,
                    'current_offense_id' => $currentOffenseId,
                    'current_offense_type' => $violation->offense->offense_type,
                    'current_sanction_id' => $currentSanctionId,
                    'current_sanction' => $violation->sanction->sanction_consequences,
                    'students' => $students,
                    'available_sanctions' => $availableSanctions
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting group violation details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading group violation details'
            ], 500);
        }
    }

    /**
     * Update group violation
     */
    public function updateGroupViolation(Request $request)
    {
        try {
            Log::info('Updating group violation', $request->all());

            $validator = Validator::make($request->all(), [
                'group_key' => 'required|string',
                'violation_incident' => 'required|string|max:255',
                'violation_date' => 'required|date',
                'violation_time' => 'required|date_format:H:i',
                'offense_type' => 'required|exists:tbl_offense,offense_id',
                'sanction_id' => 'required',
                'status' => 'required|in:pending,active,cleared,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Parse the original group key
            $groupParts = explode('|', $request->group_key);

            if (count($groupParts) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group key format'
                ], 400);
            }

            $originalIncident = $groupParts[0];
            $originalOffenseType = $groupParts[1];
            $originalSanction = $groupParts[2];
            $originalDate = $groupParts[3];
            $originalTimeGroup = $groupParts[4];

            // Get all violations in this group
            $violations = ViolationRecord::with(['offense', 'sanction'])
                ->where('violation_incident', $originalIncident)
                ->where('violation_date', $originalDate)
                ->whereHas('offense', function($query) use ($originalOffenseType) {
                    $query->where('offense_type', $originalOffenseType);
                })
                ->whereHas('sanction', function($query) use ($originalSanction) {
                    $query->where('sanction_consequences', $originalSanction);
                })
                ->get();

            if ($violations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No violations found in this group'
                ], 404);
            }

            DB::beginTransaction();

            $updatedCount = 0;

            foreach ($violations as $violation) {
                // Handle "not_assigned" sanction
                $sanctionId = $request->sanction_id;
                if ($sanctionId === 'not_assigned') {
                    $sanctionId = DB::table('tbl_sanction')
                        ->where('sanction_consequences', 'NOT ASSIGNED')
                        ->value('sanction_id')
                        ?? DB::table('tbl_sanction')->insertGetId([
                            'sanction_consequences' => 'NOT ASSIGNED',
                            'sanction_description' => 'Default sanction for violations that have not been assigned a specific consequence yet.',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                }

                $violation->update([
                    'violation_incident' => $request->violation_incident,
                    'violation_date' => $request->violation_date,
                    'violation_time' => $request->violation_time,
                    'offense_id' => $request->offense_type,
                    'sanction_id' => $sanctionId,
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

                $updatedCount++;
            }

            DB::commit();

            Log::info("Successfully updated {$updatedCount} violations in group");

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} violations in the group",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating group violation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating group violation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sanctions for a specific offense (internal helper method)
     */
    private function getSanctionsForOffense($offenseId)
    {
        try {
            Log::info('Getting sanctions for offense ID: ' . $offenseId);

            if (!$offenseId) {
                return [
                    (object) [
                        'sanction_id' => 'not_assigned',
                        'sanction_consequences' => 'NOT ASSIGNED',
                        'sanction_description' => 'Please select an offense first'
                    ]
                ];
            }

            // Get sanctions from the sanction stages table for this specific offense
            $sanctions = DB::table('tbl_offense_with_sanction_stages as owss')
                ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
                ->where('owss.offense_id', $offenseId)
                ->select('s.sanction_id', 's.sanction_consequences', 's.sanction_description')
                ->whereNull('owss.deleted_at')
                ->whereNull('s.deleted_at')
                ->orderBy('owss.owss_id')
                ->get();

            Log::info('Found ' . $sanctions->count() . ' sanctions for offense ID: ' . $offenseId);

            // Convert all sanction consequences to uppercase for consistency
            $sanctions = $sanctions->map(function ($sanction) {
                $sanction->sanction_consequences = strtoupper($sanction->sanction_consequences);
                return $sanction;
            });

            // Add "NOT ASSIGNED" sanction as the first option
            $notAssignedSanction = [
                (object) [
                    'sanction_id' => 'not_assigned',
                    'sanction_consequences' => 'NOT ASSIGNED',
                    'sanction_description' => 'Default sanction for complaints that have not been assigned a specific consequence yet.'
                ]
            ];

            // Merge "NOT ASSIGNED" with the existing sanctions from stages
            $allSanctions = array_merge($notAssignedSanction, $sanctions->toArray());

            Log::info('Returning ' . count($allSanctions) . ' total sanctions');

            return $allSanctions;

        } catch (\Exception $e) {
            Log::error('Error in getSanctionsForOffense: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return [
                (object) [
                    'sanction_id' => 'not_assigned',
                    'sanction_consequences' => 'ERROR - CHECK LOGS',
                    'sanction_description' => 'There was an error loading sanctions. Please check the server logs.'
                ]
            ];
        }
    }

    /**
     * Get sanctions for a specific offense (API endpoint)
     */
  /**
 * Get sanctions for a specific offense (API endpoint)
 */
public function getSanctionsByOffense(Request $request)
{
    try {
        $offenseId = $request->input('offense_id');

        if (!$offenseId) {
            return response()->json([
                [
                    'sanction_id' => '',
                    'sanction_consequences' => 'Please select an offense first',
                    'sanction_description' => ''
                ]
            ]);
        }

        $sanctions = $this->getSanctionsForOffense($offenseId);
        return response()->json($sanctions);

    } catch (\Exception $e) {
        Log::error('Error in getSanctionsByOffense API: ' . $e->getMessage());
        return response()->json([
            [
                'sanction_id' => '',
                'sanction_consequences' => 'Error loading sanctions',
                'sanction_description' => 'There was an error loading sanctions. Please try again.'
            ]
        ], 500);
    }
}


}
