<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CompanyMedia extends Model
{
    use SoftDeletes;

    protected $table = 'company_media';

    protected $hidden = [
        'id',
        'user_id',
        'deleted_at',
        'updated_at'
    ];
}
