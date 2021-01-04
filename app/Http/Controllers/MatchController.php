<?php

namespace App\Http\Controllers;

use App\Match;
use App\Result;
use App\Practice;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Match::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $practice_id = $request->practice_id;
        $participants = json_decode($request->participants);
        $court_count = $this->getCourtCount($participants);
        $matches = Practice::with('matches')->where('id', $practice_id)->first()->matches;

        $last_match_id = $matches->max('match_id');
        $last_matches = $matches->where('match_id', $last_match_id)->all();

        $last_attendances = [];
        $last_attendances_in_particepants = [];
        foreach ($last_matches as $last_match) {
          array_push($last_attendances, $last_match->user1);
          array_push($last_attendances, $last_match->user2);
        }
        
        $last_attendances_in_particepants = array_intersect($participants, $last_attendances);
        $last_absenees = array_diff($participants, $last_attendances_in_particepants);

        $previous_results = [];
        foreach ($last_attendances_in_particepants as $last_attendance) {
            $previous_results[$last_attendance] = $matches->where('user1', $last_attendance)->count() + $matches->where('user2', $last_attendance)->count();
        }
        asort($previous_results);
        $prioritized_participants = [];

        foreach ($last_absenees as $last_absenee) {
            array_push($prioritized_participants, $last_absenee);
        }
        foreach ($previous_results as $user_id => $previous_result) {
            array_push($prioritized_participants, $user_id);
        }
        
        $hasDuplicatePair = false;
        $i = 0;

        do {
            $hasDuplicatePair = false;
            $decided_matches = $this->makeMatches($prioritized_participants, $court_count);
            foreach ($decided_matches as $index => $decided_match) {
                if ($index % 2 == 0) {
                    $case1 = $matches->where('match_id', $last_match_id)->where('user1', $decided_matches[$index])->where('user2', $decided_matches[$index + 1])->all();
                    $case2 = $matches->where('match_id', $last_match_id)->where('user2', $decided_matches[$index])->where('user1', $decided_matches[$index + 1])->all();
                    $case3 = $matches->where('match_id', $last_match_id - 1)->where('user1', $decided_matches[$index])->where('user2', $decided_matches[$index + 1])->all();
                    $case4 = $matches->where('match_id', $last_match_id - 1)->where('user2', $decided_matches[$index])->where('user1', $decided_matches[$index + 1])->all();
                    if (!empty($case1) || !empty($case2) || !empty($case3) || !empty($case4)) {
                        $hasDuplicatePair = true;
                    }
                }
            }
            $i++;
        } while ($i < 100 && $hasDuplicatePair == true);

        $match = Match::create(['practice_id' => $request->practice_id, 'type' => $request->type]);
        $number = 1;
        $chunked_participants = array_chunk($decided_matches, 4);

        foreach ($chunked_participants as $grouped_participant) {
          if (count($grouped_participant) == 4) {
            $pair_number = 1;
            foreach (array_chunk($grouped_participant, 2) as $pair) {
              Result::create(['match_id' => $match->id, 'court' => $number , 'pair' => $pair_number, 'user1' => $pair[0], 'user2' => $pair[1] ]);
              $pair_number = $pair_number + 1;
            }
            $number = $number + 1;
          }
        }        

        return response()->json($i);
    }

    private function makeMatches($prioritized_participants, $court_count) {
        $matches = [];
        for ($i = 0; $i < $court_count * 4; $i++) {
            $matches[$i] = $prioritized_participants[$i];
        }

        shuffle($matches);
        return $matches;
    }

    private function getCourtCount ($member) {
        if (count($member) < 4) {
            return 0;
        } elseif (count($member) >= 4 && count($member) < 8) {
            return 1;
        } elseif (count($member) >= 8 && count($member) < 12) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Match::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $match = Match::find($id);
        $match->update($request->all());
        return response()->json($match);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(Match::destroy($id));
    }
}
