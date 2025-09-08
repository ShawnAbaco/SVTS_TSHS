<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{ 
   public function parentStore(Request $request)
{
    $request->validate([
        'parent_fname' => 'required|string',
        'parent_lname' => 'required|string',
        'parent_birthdate' => 'required|date',
        'parent_contactinfo' => 'required|string',
    ]);

    DB::table('tbl_parent')->insert([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_contactinfo' => $request->parent_contactinfo,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Parent added successfully!');
}
public function parentUpdate(Request $request)
{
    $request->validate([
        'parent_id' => 'required|exists:tbl_parent,parent_id',
        'parent_fname' => 'required|string',
        'parent_lname' => 'required|string',
        'parent_birthdate' => 'required|date',
        'parent_contactinfo' => 'required|string',
    ]);

    DB::table('tbl_parent')->where('parent_id', $request->parent_id)->update([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_contactinfo' => $request->parent_contactinfo,
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Parent updated successfully!');
}

public function destroyParent($id)
{
    try {
        $parent = DB::table('tbl_parent')->where('parent_id', $id)->first();

        if (!$parent) {
            return redirect()->back()->with('error', 'Parent not found.');
        }

        DB::table('tbl_parent')->where('parent_id', $id)->delete();

        return redirect()->back()->with('success', 'Parent deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting parent: ' . $e->getMessage());
    }
}
}
