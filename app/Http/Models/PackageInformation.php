<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageInformation extends Model
{
    protected $fillable = ['package_id','amount','currency'];
}
