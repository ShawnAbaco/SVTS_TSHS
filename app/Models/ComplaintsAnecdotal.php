<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ComplaintsAnecdotal extends Model
{
    protected $table = 'tbl_complaints_anecdotal';
    protected $primaryKey = 'comp_anec_id';

    protected $fillable = [
        'complaints_id',
        'comp_anec_solution',
        'comp_anec_recommendation',
        'comp_anec_date',
        'comp_anec_time',
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
