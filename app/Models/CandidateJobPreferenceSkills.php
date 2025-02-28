<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;


class CandidateJobPreferenceSkills extends Model
{

    protected $table = 'candidate_job_preference_skills';

    protected $fillable = [
        'uuid',
        'user_id',
        'preference_id',
        'skill_id',
        'created_at'
    ];

    protected $hidden = [
        'id'
    ];

    public function skill() {
        return $this->hasOne(CoreJobSkills::class, 'id', 'skill_id')->select('id', 'uuid', 'title', 'status')->where('status', 'active');
    }
}
