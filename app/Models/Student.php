<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $table = 'tbl_student';
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'parent_id',
        'adviser_id',
        'student_fname',
        'student_lname',
        'student_sex',
        'student_birthdate',
        'student_address',
        'student_contactinfo',
        'status',
    ];



    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function adviser()
    {
        return $this->belongsTo(Adviser::class, 'adviser_id');
    }

    public function violations()
    {
        return $this->hasMany(ViolationRecord::class, 'violator_id');
    }

    public function complaintsFiled()
    {
        return $this->hasMany(Complaints::class, 'complainant_id');
    }

    public function complaintsAgainst()
    {
        return $this->hasMany(Complaints::class, 'respondent_id');
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
