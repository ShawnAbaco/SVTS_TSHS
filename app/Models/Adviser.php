<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Adviser extends Authenticatable
{
    protected $table = 'tbl_adviser';
    protected $primaryKey = 'adviser_id';

    protected $fillable = [
        'adviser_fname',
        'adviser_lname',
        'adviser_sex',
        'adviser_email',
        'adviser_password',
        'adviser_contactinfo',
        'adviser_section',
        'adviser_gradelevel',
        'status',

    ];



    protected $hidden = [
        'adviser_password',
    ];

    public function getAuthPassword()
    {
        return $this->adviser_password;
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'adviser_id');
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
