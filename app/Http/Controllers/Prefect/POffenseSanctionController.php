<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;

class POffenseSanctionController extends Controller
{

public function index()
{
    $offenses = DB::table('tbl_offenses_with_sanction')
        ->select(
            'offense_type',
            'offense_description',
            DB::raw('GROUP_CONCAT(DISTINCT sanction_consequences ORDER BY offense_sanc_id SEPARATOR ", ") as sanctions'),
            DB::raw('MIN(offense_sanc_id) as min_id')
        )
        ->groupBy('offense_type', 'offense_description')
        ->orderBy('min_id', 'ASC')
        ->get();

    return view('prefect.offensesandsanctions', compact('offenses'));
}




}
