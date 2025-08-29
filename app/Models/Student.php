<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'tbl_student';
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'parent_id',
        'adviser_id',
        'student_fname',
        'student_lname',
        'student_birthdate',
        'student_address',
        'student_contactinfo',
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
}
