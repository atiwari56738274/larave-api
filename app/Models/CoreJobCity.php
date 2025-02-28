<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreJobCity extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_city';

    protected $hidden = [
        'id'
    ];
}
