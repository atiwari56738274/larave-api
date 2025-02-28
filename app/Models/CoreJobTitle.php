<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreJobTitle extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_title';

    protected $hidden = [
        'id'
    ];
}
