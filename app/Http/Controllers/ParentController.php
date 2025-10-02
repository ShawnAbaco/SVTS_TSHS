<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    /**
     * Display parent list
     */
    // public function parentList()
    // {
    //     // Get all parents with pagination
    //     $parents = DB::table('tbl_parent')->paginate(10);

    //     // Counts
    //     $archivedCount = DB::table('tbl_parent')->where('status', 'inactive')->count();
    //     $activeCount   = DB::table('tbl_parent')->where('status', 'active')->count();
    //     $totalCount    = DB::table('tbl_parent')->count();

    //     return view('prefect.parentlist', compact(
    //         'parents',
    //         'archivedCount',
    //         'activeCount',
    //         'totalCount'
    //     ));
    // }

    /**
     * Show create parent form
     */
    public function createParent()
    {
        return view('prefect.create-parent');
    }

    /**
     * Store new parent
     */
    public function parentStore(Request $request)
    {
        $request->validate([
            'parent_fname' => 'required|string',
            'parent_lname' => 'required|string',
            'parent_sex' => 'required|in:Male,Female',
            'parent_relationship' => 'required|string',
            'parent_birthdate' => 'required|date',
            'parent_contactinfo' => 'required|string|size:11',
            'status' => 'required|in:active,inactive',
        ]);

        DB::table('tbl_parent')->insert([
            'parent_fname' => $request->parent_fname,
            'parent_lname' => $request->parent_lname,
            'parent_sex' => $request->parent_sex,
            'parent_relationship' => $request->parent_relationship,
            'parent_birthdate' => $request->parent_birthdate,
            'parent_contactinfo' => $request->parent_contactinfo,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('parents.list')->with('success', 'Parent added successfully!');
    }

    /**
     * Update parent
     */
    public function parentUpdate(Request $request, $id)
    {
        $request->validate([
            'parent_fname' => 'required|string',
            'parent_lname' => 'required|string',
            'parent_birthdate' => 'required|date',
            'parent_contactinfo' => 'required|string',
            'parent_sex' => 'required|in:Male,Female',
            'parent_relationship' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        DB::table('tbl_parent')->where('parent_id', $id)->update([
            'parent_fname' => $request->parent_fname,
            'parent_lname' => $request->parent_lname,
            'parent_birthdate' => $request->parent_birthdate,
            'parent_contactinfo' => $request->parent_contactinfo,
            'parent_sex' => $request->parent_sex,
            'parent_relationship' => $request->parent_relationship,
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->route('parents.list')->with('success', 'Parent updated successfully!');
    }

    /**
     * Move parents to archive (set status to inactive)
     */
    public function archive(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id',
        ]);

        $parentIds = $request->input('parent_ids');

        // Update status to inactive (archive)
        DB::table('tbl_parent')->whereIn('parent_id', $parentIds)->update([
            'status' => 'inactive',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($parentIds) . ' parent(s) moved to archive successfully'
        ]);
    }

    /**
     * Get archived parents (status = inactive)
     */
    public function archived()
    {
        $archivedParents = DB::table('tbl_parent')
            ->where('status', 'inactive')
            ->get();

        return response()->json($archivedParents);
    }

    /**
     * Restore parents from archive (set status to active)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id',
        ]);

        $parentIds = $request->input('parent_ids');

        // Update status to active (restore)
        DB::table('tbl_parent')->whereIn('parent_id', $parentIds)->update([
            'status' => 'active',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($parentIds) . ' parent(s) restored successfully'
        ]);
    }

    /**
     * Permanently delete parents
     */
    public function destroyPermanent(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id',
        ]);

        $parentIds = $request->input('parent_ids');

        // Permanently delete
        DB::table('tbl_parent')->whereIn('parent_id', $parentIds)->delete();

        return response()->json([
            'success' => true,
            'message' => count($parentIds) . ' parent(s) deleted permanently'
        ]);
    }

    /**
     * Single parent destroy (for individual deletion)
     */
    public function destroyParent($id)
    {
        try {
            $parent = DB::table('tbl_parent')->where('parent_id', $id)->first();

            if (!$parent) {
                return redirect()->back()->with('error', 'Parent not found.');
            }

            // Update status to inactive instead of deleting
            DB::table('tbl_parent')->where('parent_id', $id)->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);

            return redirect()->route('parents.list')->with('success', 'Parent moved to archive successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error archiving parent: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS to parent
     */
    public function sendSms(Request $request)
    {
        $parentId = $request->parent_id;
        $message = $request->message;

        // Retrieve parent info
        $parent = DB::table('tbl_parent')->where('parent_id', $parentId)->first();

        if (!$parent) {
            return back()->with('error', 'Parent not found.');
        }

        // Here you would integrate your SMS API
        // Example: SmsService::send($parent->parent_contactinfo, $message);

        return back()->with('success', 'SMS sent to ' . $parent->parent_fname);
    }


}
