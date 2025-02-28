<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class CandidateLanguages extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_languages';

    protected $hidden = [
        'id',
        'user_id',
        'deleted_at',
        'updated_at',
    ];
}
