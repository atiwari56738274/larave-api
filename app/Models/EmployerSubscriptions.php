<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Subscriptions;
use App\Models\Users;
use App\Models\Employer;
use Illuminate\Support\Facades\Auth;

class EmployerSubscriptions extends Model
{

    protected $table = 'employer_subscriptions';

    protected $hidden = [
        'id',
        'subscription_id',
        'user_id',
        'deleted_at',
        'updated_at'
    ];

    public function subscriptions() {
        return $this->hasOne(Subscriptions::class, 'id', 'subscription_id');
    }

    public function is_subscribe() {
        return $this->hasOne(Users::class, 'id', 'user_id')->where('user_id', Auth::id());
    }

    public function employer() {
        return $this->hasOne(Employer::class, 'id', 'user_id');
    }

}
