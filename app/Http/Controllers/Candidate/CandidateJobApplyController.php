<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateJobSaved;
use App\Models\CandidateJobApply;
use App\Models\Jobs;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateJobApplyController extends Controller
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

            $records = CandidateJobApply::select('candidate_job_apply.*', 'company_profile.company_logo as company_logo')
                ->join('jobs', 'jobs.id', 'candidate_job_apply.job_id')
                ->leftjoin('company_profile', 'company_profile.user_id', '=', 'jobs.user_id')
                ->where('candidate_job_apply.user_id', $user_id)
                ->where('jobs.deleted_at', NULL)
                ->with(['job.department', 'job.job_title'])
                ->with(['job.job_locations', 'job.job_skills', 'job.job_educations'])
                ->with(['job.city', 'job.state', 'job.country'])
                ->orderBy('candidate_job_apply.created_at', 'desc');

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
                'is_new_resume' => 'required',
                'new_resume' => 'required_if:is_new_resume,==,true',
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


            // check if job already applied
            $jobApplyied = CandidateJobApply::where('user_id', Auth::user()->id)->where('job_id', $jobRecord->id)->first();
            if ($jobApplyied) {
                return $this->simpleReturn('error', 'Already Applied!', 404);
            }

            $fileNameResume = null;
            if (request()->hasFile('new_resume')) {
                $file = request()->file('new_resume');
                $fileNameResume = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('./uploads/candidate/resume/', $fileNameResume);
            }

            $record = new CandidateJobApply();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->job_id = $jobRecord->id;
            $record->is_new_resume =( $request->is_new_resume) ? 1 : 0;
            $record->new_resume = ($request->is_new_resume && (request()->hasFile('new_resume'))) ? $fileNameResume : NULL;
            $record->apply_note = $request->apply_note;
            $record->status = "applied";
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