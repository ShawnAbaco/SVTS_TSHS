<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViolationRecord extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_violation_record';
    protected $primaryKey = 'violation_id';

    protected $fillable = [
        'violator_id',
        'prefect_id',
        'offense_id',
        'sanction_id',
        'violation_incident',
        'violation_date',
        'violation_time',
        'status',
        'handled_by',
        'escalated_at',
        'witnesses',
        'complainant',              // <-- NEW
        'evidence_description',
        'evidence_files',
        'sanction_start_at',        // <-- NEW
        'sanction_end_at',          // <-- NEW
        'sanction_status',          // <-- NEW
    ];

    protected $casts = [
        'violation_date' => 'date',
        'violation_time' => 'string',
        'escalated_at' => 'datetime',
        'sanction_start_at' => 'datetime',
        'sanction_end_at' => 'datetime',
        'deleted_at' => 'datetime',
        'evidence_files' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'violator_id', 'student_id');
    }

    public function prefect()
    {
        return $this->belongsTo(PrefectOfDiscipline::class, 'prefect_id', 'prefect_id');
    }

    public function offense()
    {
        return $this->belongsTo(Offense::class, 'offense_id', 'offense_id');
    }

    public function sanction()
    {
        return $this->belongsTo(Sanction::class, 'sanction_id', 'sanction_id');
    }

    public function appointments()
    {
        return $this->hasMany(ViolationAppointment::class, 'violation_id', 'violation_id');
    }

    public function anecdotal()
    {
        return $this->hasOne(ViolationAnecdotal::class, 'violation_id', 'violation_id');
    }
}
