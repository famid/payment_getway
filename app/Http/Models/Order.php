<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','payment_id','payment_date','amount','currency','payment_status','payment_method'];
}
