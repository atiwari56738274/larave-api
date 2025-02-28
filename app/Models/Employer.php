<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployerSubscriptions;
use App\Models\EmployerSubscriptionsDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Employer extends Model
{

    protected $table = 'users';

    protected $hidden = [
        'user_type',
        'email_verified_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    public function active_subscription($user_id = null) {
        if($user_id === null) {
            $user_id = Auth::id();
        }
        $today_date = new \DateTime();
        $today = $today_date->format('Y-m-d');

        return $this->hasOne(EmployerSubscriptions::class, 'user_id', 'id')->where('user_id', $user_id)->where('status', 'active')->where('validity', '>=' , $today)->orderBy('id', 'desc');
    }
}
