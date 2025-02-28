<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SubscriptionFeature;
use App\Models\EmployerSubscriptions;
use Illuminate\Support\Facades\Auth;

class Subscriptions extends Model
{

    protected $table = 'subscriptions';

    protected $hidden = [
        'id',
        'updated_at'
    ];

    public function subscription_feature() {
        return $this->hasMany(SubscriptionFeature::class, 'subscription_id', 'id');
    }

    public function is_employer_subscribe() {
        return $this->hasOne(EmployerSubscriptions::class, 'subscription_id', 'id')->where('user_id', Auth::id());
    }
}
