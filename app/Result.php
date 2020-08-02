<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['match_id', 'user1', 'user2', 'point'];
}
