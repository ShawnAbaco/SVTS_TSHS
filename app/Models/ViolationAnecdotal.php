<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationAnecdotal extends Model
{
    protected $table = 'tbl_violation_anecdotal';
    protected $primaryKey = 'violation_anec_id';

    protected $fillable = [
        'violation_id',
        'violation_anec_solution',
        'violation_anec_recommendation',
        'violation_anec_date',
        'violation_anec_time',
    ];

    // Cast dates/times to Carbon instances
    protected $dates = ['violation_anec_date', 'violation_anec_time', 'created_at', 'updated_at'];

    public function violation()
    {
        return $this->belongsTo(ViolationRecord::class, 'violation_id');
    }
}
