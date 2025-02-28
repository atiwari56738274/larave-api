<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use App\Models\CompanyReviews;
use App\Models\CompanyInterviewProcess;
use App\Models\CompanyMedia;
use App\Models\Jobs;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';

    protected $hidden = [
        'id',
        'city_id',
        'state_id',
        'country_id',
        'user_id',
        'added_by',
        'updated_by',
        'updated_at'
    ];

    public function city() {
        return $this->hasOne(Cities::class, 'id', 'city_id')->select('id', 'name');
    }

    public function state() {
        return $this->hasOne(States::class, 'id', 'state_id')->select('id', 'name');
    }

    public function country() {
        return $this->hasOne(Countries::class, 'id', 'country_id')->select('id', 'name');
    }

    public function reviews() {
        return $this->hasMany(CompanyReviews::class, 'company_profile_id', 'id')->with(['job_title', 'job_location']);
    }

    public function company_interview_process() {
        return $this->hasMany(CompanyInterviewProcess::class, 'user_id', 'user_id')->where('status', 'active');
    }

    public function company_media() {
        return $this->hasMany(CompanyMedia::class, 'user_id', 'user_id')->where('status', 'active');
    }

    public function jobs() {
        return $this->hasMany(Jobs::class, 'user_id', 'user_id')->where('status', 'active')->where('is_published', '1');
    }
}
