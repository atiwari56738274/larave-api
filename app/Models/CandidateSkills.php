<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class CandidateSkills extends Model
{
    // use SoftDeletes; 

    protected $table = 'candidate_skills';

    protected $fillable = [
        'uuid',
        'user_id',
        'skill_id',
        'created_at'
    ];

    protected $hidden = [
        'id',
    ];

    public function skill() {
        return $this->hasOne(CoreJobSkills::class, 'id', 'skill_id')->select('id', 'title');
    }
}
