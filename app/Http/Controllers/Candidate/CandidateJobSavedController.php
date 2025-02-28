<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateJobSaved;
use App\Models\Jobs;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateJobSavedController extends Controller
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
    public function index(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CandidateJobSaved::select('candidate_job_saved.*')
                ->join('jobs', 'jobs.id', 'candidate_job_saved.job_id')
                ->where('candidate_job_saved.user_id', $user_id)
                ->where('jobs.deleted_at', NULL)
                ->with(['job.department', 'job.job_title'])
                ->with(['job.job_locations', 'job.job_skills', 'job.job_educations'])
                ->with(['job.city', 'job.state', 'job.country'])
                ->orderBy('candidate_job_saved.created_at', 'desc');                

            $records = $records->paginate($perPageCount);

            if ($records) {
                return $this->simpleReturn('success', $records);
            } else {
                return $this->simpleReturn('error', 'Record not found!', 404);
            }
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }   
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
                'job_uuid' => 'required',
                'status' => 'required|in:save,remove'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $user_id = Auth::user()->id;

            // check if job exists
            $jobRecord = Jobs::where('uuid', $request->job_uuid)->first();
            if (!$jobRecord) {
                return $this->simpleReturn('error', 'Jobs not exist!', 404);
            }

            // if job save status save
            if($request->status === 'save') {
                $record = CandidateJobSaved::where('job_id', $jobRecord->id)->where('user_id', $user_id)->first();
                if(!$record) {
                    $add = new CandidateJobSaved();
                    $add->uuid = (string) Str::uuid();
                    $add->user_id = $user_id;
                    $add->job_id = $jobRecord->id;
                    if($add->save()) {
                        return $this->simpleReturn('success', 'Job Saved!');
                    }
                    return $this->simpleReturn('error', 'Error in insertion');
                }
                return $this->simpleReturn('error', 'Already exists!', 400);
            } else {
                // if job save status is removed
                $record = CandidateJobSaved::where('job_id', $jobRecord->id)->where('user_id', $user_id)->first();
                if($record){
                    $record_delete = $record->delete();
                    if($record_delete) {
                        return $this->simpleReturn('success', 'Job Removed!');
                    }
                    return $this->simpleReturn('error', 'deletion error', 422);
                }
                return $this->simpleReturn('error', 'Invalid record!', 404);

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
     * @param  \App\CandidateJobSaved  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateJobSaved  $id
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
     * @param  \App\CandidateJobSaved  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateJobSaved  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }
}