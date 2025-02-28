<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscriptions;

class SubscriptionFeature extends Model
{

    protected $table = 'subscription_feature';

    protected $fillable = [
        'uuid',
        'subscription_id',
        'feature',
        'sort_order',
    ];

    protected $hidden = [
        'id',
        'updated_at'
    ];

}
