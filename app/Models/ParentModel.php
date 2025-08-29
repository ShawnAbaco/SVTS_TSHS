<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'tbl_parent';
    protected $primaryKey = 'parent_id';

    protected $fillable = [
        'parent_fname',
        'parent_lname',
        'parent_birthdate',
        'parent_contactinfo',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
