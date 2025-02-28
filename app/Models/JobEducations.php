<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobEducations;

class JobEducations extends Model
{

    protected $table = 'job_educations';

    protected $fillable = [
        'uuid',
        'job_id',
        'job_education_id'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function job_education() {
        return $this->hasOne(CoreJobEducations::class, 'id', 'job_education_id')->select('id','uuid','title');
    }
}
