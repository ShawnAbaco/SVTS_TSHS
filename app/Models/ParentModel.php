<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ParentModel extends Model
{
    protected $table = 'tbl_parent';
    protected $primaryKey = 'parent_id';

    protected $fillable = [
        'parent_fname',
        'parent_lname',
        'parent_sex',
        'parent_birthdate',
        'parent_email',
        'parent_contactinfo',
        'parent_relationship',
        'status',
    ];



    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
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
