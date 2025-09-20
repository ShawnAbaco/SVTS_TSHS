<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationAppointment extends Model
{
    protected $table = 'tbl_violation_appointment';
    protected $primaryKey = 'violation_app_id';

    protected $fillable = [
        'violation_id',
        'violation_app_date',
        'violation_app_time',
        'violation_app_status',
    ];

    // Cast dates/times to Carbon instances
    protected $dates = ['violation_app_date', 'violation_app_time', 'created_at', 'updated_at'];

    public function violation()
    {
        return $this->belongsTo(ViolationRecord::class, 'violation_id');
    }
}
