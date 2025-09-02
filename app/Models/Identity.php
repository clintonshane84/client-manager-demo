<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    //
    protected $casts = ['value_encrypted' => 'encrypted:string'];
}
