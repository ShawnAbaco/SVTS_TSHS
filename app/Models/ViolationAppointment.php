<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViolationAppointment extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the table name since it doesn't follow Laravel's naming convention
    protected $table = 'tbl_violation_appointment';

    protected $primaryKey = 'violation_app_id';

    protected $fillable = [
        'violation_id',
        'handled_by',
        'violation_app_date',
        'violation_app_time',
        'violation_app_notes',
        'violation_app_status',
    ];

    protected $dates = [
        'violation_app_date',
        'violation_app_time',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationship with ViolationRecord
    public function violation()
    {
        return $this->belongsTo(ViolationRecord::class, 'violation_id', 'violation_id');
    }
}
