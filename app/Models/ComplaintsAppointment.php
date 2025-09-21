<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ComplaintsAppointment extends Model
{
    protected $table = 'tbl_complaints_appointment';
    protected $primaryKey = 'comp_app_id';

    protected $fillable = [
        'complaints_id',
        'comp_app_date',
        'comp_app_time',
        'comp_app_status',
        'status',
    ];



    public function complaint()
    {
        return $this->belongsTo(Complaints::class, 'complaints_id');
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
