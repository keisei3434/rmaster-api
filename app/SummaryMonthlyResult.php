<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SummaryMonthlyResult extends Model
{
    protected $fillable = ['user_id', 'total_count', 'win_count', 'practice_count', 'is_evaluation_target', 'year_month'];
}
