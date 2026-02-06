<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ParentModel;

class AParentController extends Controller
{
    public function parentlist()
    {
        $adviserId = Auth::guard('adviser')->id();

        // FIX: Get ALL active parents, not just those associated with students
        $parents = ParentModel::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Changed to 5 records per page

        // Summary Cards Data - count ALL parents
        $totalParents = ParentModel::where('status', 'active')->count();
        $activeParents = $totalParents;

        $archivedParents = ParentModel::where('status', 'inactive')->count();

        // Gender counts - ALL parents
        $maleParents = ParentModel::where('parent_sex', 'male')
            ->where('status', 'active')
            ->count();

        $femaleParents = ParentModel::where('parent_sex', 'female')
            ->where('status', 'active')
            ->count();

        $otherParents = ParentModel::where('parent_sex', 'other')
            ->where('status', 'active')
            ->count();

        // Relationship type counts - ALL parents
        $fatherCount = ParentModel::where('parent_relationship', 'father')
            ->where('status', 'active')
            ->count();

        $motherCount = ParentModel::where('parent_relationship', 'mother')
            ->where('status', 'active')
            ->count();

        $guardianCount = ParentModel::where('parent_relationship', 'guardian')
            ->where('status', 'active')
            ->count();

        return view('adviser.parentlist', compact(
            'parents',
            'totalParents',
            'activeParents',
            'archivedParents',
            'maleParents',
            'femaleParents',
            'otherParents',
            'fatherCount',
            'motherCount',
            'guardianCount'
        ));
    }

    /**
     * Check for duplicate parents before saving
     */
    public function checkDuplicate(Request $request)
    {
        try {
            $parents = $request->input('parents', []);
            $duplicates = [];

            foreach ($parents as $index => $parentData) {
                $existingParent = ParentModel::where('parent_fname', $parentData['parent_fname'])
                    ->where('parent_lname', $parentData['parent_lname'])
                    ->where('parent_birthdate', $parentData['parent_birthdate'])
                    ->where('parent_contactinfo', $parentData['parent_contactinfo'])
                    ->first();

                if ($existingParent) {
                    $duplicates[] = [
                        'index' => $index + 1, // Use array index + 1
                        'name' => $parentData['parent_fname'] . ' ' . $parentData['parent_lname'],
                        'birthdate' => $parentData['parent_birthdate'],
                        'contact' => $parentData['parent_contactinfo']
                    ];
                }
            }

            return response()->json([
                'has_duplicates' => !empty($duplicates),
                'duplicates' => $duplicates
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking parent duplicates: ' . $e->getMessage());
            return response()->json([
                'has_duplicates' => false,
                'error' => 'Error checking duplicates'
            ], 500);
        }
    }

    public function parentStore(Request $request)
    {
        // Debug: Check what's being received
        \Log::info('Received data:', $request->all());

        // Validate the array of parents - accept both cases for sex
        $validated = $request->validate([
            'parents' => 'required|array|min:1',
            'parents.*.parent_fname' => 'required|string|max:255',
            'parents.*.parent_lname' => 'required|string|max:255',
            'parents.*.parent_sex' => 'required|in:male,female,other,Male,Female,Other',
            'parents.*.parent_relationship' => 'required|string|max:255',
            'parents.*.parent_birthdate' => 'required|date',
            'parents.*.parent_contactinfo' => 'required|string|max:20',
            'parents.*.parent_email' => 'nullable|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            $duplicateParents = [];
            $validParents = [];
            $savedParents = [];

            // Enhanced duplicate checking with transaction
            foreach ($validated['parents'] as $index => $parentData) {
                // Convert sex to lowercase for database consistency
                $parentSex = strtolower($parentData['parent_sex']);

                $existingParent = ParentModel::where('parent_fname', $parentData['parent_fname'])
                    ->where('parent_lname', $parentData['parent_lname'])
                    ->where('parent_birthdate', $parentData['parent_birthdate'])
                    ->where('parent_contactinfo', $parentData['parent_contactinfo'])
                    ->first();

                if ($existingParent) {
                    $duplicateParents[] = [
                        'index' => $index + 1,
                        'name' => $parentData['parent_fname'] . ' ' . $parentData['parent_lname'],
                        'birthdate' => $parentData['parent_birthdate'],
                        'contact' => $parentData['parent_contactinfo']
                    ];
                } else {
                    $validParents[] = [
                        'data' => $parentData,
                        'index' => $index
                    ];
                }
            }

            // If there are duplicates, return error
            if (!empty($duplicateParents)) {
                DB::rollBack();

                $errorMessage = "The following parents already exist in the database:\n";
                foreach ($duplicateParents as $duplicate) {
                    $errorMessage .= "Parent #{$duplicate['index']}: {$duplicate['name']} (Birthdate: {$duplicate['birthdate']}, Contact: {$duplicate['contact']})\n";
                }
                $errorMessage .= "\nPlease remove or modify these parents before saving.";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'duplicates' => $duplicateParents
                    ], 422);
                }

                return back()->withInput()->with('error', $errorMessage);
            }

            // Save only non-duplicate parents
            $savedCount = 0;
            foreach ($validParents as $validParent) {
                $parentData = $validParent['data'];

                // Convert sex to lowercase for database
                $parentSex = strtolower($parentData['parent_sex']);

                $parent = ParentModel::create([
                    'parent_fname' => $parentData['parent_fname'],
                    'parent_lname' => $parentData['parent_lname'],
                    'parent_sex' => $parentSex,
                    'parent_relationship' => $parentData['parent_relationship'],
                    'parent_birthdate' => $parentData['parent_birthdate'],
                    'parent_contactinfo' => $parentData['parent_contactinfo'],
                    'parent_email' => $parentData['parent_email'] ?? null,
                    'status' => 'active',
                ]);

                $savedParents[] = $parent;
                $savedCount++;
            }

            DB::commit();

            // Log successful creation
            \Log::info("Created {$savedCount} parents", [
                'parent_count' => $savedCount,
                'parent_ids' => array_map(function ($parent) {
                    return $parent->parent_id;
                }, $savedParents)
            ]);

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $savedCount . ' parent(s) saved successfully!',
                    'saved_count' => $savedCount
                ]);
            }

            return redirect()->route('adviser.parentlist')->with('success', $savedCount . ' parent(s) saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving parents: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving parents: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'An error occurred while saving parents.');
        }
    }

    public function createParent()
    {
        return view('adviser.create-parent');
    }

    /**
     * Update parent (POST method for form submission)
     */
    public function parentUpdatePost(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'parent_fname' => 'required|string|max:255',
            'parent_lname' => 'required|string|max:255',
            'parent_birthdate' => 'required|date',
            'parent_contactinfo' => 'required|string|max:20',
            'parent_sex' => 'required|in:male,female,other',
            'parent_relationship' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'parent_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $parent = ParentModel::where('parent_id', $id)->first();

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent not found.'
                ], 404);
            }

            $updateData = [
                'parent_fname' => $request->parent_fname,
                'parent_lname' => $request->parent_lname,
                'parent_birthdate' => $request->parent_birthdate,
                'parent_contactinfo' => $request->parent_contactinfo,
                'parent_sex' => $request->parent_sex,
                'parent_relationship' => $request->parent_relationship,
                'status' => $request->status,
                'updated_at' => now(),
            ];

            // Only update email if provided
            if ($request->has('parent_email')) {
                $updateData['parent_email'] = $request->parent_email;
            }

            $parent->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Parent updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating parent: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating parent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent details for info modal
     */
    public function getParentDetails($id)
    {
        try {
            $parent = ParentModel::where('parent_id', $id)->first();

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent not found.'
                ], 404);
            }

            // Get associated students
            $students = Student::where('parent_id', $id)
                ->with('adviser')
                ->get();

            return response()->json([
                'success' => true,
                'parent' => $parent,
                'students' => $students
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching parent details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching parent details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move parents to archive (set status to inactive)
     */
    public function archiveParents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $affectedRows = ParentModel::whereIn('parent_id', $request->parent_ids)
                ->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => $affectedRows . ' parent(s) moved to archive successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error archiving parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error archiving parents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived parents (status = inactive)
     */
    public function getArchivedParents()
    {
        try {
            $adviserId = Auth::guard('adviser')->id();

            // Get parent IDs directly from students table
            $parentIds = Student::where('adviser_id', $adviserId)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->unique();

            $archivedParents = ParentModel::whereIn('parent_id', $parentIds)
                ->where('status', 'inactive')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json($archivedParents);
        } catch (\Exception $e) {
            \Log::error('Error fetching archived parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching archived parents'
            ], 500);
        }
    }

    /**
     * Restore parents from archive
     */
    public function restoreParents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $affectedRows = ParentModel::whereIn('parent_id', $request->parent_ids)
                ->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => $affectedRows . ' parent(s) restored successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error restoring parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error restoring parents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete parents
     */
    public function destroyParentsPermanent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if any parents have associated students
            $parentsWithStudents = Student::whereIn('parent_id', $request->parent_ids)
                ->exists();

            if ($parentsWithStudents) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete parents with associated students. Please remove student associations first.'
                ], 422);
            }

            $deletedCount = ParentModel::whereIn('parent_id', $request->parent_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' parent(s) permanently deleted!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting parents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Single parent destroy (for individual deletion)
     */
    public function destroyParent($id)
    {
        try {
            $parent = ParentModel::where('parent_id', $id)->first();

            if (!$parent) {
                return redirect()->back()->with('error', 'Parent not found.');
            }

            // Check if parent has associated students
            $hasStudents = Student::where('parent_id', $id)->exists();

            if ($hasStudents) {
                return redirect()->back()->with('error', 'Cannot archive parent with associated students. Please remove student associations first.');
            }

            // Update status to inactive instead of deleting
            $parent->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);

            return redirect()->route('adviser.parentlist')->with('success', 'Parent moved to archive successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error archiving parent: ' . $e->getMessage());
        }
    }

    /**
     * Get archived parents count
     */
    public function getArchivedParentsCount()
    {
        try {
            $adviserId = Auth::guard('adviser')->id();

            // Get parent IDs directly from students table
            $parentIds = Student::where('adviser_id', $adviserId)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->unique();

            $count = ParentModel::whereIn('parent_id', $parentIds)
                ->where('status', 'inactive')
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            \Log::error('Error getting archived count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Search parents
     */
    public function searchParents(Request $request)
    {
        try {
            $adviserId = Auth::guard('adviser')->id();
            $searchTerm = $request->input('search', '');

            // Get parent IDs directly from students table
            $parentIds = Student::where('adviser_id', $adviserId)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->unique();

            $parents = ParentModel::whereIn('parent_id', $parentIds)
                ->where('status', 'active')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('parent_fname', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_lname', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_id', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_contactinfo', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('parent_id', 'desc')
                ->paginate(5); // Changed to 5 records per page

            return response()->json([
                'success' => true,
                'parents' => $parents
            ]);
        } catch (\Exception $e) {
            \Log::error('Error searching parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error searching parents'
            ], 500);
        }
    }

    /**
     * Search archived parents
     */
    public function searchArchivedParents(Request $request)
    {
        try {
            $adviserId = Auth::guard('adviser')->id();
            $searchTerm = $request->input('search', '');

            // Get parent IDs directly from students table
            $parentIds = Student::where('adviser_id', $adviserId)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->unique();

            $parents = ParentModel::whereIn('parent_id', $parentIds)
                ->where('status', 'inactive')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('parent_fname', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_lname', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_id', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('parent_contactinfo', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('parent_id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'parents' => $parents
            ]);
        } catch (\Exception $e) {
            \Log::error('Error searching archived parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error searching archived parents'
            ], 500);
        }
    }

    /**
     * Send SMS to parent
     */
    public function sendSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:tbl_parent,parent_id',
            'message' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $parent = ParentModel::where('parent_id', $request->parent_id)->first();

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent not found.'
                ], 404);
            }

            // Here you would integrate your SMS API
            // Example: SmsService::send($parent->parent_contactinfo, $request->message);

            // For now, just log the SMS request
            \Log::info('SMS would be sent to ' . $parent->parent_contactinfo . ': ' . $request->message);

            return response()->json([
                'success' => true,
                'message' => 'SMS sent to ' . $parent->parent_fname . ' ' . $parent->parent_lname
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending SMS: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent statistics for dashboard
     */
    public function getParentStatistics()
    {
        try {
            $adviserId = Auth::guard('adviser')->id();

            // Get parent IDs directly from students table
            $parentIds = Student::where('adviser_id', $adviserId)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->unique();

            $totalParents = ParentModel::whereIn('parent_id', $parentIds)->count();
            $activeParents = ParentModel::whereIn('parent_id', $parentIds)->where('status', 'active')->count();
            $archivedParents = ParentModel::whereIn('parent_id', $parentIds)->where('status', 'inactive')->count();

            // Parents by relationship type
            $relationshipStats = ParentModel::whereIn('parent_id', $parentIds)
                ->select('parent_relationship', DB::raw('count(*) as count'))
                ->where('status', 'active')
                ->groupBy('parent_relationship')
                ->get();

            // Recent parents (last 7 days)
            $recentParents = ParentModel::whereIn('parent_id', $parentIds)
                ->where('created_at', '>=', now()->subDays(7))
                ->where('status', 'active')
                ->count();

            return response()->json([
                'success' => true,
                'statistics' => [
                    'total' => $totalParents,
                    'active' => $activeParents,
                    'archived' => $archivedParents,
                    'recent' => $recentParents,
                    'relationships' => $relationshipStats
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching parent statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics'
            ], 500);
        }
    }

    public function getParentStudents($parentId)
    {
        try {
            $parent = ParentModel::find($parentId);

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent not found.'
                ], 404);
            }

            // Get students associated with this parent
            $students = Student::where('parent_id', $parentId)
                ->with('adviser')
                ->get();

            return response()->json([
                'success' => true,
                'students' => $students
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching parent students: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching students'
            ], 500);
        }
    }
}