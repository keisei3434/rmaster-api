<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    protected $fillable = ['place_id', 'is_active', 'players', 'start_at', 'end_at'];
}
