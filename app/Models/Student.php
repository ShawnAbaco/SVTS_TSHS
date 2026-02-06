<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

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

    protected $dates = ['deleted_at'];

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id', 'parent_id');
    }

    public function adviser()
    {
        return $this->belongsTo(Adviser::class, 'adviser_id', 'adviser_id');
    }

    public function violations()
    {
        return $this->hasMany(ViolationRecord::class, 'violator_id', 'student_id');
    }

    public function complaintsFiled()
    {
        return $this->hasMany(Complaints::class, 'complainant_id', 'student_id');
    }

    public function complaintsAgainst()
    {
        return $this->hasMany(Complaints::class, 'respondent_id', 'student_id');
    }

    /**
     * Get all complaints associated with the student (both as complainant and respondent)
     */
    public function allComplaints()
    {
        return Complaints::where('complainant_id', $this->student_id)
            ->orWhere('respondent_id', $this->student_id)
            ->get();
    }

    /**
     * Get the student's full name
     */
    public function getFullNameAttribute()
    {
        return $this->student_fname . ' ' . $this->student_lname;
    }

    /**
     * Get the student's grade level through adviser
     */
    public function getGradeLevelAttribute()
    {
        return $this->adviser ? $this->adviser->adviser_gradelevel : 'N/A';
    }

    /**
     * Get the student's section through adviser
     */
    public function getSectionAttribute()
    {
        return $this->adviser ? $this->adviser->adviser_section : 'N/A';
    }

    /**
     * Get the student's adviser name
     */
    public function getAdviserNameAttribute()
    {
        return $this->adviser ? $this->adviser->adviser_fname . ' ' . $this->adviser->adviser_lname : 'N/A';
    }
}