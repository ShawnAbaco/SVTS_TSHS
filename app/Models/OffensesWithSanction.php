<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OffensesWithSanction extends Model
{
    protected $table = 'tbl_offenses_with_sanction';
    protected $primaryKey = 'offense_sanc_id';

    protected $fillable = [
        'offense_type',
        'offense_description',
        'sanction_consequences',
    ];




    public function violations()
    {
        return $this->hasMany(ViolationRecord::class, 'offense_sanc_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'offense_sanc_id');
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
