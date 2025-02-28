<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Countries;

class States extends Model
{
    protected $table = 'states';

    protected $hidden = [
        'id'
    ];

  public function country()
{
    return $this->belongsTo(Countries::class, 'country_id', 'uuid');  // Assuming 'uuid' is the unique identifier
}
}
