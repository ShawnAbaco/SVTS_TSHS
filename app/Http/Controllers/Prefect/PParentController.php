<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ParentModel;




class PParentController extends Controller
{
    /**
     * Display parent list
     */
    public function parentlists()
    {

        $totalParents = DB::table('tbl_parent')->count();

        // Get active parents
        $activeParents = DB::table('tbl_parent')
            ->where('status', 'active')
            ->count();

        // Get archived parents
        $archivedParents = DB::table('tbl_parent')
            ->where('status', 'archived')
            ->count();
        $parents = ParentModel::where('status', 'active')->paginate(10);
        $archivedParents = ParentModel::where('status', 'inactive')->get();
        
        return view('prefect.parentlists', compact('parents', 'archivedParents','totalParents','activeParents','archivedParents'));
    }

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
    public function archiveParents(Request $request)
        {
            $request->validate([
                'parent_ids' => 'required|array',
                'parent_ids.*' => 'exists:tbl_parent,parent_id'
            ]);

            ParentModel::whereIn('parent_id', $request->parent_ids)
                ->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => count($request->parent_ids) . ' parent(s) moved to archive successfully!'
            ]);
        }

    /**
     * Get archived parents (status = inactive)
     */
    public function getArchivedParents()
    {
        $archivedParents = ParentModel::where('status', 'inactive')->get();
        return response()->json($archivedParents);
    }

    /**
     * Restore parents from archive
     */
    public function restoreParents(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        ParentModel::whereIn('parent_id', $request->parent_ids)
            ->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => count($request->parent_ids) . ' parent(s) restored successfully!'
        ]);
    }

    /**
     * Permanently delete parents
     */
    public function destroyParentsPermanent(Request $request)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:tbl_parent,parent_id'
        ]);

        ParentModel::whereIn('parent_id', $request->parent_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->parent_ids) . ' parent(s) permanently deleted!'
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

    public function getArchivedParentsCount()
    {
        $count = ParentModel::where('status', 'inactive')->count();
        return response()->json(['count' => $count]);
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