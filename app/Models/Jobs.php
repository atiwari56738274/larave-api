<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use App\Models\CoreJobDepartments;
use App\Models\CoreJobTitle;
use App\Models\JobEducations;
use App\Models\JobLocations;
use App\Models\JobSkills;
use App\Models\CandidateJobSaved;
use App\Models\CandidateJobApply;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Auth;

class Jobs extends Model
{
    use SoftDeletes; 

    protected $table = 'jobs';

    protected $hidden = [
        'id',
        'deleted_at'
    ];

    public function department() {
        return $this->hasOne(CoreJobDepartments::class, 'id', 'department_id')->select('id', 'title', 'uuid');
    }

    public function job_title() {
        return $this->hasOne(CoreJobTitle::class, 'id', 'job_title_id')->select('id', 'title', 'uuid');
    }

    public function city() {
        return $this->hasOne(Cities::class, 'id', 'city_id')->select('id', 'name', 'uuid');
    }

    public function state() {
        return $this->hasOne(States::class, 'id', 'state_id')->select('id', 'name', 'uuid');
    }

    public function country() {
        return $this->hasOne(Countries::class, 'id', 'country_id')->select('id', 'name', 'uuid');
    }

    public function job_locations() {
        return $this->hasMany(JobLocations::class, 'job_id', 'id')->with('job_location');
    }

    public function job_skills() {
        return $this->hasMany(JobSkills::class, 'job_id', 'id')->with('job_skill');
    }

    public function job_educations() {
        return $this->hasMany(JobEducations::class, 'job_id', 'id')->with('job_education');
    }

    public function candidate_job_saved() {
        return $this->hasMany(CandidateJobSaved::class, 'job_id', 'id')->where('user_id', Auth::guard('sanctum')->user()->id);
    }

    public function candidate_job_apply() {
        return $this->hasOne(CandidateJobApply::class, 'job_id', 'id')->where('user_id', Auth::guard('sanctum')->user()->id);
    }

    public function shortlisted_candidate() {
        return $this->hasMany(CandidateJobApply::class, 'job_id', 'id')->where('status', 'shortlist');
    }

    public function applicants() {
        return $this->hasMany(CandidateJobApply::class, 'job_id', 'id');
    }

    public function company() {
        return $this->hasOne(CompanyProfile::class, 'user_id', 'user_id')->select('id', 'company_name', 'uuid');
    }
}
