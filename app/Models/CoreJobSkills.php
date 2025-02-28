<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreJobSkills extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_skills';

    protected $hidden = [
        'id'
    ];
}
