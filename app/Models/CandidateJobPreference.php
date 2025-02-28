<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CandidateJobPreferenceSkills;
use App\Models\CandidateJobPreferenceLocations;
use App\Models\CoreJobDepartments;
use App\Models\CandidateBasicDetails;
use App\Models\Candidate;
use App\Models\CandidateEmployment;
use App\Models\CandidateSkills;
use App\Models\EmployerCandidateSaved;

class CandidateJobPreference extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_job_preference';

    protected $hidden = [
        'id',
        'skill_id',
        'user_id',
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    public function department() {
        return $this->hasOne(CoreJobDepartments::class, 'id', 'department_id')->select('uuid', 'id', 'title');
    }

    public function candidate() {
        return $this->hasOne(Candidate::class, 'id', 'user_id');
    }

    public function preference_skills() {
        return $this->hasMany(CandidateJobPreferenceSkills::class, 'preference_id', 'id')->with('skill')->select('id', 'uuid', 'preference_id', 'skill_id');
    }

    public function preference_location() {
        return $this->hasMany(CandidateJobPreferenceLocations::class, 'preference_id', 'id')->with('location')->select('id','uuid', 'preference_id', 'location_id');
    }

    public function candidate_basic_details() {
        return $this->hasOne(CandidateBasicDetails::class, 'user_id', 'user_id');
    }

    public function current_company_details() {
        return $this->hasOne(CandidateEmployment::class, 'user_id', 'user_id')->with('job_title')->where('is_current_employement', '1')->orderBy('id', 'desc');
    }

    public function skills() {
        return $this->hasMany(CandidateSkills::class, 'user_id', 'user_id')->with(['skill']);
    }

    public function candidate_bookmarked() {
        return $this->hasOne(EmployerCandidateSaved::class, 'candidate_id', 'user_id');
    }


}
