<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class JobSkills extends Model
{

    protected $table = 'job_skills';

    protected $fillable = [
        'uuid',
        'job_id',
        'job_skill_id'
    ];
    
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function job_skill() {
        return $this->hasOne(CoreJobSkills::class, 'id', 'job_skill_id')->select('id','uuid','title');
    }
}
