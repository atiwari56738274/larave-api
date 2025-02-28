<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\States;

class Cities extends Model
{
    protected $table = 'cities';

    protected $hidden = [
        'id'
    ];

    public function states()
    {
        return $this->hasOne(States::class, 'state_id', 'uuid');
    }
}
