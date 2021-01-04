<?php


namespace App\Http\Controllers;

use App\Practice;

use Illuminate\Http\Request;

class PracticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Practice::orderBy('end_at')->get());
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
        return response()->json(Practice::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Practice::with('matches')->find($id));
    }

    public function getActivePractice()
    {
        date_default_timezone_set('Asia/Tokyo');
        $fromTime = date("Y-m-d H:i:s", strtotime('+30 min'));
        $toTime = date("Y-m-d H:i:s", strtotime('-30 min'));
        return response()->json(Practice::with('matches')->where('start_at', '<', $fromTime)->where('end_at', '>', $toTime)->first());  
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
        $practice = Practice::find($id);
        if ($request->has('is_active')) {
          $practice->where('is_active', 1)->update(['is_active' => 0]);
        }
        $practice->update($request->all());
        return response()->json($practice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(Practice::destroy($id));
    }
}
