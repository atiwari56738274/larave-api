<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobEducationCourse;

class CoreJobEducations extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_educations';


    protected $hidden = [
        'id',
        'updated_at',
        'deleted_at'
    ];


    public function core_job_education_course() {
        return $this->hasMany(CoreJobEducationCourse::class, 'job_education_id', 'id')->with('core_job_education_course_specialty');
    }
}
