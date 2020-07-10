<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Mandate extends Model
{
    protected $fillable =['user_id','mandata_id','user_account','user_bic','signature_date','mandate_reference'];
}
