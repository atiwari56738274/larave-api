<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreJobCompany extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_company';

    protected $hidden = [
        'id'
    ];
}
