<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;

class CandidateBasicDetails extends Model
{

    protected $table = 'candidate_basic_details';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function city() {
        return $this->hasOne(Cities::class, 'id', 'city_id')->select('id', 'uuid', 'name');
    }

    public function state() {
        return $this->hasOne(States::class, 'id', 'state_id')->select('id', 'uuid', 'name');
    }

    public function country() {
        return $this->hasOne(Countries::class, 'id', 'country_id')->select('id', 'uuid', 'name');
    }
}
