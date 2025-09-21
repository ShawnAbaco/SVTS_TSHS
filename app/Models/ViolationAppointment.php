<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ViolationAppointment extends Model
{
    protected $table = 'tbl_violation_appointment';
    protected $primaryKey = 'violation_app_id';

    protected $fillable = [
        'violation_id',
        'violation_app_date',
        'violation_app_time',
        'violation_app_status',
        'status',
    ];



    // Cast dates/times to Carbon instances
    protected $dates = ['violation_app_date', 'violation_app_time', 'created_at', 'updated_at'];

    public function violation()
    {
        return $this->belongsTo(ViolationRecord::class, 'violation_id');
    }
     /** 🔎 Local Scopes for Status Filtering */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
