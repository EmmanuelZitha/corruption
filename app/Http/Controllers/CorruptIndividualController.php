<?php

namespace App\Http\Controllers;

use App\Models\Corrupt_Individual;
use Illuminate\Http\Request;

class CorruptIndividualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get covertypes list
        $individual=CorruptIndividual::all();
        //custom response
        $response=[
            "success"=>true,
            "message"=>"roles list",
            "data"=>$roles
        ];
        //return response
        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //input validation
        $request->validate([
            "validate"=>"name"
        ]);
        //check if record exists
        $Individual=CorruptIndividual::where('name',$request->name)->whereNotNull('name')->exists();//returns true or false
        if ($Individual){
            //generate response
            $response=[
                "success"=>false,
                "message"=>"This record already exists"
            ];
            //return custom response
            return response()->json($response,400);
        }else
        //save a new record
        $corrupt_individual=CorruptIndividual::create([
            "name"=>$request->name
        ]);
        //generate custom response
        $response=[
            "success"=>true,
            "message"=>"record with name ".$request->name." created successfully"
        ];
        //return custom response
        return response()->json($response,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Corrupt_Individual  $corrupt_Individual
     * @return \Illuminate\Http\Response
     */
    public function show(Corrupt_Individual $corrupt_Individual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Corrupt_Individual  $corrupt_Individual
     * @return \Illuminate\Http\Response
     */
    public function edit(Corrupt_Individual $corrupt_Individual)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Corrupt_Individual  $corrupt_Individual
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Corrupt_Individual $corrupt_Individual)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Corrupt_Individual  $corrupt_Individual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Corrupt_Individual $corrupt_Individual)
    {
        //
    }
}
