<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;

class AOffenseSanctionController extends Controller
{
public function offensesanction()
{
    $offenses = DB::table('tbl_offenses_with_sanction')
        ->select(
            'offense_type',
            'offense_description',
            DB::raw('GROUP_CONCAT(DISTINCT sanction_consequences ORDER BY group_number, stage_number SEPARATOR ", ") as sanctions')
        )
        ->groupBy('offense_type', 'offense_description')
        ->orderBy('offense_type')
        ->get();

    return view('adviser.offensesanction', compact('offenses'));
}
    
}