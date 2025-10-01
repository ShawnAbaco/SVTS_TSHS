<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ParentModel;

class PParentController extends Controller
{

public function store(Request $request)
    {
        $parents = $request->input('parents', []);

        foreach ($parents as $parentData) {
            ParentModel::create([
                'parent_fname'       => $parentData['parent_fname'],
                'parent_lname'       => $parentData['parent_lname'],
                'parent_sex'         => $parentData['parent_sex'] ?? null,
                'parent_birthdate'   => $parentData['parent_birthdate'],
                'parent_email'       => $parentData['parent_email'] ?? null,
                'parent_contactinfo' => $parentData['parent_contactinfo'],
                'parent_relationship'=> $parentData['parent_relationship'] ?? null,
            ]);
        }

        return redirect()->route('parent.lists')->with('success', 'Parents saved successfully!');
    }

public function update(Request $request)
{
    $parent = ParentModel::findOrFail($request->parent_id);

    $parent->update([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_sex' => $request->parent_sex,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_email' => $request->parent_email,
        'parent_contactinfo' => $request->parent_contactinfo,
        'parent_relationship' => $request->parent_relationship,
        'status' => $request->status,
    ]);

    return redirect()->back()->with('success', 'Parent updated successfully!');
}



}
