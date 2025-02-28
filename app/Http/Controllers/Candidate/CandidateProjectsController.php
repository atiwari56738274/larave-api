<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateProjects;
use App\Models\CandidateEmployment;
use App\Models\CandidateEducations;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class CandidateProjectsController extends Controller
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
                'project_tag' => 'required',
                'project_status' => 'required',
                'work_from_year' => 'required',
                'work_from_month' => 'required',
                'project_description' => 'required',
                'employment_type' => 'required',
                'team_size' => 'required',
                'role' => 'required',
                'role_description' => 'required',
                'skill_used' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $candidate_employment = CandidateEmployment::where('uuid',$request->candidate_employment_uuid)->first();

            $candidate_education = CandidateEducations::where('uuid',$request->candidate_education_uuid)->first();
       
            $record = new CandidateProjects();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->project_tag = $request->project_tag;
            if($request->project_tag == 'employee') {
                $record->candidate_employment_id = $employment->id;
                $record->candidate_education_id = NULL;
            }
            if($request->project_tag == 'education') {
                $record->candidate_employment_id = NULL;
                $record->candidate_education_id = $candidate_education->id;
            }
            $record->project_status = $request->project_status;
            $record->work_from_year = $request->work_from_year;
            $record->work_from_month = $request->work_from_month;
            $record->work_till_year = $request->work_till_year;
            $record->work_till_month = $request->work_till_month;
            $record->project_description = $request->project_description;
            $record->employment_type = $request->employment_type;
            $record->team_size = $request->team_size;
            $record->role = $request->role;
            $record->role_description = $request->role_description;
            $record->skill_used = $request->skill_used;
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
     * @param  \App\CandidateProjects  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateProjects  $id
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
     * @param  \App\CandidateProjects  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'project_tag' => 'required',
                'project_status' => 'required',
                'work_from_year' => 'required',
                'work_from_month' => 'required',
                'project_description' => 'required',
                'employment_type' => 'required',
                'team_size' => 'required',
                'role' => 'required',
                'role_description' => 'required',
                'skill_used' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateProjects::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $candidate_employment = CandidateEmployment::where('uuid',$request->candidate_employment_uuid)->first();

            $candidate_education = CandidateEducations::where('uuid',$request->candidate_education_uuid)->first();
       

            if ($record) {
                $record->project_tag = $request->project_tag;
                if($request->project_tag == 'employee') {
                    $record->candidate_employment_id = $candidate_employment->id;
                }
                if($request->project_tag == 'education') {
                    $record->candidate_education_id = $candidate_education->id;
                }
                $record->project_status = $request->project_status;
                $record->work_from_year = $request->work_from_year;
                $record->work_from_month = $request->work_from_month;
                $record->work_till_year = $request->work_till_year;
                $record->work_till_month = $request->work_till_month;
                $record->project_description = $request->project_description;
                $record->employment_type = $request->employment_type;
                $record->team_size = $request->team_size;
                $record->role = $request->role;
                $record->role_description = $request->role_description;
                $record->skill_used = $request->skill_used;
                $record->save();
                if($record){
                    return $this->simpleReturn('success', 'Updated successfully');
                }
            }
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateProjects  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = CandidateProjects::where('uuid', $uuid)->first();
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