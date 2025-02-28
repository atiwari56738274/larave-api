<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateSkills;
use App\Models\CoreJobSkills;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateSkillsController extends Controller
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
                'skill_uuids' => 'required|array'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            if(count($request->skill_uuids) === 0) {
                return $this->simpleReturn('error', 'Please select atleast 1 skill');
            } 
            $skillArray = array();
            foreach ($request->skill_uuids as $uuid) {
                $skill = CoreJobSkills::where('uuid', $uuid)->first();
                if (!$skill) {
                    return $this->simpleReturn('error', 'Skill not found by UUID', 404);
                }
                    $skill_id = $skill->id; 

                $skill_exist = CandidateSkills::where('user_id', Auth::user()->id)->where('skill_id', $skill_id)->first();
                if ($skill_exist === null) {
                    $skill_records = CandidateSkills::create([
                        'uuid' => (string) Str::uuid(),
                        'user_id' => Auth::user()->id,
                        'skill_id' => $skill_id
                    ]);
                }
                $skillArray[] = $skill_id;
            }  
        

            if(count($skillArray) > 0) {
                $skill_records_delete = CandidateSkills::where('user_id', Auth::user()->id)->whereNotIn('skill_id', $skillArray)->delete();
            }
            return $this->simpleReturn('success', 'Added successfully');

        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CandidateSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateSkills  $id
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
     * @param  \App\CandidateSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateSkills  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }
}