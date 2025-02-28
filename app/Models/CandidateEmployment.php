<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobTitle;
use App\Models\CoreJobDepartments;

class CandidateEmployment extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_employment';

    protected $hidden = [
        'id'
    ];

    public function job_title() {
        return $this->hasOne(CoreJobTitle::class, 'id', 'job_title_id')->select('id','uuid','title');
    }
    
    public function offered_job_title() {
        return $this->hasOne(CoreJobTitle::class, 'id', 'offered_job_title_id')->select('id','uuid','title');
    }

    public function department() {
        return $this->hasOne(CoreJobDepartments::class, 'id', 'department_id')->select('id','uuid', 'title');
    }
}
