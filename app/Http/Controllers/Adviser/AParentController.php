<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ParentModel;

class AParentController extends Controller
{
    public function parentlist()
{
    // // Get all parents with their students
    // $parents = \App\Models\ParentModel::with('students')->get();

    // return view('adviser.parentlist', compact('parents'));


    $parents = ParentModel::paginate(10); // ✅ Use paginate for links()
    return view('adviser.parentlist', compact("parents"));


}

}