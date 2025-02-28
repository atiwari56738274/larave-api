<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CoreJobSkills;

class ProductReviews extends Model
{

    protected $table = 'product_reviews';

    protected $hidden = [
        'id',
        'updated_at',
        'deleted_at',
    ];

}
