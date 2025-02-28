<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class Faqs extends Model
{

    protected $table = 'faqs';

    protected $hidden = [
        'id',
        'updated_at',
        'deleted_at',
    ];

}
