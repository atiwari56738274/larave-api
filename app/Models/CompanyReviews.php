<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobTitle;
use App\Models\CompanyProfile;
use App\Models\CoreJobCity;
use App\Models\User;

class CompanyReviews extends Model
{

    protected $table = 'company_reviews';

    protected $hidden = [
        'id',
        'job_title_id',
        'job_location_id',
        'company_profile_id',
        'user_id',
        'updated_at',
        'deleted_at'
    ];

    public function job_title() {
        return $this->hasOne(CoreJobTitle::class, 'id', 'job_title_id')->select('id', 'uuid', 'title');
    }

    public function job_location() {
        return $this->hasOne(CoreJobCity::class, 'id', 'job_location_id')->select('id', 'uuid', 'title');
    }

    public function company_profile() {
        return $this->hasOne(CompanyProfile::class, 'id', 'company_profile_id')->select('id','uuid','company_name');
    }
    
    public function candidate() {
        return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'name');
    }

}
