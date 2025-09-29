<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{

public function createParent(Request $request){

    return view('prefect.create-parent');

}


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
        'parent_sex' => 'required|in:Male,Female',
        'parent_relationship' => 'required|string',
        'status' => 'required|in:active,inactive',
    ]);

    DB::table('tbl_parent')->where('parent_id', $request->parent_id)->update([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_contactinfo' => $request->parent_contactinfo,
        'parent_sex' => $request->parent_sex,
        'parent_relationship' => $request->parent_relationship,
        'status' => $request->status,
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

        // Update status to inactive instead of deleting
        DB::table('tbl_parent')->where('parent_id', $id)->update([
            'status' => 'inactive',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Parent deactivated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deactivating parent: ' . $e->getMessage());
    }
}
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
