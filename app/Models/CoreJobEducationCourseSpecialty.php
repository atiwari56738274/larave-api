<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobEducationCourse;

class CoreJobEducationCourseSpecialty extends Model
{
    use SoftDeletes; 

    protected $table = 'core_job_education_course_specialty';

    protected $hidden = [
        'id',
        'updated_at',
        'job_education_course_id',
        'deleted_at'
    ];

    public function core_job_education_course() {
        return $this->belongsTo(CoreJobEducationCourse::class, 'job_education_course_id', 'id')->with('core_job_education');
    }
    
}
