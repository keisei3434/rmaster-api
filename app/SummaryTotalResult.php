<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SummaryTotalResult extends Model
{
    protected $fillable = ['user_id', 'total_count', 'win_count', 'practice_count', 'is_evaluation_target'];
}
