<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobEducations;
use App\Models\CoreJobEducationCourseSpecialty;

class CoreJobEducationCourse extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_education_course';

    protected $hidden = [
        'id',
        'updated_at',
        'job_education_id',
        'deleted_at'
    ];


    public function core_job_education() {
        return $this->belongsTo(CoreJobEducations::class, 'job_education_id', 'id');
    }


    public function core_job_education_course_specialty() {
        return $this->hasMany(CoreJobEducationCourseSpecialty::class, 'job_education_course_id', 'id');
    }
}
