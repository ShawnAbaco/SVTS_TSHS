<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OffenseWithSanctionStage extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_offense_with_sanction_stages';
    protected $primaryKey = 'owss_id';

    protected $fillable = [
        'offense_id',
        'sanction_id'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function offense()
    {
        return $this->belongsTo(Offense::class, 'offense_id', 'offense_id');
    }

    public function sanction()
    {
        return $this->belongsTo(Sanction::class, 'sanction_id', 'sanction_id');
    }
}
