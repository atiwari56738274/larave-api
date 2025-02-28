<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use App\Models\Jobs;
use App\Models\Candidate;
use App\Models\CandidateJobInterview;

class CandidateJobApply extends Model
{

    protected $table = 'candidate_job_apply';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function job() {
        return $this->hasOne(Jobs::class, 'id', 'job_id');
    }

    public function candidate() {
        return $this->hasOne(Candidate::class, 'id', 'user_id')->with('basic_details','it_skills','current_company_details');
    }

    public function interview_schedule() {
        return $this->hasOne(CandidateJobInterview::class, 'candidate_job_apply_id', 'id');
    }

}
