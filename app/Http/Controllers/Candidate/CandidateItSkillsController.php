<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateItSkills;
use App\Models\CoreJobSkills;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateItSkillsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //   
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
        try {
            $rules = array(
                'skill_uuid' => 'required',
                'versions' => 'nullable',
                'experience_year' => 'required',
                'experience_month' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $skills = CoreJobSkills::where('uuid', $request->skill_uuid)->first();
            if(!$skills){
                return $this->simpleReturn('error','Skills not found by UUID', 404);
            }

            $record = new CandidateItSkills();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->skill_id = $skills->id;
            $record->versions = $request->versions;
            $record->experience_year = $request->experience_year;
            $record->experience_month = $request->experience_month;
            if($record->save()) {
                return $this->simpleReturn('success', 'Added successfully');
            }
            return $this->simpleReturn('error', 'Error in insertion');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CandidateItSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateItSkills  $id
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
     * @param  \App\CandidateItSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        try {
            $rules = [
                'skill_uuid' => 'required',
                'versions' => 'nullable',
                'experience_year' => 'required',
                'experience_month' => 'required'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }
    
           
            $skills = CoreJobSkills::where('uuid', $request->skill_uuid)->first();
            if (!$skills) {
                return $this->simpleReturn('error','Skills not found by UUID', 404);
            }
    
            $record = CandidateItSkills::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('Record not found', 404);
            }
    
            $record->skill_id = $skills->id; 
            $record->versions = $request->versions;
            $record->experience_year = $request->experience_year;
            $record->experience_month = $request->experience_month;
            $record->updated_at = now();
    
          
            if ($record->save()) {
                return $this->simpleReturn('success', 'Updated successfully');
            }
    
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateItSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = CandidateItSkills::where('uuid', $uuid)->first();
            if($record){
                $record_delete = $record->delete();
                if($record_delete){
                    return $this->simpleReturn('success', 'Successfully deleted');
                }
                return $this->simpleReturn('error', 'deletion error', 422);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }  
    }
}