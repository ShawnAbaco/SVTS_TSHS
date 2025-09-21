<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ViolationRecord extends Model
{
    protected $table = 'tbl_violation_record';
    protected $primaryKey = 'violation_id';

    protected $fillable = [
        'violator_id',
        'prefect_id',
        'offense_sanc_id',
        'violation_incident',
        'violation_date',
        'violation_time',
        'status',
    ];



    // Cast dates/times to Carbon instances
    protected $dates = ['violation_date', 'violation_time', 'created_at', 'updated_at'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'violator_id');
    }

    public function prefect()
    {
        return $this->belongsTo(PrefectOfDiscipline::class, 'prefect_id');
    }

    public function offense()
    {
        return $this->belongsTo(OffensesWithSanction::class, 'offense_sanc_id');
    }

    public function appointments()
    {
        return $this->hasMany(ViolationAppointment::class, 'violation_id');
    }

    public function anecdotal()
    {
        return $this->hasOne(ViolationAnecdotal::class, 'violation_id');
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
