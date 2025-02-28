<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreIndustry extends Model
{
    use SoftDeletes; 

    protected $table = 'core_industry';

    protected $hidden = [
        'id'
    ];
}
