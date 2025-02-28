<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobEducations;
use App\Models\CoreJobEducationCourse;
use App\Models\CoreJobEducationCourseSpecialty;

class CandidateEducations extends Model
{
    use SoftDeletes; 

    protected $table = 'candidate_education';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function education() {
        return $this->hasOne(CoreJobEducations::class, 'id', 'education_id')->select('id','uuid', 'title');
    }

    public function education_course() {
        return $this->hasOne(CoreJobEducationCourse::class, 'id', 'course_id')->select('id','uuid', 'title');
    }

    public function education_course_specialty() {
        return $this->hasOne(CoreJobEducationCourseSpecialty::class, 'id', 'specialization_id')->select('id','uuid','title');
    }
}
