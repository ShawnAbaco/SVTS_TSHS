<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class PrefectOfDiscipline extends Authenticatable
{
    protected $table = 'tbl_prefect_of_discipline';
    protected $primaryKey = 'prefect_id';

    protected $fillable = [
        'prefect_fname',
        'prefect_lname',
        'prefect_sex',
        'prefect_email',
        'prefect_password',
        'prefect_contactinfo',
        'status',
    ];



    protected $hidden = [
        'prefect_password',
    ];

    public function getAuthPassword()
    {
        return $this->prefect_password;
    }

    public function violations()
    {
        return $this->hasMany(ViolationRecord::class, 'prefect_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'prefect_id');
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
