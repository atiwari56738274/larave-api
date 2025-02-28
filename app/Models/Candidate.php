<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CandidateBasicDetails;
use App\Models\CandidateJobPreference;
use App\Models\CandidateEmployment;
use App\Models\CandidateProjects;
use App\Models\CandidateSkills;
use App\Models\CandidateItSkills;
use App\Models\CandidateEducations;
use App\Models\CandidateCertifications;
use App\Models\CandidateLanguages;

class Candidate extends Model
{

    protected $table = 'users';

    protected $hidden = [
        'user_type',
        'email_verified_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    public function basic_details() {
        return $this->hasOne(CandidateBasicDetails::class, 'user_id', 'id')->with(['city', 'state', 'country']);
    }

    public function job_preference() {
        return $this->hasMany(CandidateJobPreference::class, 'user_id', 'id')->with(['preference_skills', 'preference_location', 'department']);
    }

    public function employment() {
        return $this->hasMany(CandidateEmployment::class, 'user_id', 'id')->with(['job_title', 'offered_job_title', 'department']);
    }

    public function projects() {
        return $this->hasMany(CandidateProjects::class, 'user_id', 'id');
    }

    public function skills() {
        return $this->hasMany(CandidateSkills::class, 'user_id', 'id')->with(['skill']);
    }

    public function it_skills() {
        return $this->hasMany(CandidateItSkills::class, 'user_id', 'id')->with(['skill']);
    }

    public function education() {
        return $this->hasMany(CandidateEducations::class, 'user_id', 'id')->with(['education', 'education_course','education_course_specialty']);
    }

    public function certifications() {
        return $this->hasMany(CandidateCertifications::class, 'user_id', 'id');
    }
    
    public function candidate_languages() {
        return $this->hasMany(CandidateLanguages::class, 'user_id', 'id');
    }

    
    public function current_company_details() {
        return $this->hasOne(CandidateEmployment::class, 'user_id', 'id')
            ->where('is_current_employement', true)
            ->orderBy('id', 'desc')
            ->with(['department', 'job_title', 'offered_job_title']);
    }
}
