<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscriptions;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

class EmployerSubscriptionsDetails extends Model
{

    protected $table = 'employer_subscriptions_details';

    protected $hidden = [
        'id',
        'employer_subscriptions_id',
        'user_id',
        'deleted_at',
        'updated_at'
    ];

    public function subscriptions() {
        return $this->hasOne(Subscriptions::class, 'id', 'subscription_id');
    }

}
