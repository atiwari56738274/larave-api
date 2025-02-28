<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobCity;

class CandidateJobPreferenceLocations extends Model
{

    protected $table = 'candidate_job_preference_locations';

    protected $fillable = [
        'uuid',
        'user_id',
        'preference_id',
        'location_id',
        'created_at'
    ];

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function location() {
        return $this->hasOne(CoreJobCity::class, 'id', 'location_id')->select('id', 'uuid', 'title', 'status')->where('status', 'active');
    }
}
