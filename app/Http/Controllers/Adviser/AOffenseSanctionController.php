<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ViolationRecord;

class AOffenseSanctionController extends Controller
{
public function offensesanction()
{

    $offenses = DB::table('tbl_offense_with_sanction_stages as owss')
        ->join('tbl_offense as o', 'owss.offense_id', '=', 'o.offense_id')
        ->join('tbl_sanction as s', 'owss.sanction_id', '=', 's.sanction_id')
        ->select(
            'o.offense_type',
            'o.offense_description',
            DB::raw('GROUP_CONCAT(DISTINCT s.sanction_consequences ORDER BY owss.owss_id SEPARATOR ", ") as sanctions'),
            DB::raw('MIN(owss.owss_id) as min_id')
        )
        ->groupBy('o.offense_type', 'o.offense_description')
        ->orderBy('min_id', 'ASC')
        ->get();

        $sanctions = DB::table('tbl_sanction')->get();




    return view('adviser.offensesanction', compact('offenses','sanctions'));
}

}
