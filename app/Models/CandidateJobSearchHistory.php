<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users;

class CandidateJobSearchHistory extends Model
{

    protected $table = 'candidate_job_search_history';

    protected $hidden = [
        'id',
        'updated_at'
    ];

}
