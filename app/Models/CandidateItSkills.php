<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class CandidateItSkills extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_it_skills';

    protected $hidden = [
        'id'
    ];


    public function skill() {
        return $this->hasOne(CoreJobSkills::class, 'id', 'skill_id')->select('id','uuid','title');
    }
}
