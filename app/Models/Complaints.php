<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Complaints extends Model
{
    protected $table = 'tbl_complaints';
    protected $primaryKey = 'complaints_id';

    protected $fillable = [
        'complainant_id',
        'respondent_id',
        'prefect_id',
        'offense_sanc_id',
        'complaints_incident',
        'complaints_date',
        'complaints_time',
        'status',
    ];




    public function complainant()
    {
        return $this->belongsTo(Student::class, 'complainant_id');
    }

    public function respondent()
    {
        return $this->belongsTo(Student::class, 'respondent_id');
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
        return $this->hasMany(ComplaintsAppointment::class, 'complaints_id');
    }

    public function anecdotal()
    {
        return $this->hasOne(ComplaintsAnecdotal::class, 'complaints_id');
    }
}
