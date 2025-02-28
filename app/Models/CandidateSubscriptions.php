<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscriptions;

class CandidateSubscriptions extends Model
{

    protected $table = 'candidate_subscriptions';

    protected $hidden = [
        'id',
        'updated_at'
    ];

}
