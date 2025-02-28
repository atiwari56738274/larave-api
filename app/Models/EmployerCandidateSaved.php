<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users;

class EmployerCandidateSaved extends Model
{

    protected $table = 'employer_candidate_saved';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function employer() {
        return $this->hasOne(Jobs::class, 'id', 'employer_id');
    }

    public function candidate() {
        return $this->hasOne(Candidate::class, 'id', 'candidate_id');
    }

}
