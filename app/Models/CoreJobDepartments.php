<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreJobDepartments extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_department';

    protected $hidden = [
        'id'
    ];
}
