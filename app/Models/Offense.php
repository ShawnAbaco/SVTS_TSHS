<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offense extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_offense';
    protected $primaryKey = 'offense_id';

    protected $fillable = [
        'category',
        'offense_type',
        'offense_description'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function sanctionStages()
    {
        return $this->hasMany(OffenseWithSanctionStage::class, 'offense_id', 'offense_id');
    }

    public function sanctions()
    {
        return $this->belongsToMany(Sanction::class, 'tbl_offense_with_sanction_stages', 'offense_id', 'sanction_id');
    }

    public function violationRecords()
    {
        return $this->hasMany(ViolationRecord::class, 'offense_id', 'offense_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'offense_id', 'offense_id');
    }
}
