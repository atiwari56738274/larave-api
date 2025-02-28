<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobCity;

class JobLocations extends Model
{
    protected $table = 'job_locations';

    protected $fillable = [
        'uuid',
        'job_id',
        'job_location_id'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    public function job_location() {
        return $this->hasOne(CoreJobCity::class, 'id', 'job_location_id')->select('id','uuid', 'title');
    }
}
