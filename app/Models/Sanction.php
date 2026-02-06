<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sanction extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_sanction';
    protected $primaryKey = 'sanction_id';

    protected $fillable = [
        'sanction_consequences',
        'sanction_description'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function offenseStages()
    {
        return $this->hasMany(OffenseWithSanctionStage::class, 'sanction_id', 'sanction_id');
    }

    public function offenses()
    {
        return $this->belongsToMany(Offense::class, 'tbl_offense_with_sanction_stages', 'sanction_id', 'offense_id');
    }

    public function violationRecords()
    {
        return $this->hasMany(ViolationRecord::class, 'sanction_id', 'sanction_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'sanction_id', 'sanction_id');
    }
}
