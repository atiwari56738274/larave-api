<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscriptions;
use App\Models\EmployerSubscriptions;
use App\Models\Employer;
use Illuminate\Support\Facades\Auth;

class SubscriptionsMannualEmployer extends Model
{
    use SoftDeletes; 

    protected $table = 'subscriptions_mannual_employer';

    protected $hidden = [
        'id',
        'user_id',
        'subscription_id',
        'employer_subscription_id',
        'updated_at',
        'deleted_at'
    ];

    public function subscription() {
        return $this->hasMany(Subscriptions::class, 'id', 'subscription_id')->select('id', 'title', 'description');
    }

    public function employer() {
        return $this->hasOne(Employer::class, 'id', 'user_id')->select('id', 'name', 'email', 'phone');
    }
}
