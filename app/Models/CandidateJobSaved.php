<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use App\Models\Jobs;

class CandidateJobSaved extends Model
{

    protected $table = 'candidate_job_saved';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function job() {
        return $this->hasOne(Jobs::class, 'id', 'job_id');
    }

}
