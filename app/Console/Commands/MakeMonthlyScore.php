<?php

namespace App\Console\Commands;
use App\Result;
use App\User;
use App\Practice;
use App\SummaryMonthlyResult;
use App\SummaryTotalResult;
use Illuminate\Console\Command;

class MakeMonthlyScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_monthly_score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $last_day_of_current_month = date('Y-m-t 23:59:59');
        $first_day_of_current_month = date('Y-m-01 00:00:00');
        $year_month = date('Ym');
//$last_day_of_current_month = date('2020-11-30 23:59:59');
//$first_day_of_current_month = date('2020-11-01 00:00:00');
//$year_month = date('202011');

        $results = Result::where('created_at', '>=', $first_day_of_current_month)
               ->where('created_at', '<=', $last_day_of_current_month)
               ->whereNotNull('point')
               ->get();
        $users = User::all();
        
        $practices = Practice::where('start_at', '>=', $first_day_of_current_month)
               ->where('end_at', '<=', $last_day_of_current_month)
               ->where('id', '!=', 16)
               ->get();
        foreach ($practices as $practice) {
            $decode_player = json_decode($practice->players);
            foreach ($decode_player as $player) {
                $participants[$player][] = $practice->id;
            }
        }

        foreach ($users as $user) {
            $win_count = 0;
            $user1_results = $results->where('user1', $user->id)->all();
            $user2_results = $results->where('user2', $user->id)->all();
            $match_count = count($user1_results) + count($user2_results);
            foreach ($user1_results as $user1_result) {
                $user1_win_results = $results->whereNotIn('user1', [$user1_result->user1])->where('court', $user1_result->court)
                    ->where('match_id', $user1_result->match_id)->where('point', '<', $user1_result->point)->all();
                if (!empty($user1_win_results)) {
                    $win_count++;
                }
            }
            foreach ($user2_results as $user2_result) {
                $user2_win_results = $results->whereNotIn('user2', [$user2_result->user2])->where('court', $user2_result->court)
                    ->where('match_id', $user2_result->match_id)->where('point', '<', $user2_result->point)->all();
                if (!empty($user2_win_results)) {
                    $win_count++;
                }
            }

            $practice_count = (!empty($participants[$user->id])) ? count($participants[$user->id]) : 0;
            $is_evaluation_target = $practice_count >= count($practices) / 2;

            SummaryMonthlyResult::updateOrCreate(
                ['user_id' => $user->id, 'year_month' => $year_month], 
                ['total_count' => $match_count, 'win_count' => $win_count, 'practice_count' => $practice_count, 'is_evaluation_target' => $is_evaluation_target]
            );

        }

        $monthly_results = SummaryMonthlyResult::all();

        $start_day_of_three_month_practices = date("Y-m-d 23:59:59",strtotime("-90 day"));
        $last_day_of_three_month_practices = date("Y-m-d 23:59:59");

        $three_month_practices = Practice::where('start_at', '>=', $start_day_of_three_month_practices)
           ->where('end_at', '<=', $last_day_of_three_month_practices)
           ->where('id', '!=', 11)
           ->get();

        foreach ($users as $user) {
            $user_result = $monthly_results->where('user_id', $user->id);
            $total_count = $user_result->sum('total_count');
            $win_count = $user_result->sum('win_count');
            $practice_count = $user_result->sum('practice_count');
            $is_evaluation_target = $practice_count >= count($three_month_practices) * 0.3;

            SummaryTotalResult::updateOrCreate(
                ['user_id' => $user->id], 
                ['total_count' => $total_count, 'win_count' => $win_count, 'practice_count' => $practice_count, 'is_evaluation_target' => $is_evaluation_target]
            );

        }

        return 0;
    }
}
