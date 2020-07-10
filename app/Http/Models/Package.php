<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable =['header','title','description','interval'];
}
