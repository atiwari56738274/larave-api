<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateProjects extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_projects';

    protected $hidden = [
        'id'
    ];
}
