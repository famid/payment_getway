<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable =['user_id','subscription_id','started_at','ended_at','status','package_id','payment_method','order_id'];
}
