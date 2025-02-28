<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobTitle;

class CandidateCertifications extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_certifications';

    protected $hidden = [
        'id',
        'updated_at'
    ];

}
