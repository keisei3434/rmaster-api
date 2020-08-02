<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    protected $fillable = ['place_id', 'start_at', 'end_at'];
}
