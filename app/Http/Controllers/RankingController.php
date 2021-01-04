<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SummaryTotalResult;
use App\SummaryMonthlyResult;
use App\User;

class RankingController extends Controller
{
    public function getRankingSummary() {
        $total_results = SummaryTotalResult::where('is_evaluation_target', 1)
            ->join('users','users.id','=','summary_total_results.user_id')->orderBy('total_count', 'desc')->get();

        $total_results_man = $total_results->where('gender', 1)->toArray();
        uasort($total_results_man, [$this, 'sortByWinRatio']);

        $total_man = [];
        foreach ($total_results_man as $total_result_man) {
            array_push($total_man, [
                'name' => $total_result_man['name'],
                'total' => $total_result_man['total_count'],
                'win' => $total_result_man['win_count'],
            ]);
        }

        $total_results_woman = $total_results->where('gender', 2)->toArray();
        uasort($total_results_woman, [$this, 'sortByWinRatio']);

        $total_woman = [];
        foreach ($total_results_woman as $total_result_woman) {
            array_push($total_woman, [
                'name' => $total_result_woman['name'],
                'total' => $total_result_woman['total_count'],
                'win' => $total_result_woman['win_count'],
            ]);
        }

        $all_monthly_results = SummaryMonthlyResult::where('is_evaluation_target', 1)
            ->join('users','users.id','=','summary_monthly_results.user_id')->orderBy('total_count', 'desc')->get();

        $man_monthly_results = [];

        for ($i=0; $i < 12; $i++) {
            $year_month = date("Ym", strtotime("-$i month"));
            $monthly_results_man = $all_monthly_results->where('gender', 1)->where('year_month', $year_month)->toArray();
            uasort($monthly_results_man, [$this, 'sortByWinRatio']);
            $monthly_man = [];
            foreach ($monthly_results_man as $monthly_result_man) {
                array_push($monthly_man, [
                    'name' => $monthly_result_man['name'],
                    'total' => $monthly_result_man['total_count'],
                    'win' => $monthly_result_man['win_count'],
                ]);
            }
            $year_month_key = date("Y-m", strtotime("-$i month"));
            if (!empty($monthly_man)) {
                $man_monthly_results[] = ['date' => $year_month_key, 'ranking' => $monthly_man];
            }
        }

        $woman_monthly_results = [];

        for ($i=0; $i < 12; $i++) {
            $year_month = date("Ym", strtotime("-$i month"));
            $monthly_results_woman = $all_monthly_results->where('gender', 2)->where('year_month', $year_month)->toArray();
            uasort($monthly_results_woman, [$this, 'sortByWinRatio']);
            $monthly_woman = [];
            foreach ($monthly_results_woman as $monthly_result_woman) {
                array_push($monthly_woman, [
                    'name' => $monthly_result_woman['name'],
                    'total' => $monthly_result_woman['total_count'],
                    'win' => $monthly_result_woman['win_count'],
                ]);
            }
            $year_month_key = date("Y-m", strtotime("-$i month"));
            if (!empty($monthly_woman)) {
                $woman_monthly_results[] = ['date' => $year_month_key, 'ranking' => $monthly_woman];
            }
        }

        $result = [
            'total' => ['man' => $total_man, 'woman' => $total_woman],
            'monthly' => [
                'man' => $man_monthly_results,
                'woman' => $woman_monthly_results,
            ]
        ];


        return $result;

        $total_man = [
            ['name' => 'kokosu', 'total' => 20, 'win' => 18],
            ['name' => 'inu', 'total' => 20, 'win' => 18],
            ['name' => 'kaede', 'total' => 20, 'win' => 18]
        ];

        $total_woman = [ 
            ['name' => 'kokosu2', 'ranking' => 1, 'total' => 20, 'win' => 18],
            ['name' => 'inu2', 'ranking' => 2, 'total' => 20, 'win' => 18],
            ['name' => 'kaede2', 'ranking' => 3, 'total' => 20, 'win' => 18]
        ];

        $total = [
            'total' => ['man' => $total_man, 'woman' => $total_woman],
            'monthly' => [
                'man' => [
                    ['date' =>'2020-08', 'ranking' => $total_man],
                    ['date' =>'2020-09', 'ranking' => $total_man],
                    ['date' =>'2020-10', 'ranking' => $total_man],
                ],
                'woman' => [
                    ['date' =>'2020-08', 'ranking' => $total_woman],
                    ['date' =>'2020-09', 'ranking' => $total_woman],
                    ['date' =>'2020-10', 'ranking' => $total_woman],
                ]
            ]
        ];

        return $total;
    }

    function sortByWinRatio($a, $b) {
        $a_win_ratio = $a['total_count'] == 0 ? 0 : $a['win_count'] / $a['total_count'];
        $b_win_ratio = $b['total_count'] == 0 ? 0 : $b['win_count'] / $b['total_count'];

        if ($a_win_ratio == $b_win_ratio) {
            return 0;
        }
        return ($a_win_ratio < $b_win_ratio) ? 1 : -1;
    }
}
