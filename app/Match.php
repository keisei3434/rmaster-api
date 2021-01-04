<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $fillable = ['practice_id', 'link', 'type'];

    public function results()
    {
        return $this->hasMany('App\Result');
    }
}
