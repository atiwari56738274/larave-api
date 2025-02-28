<?php

namespace App\Http\Controllers\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateJobApply;
use App\Models\CandidateJobInterview;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApplicantInterviewScheduleController extends Controller
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
            $today_date = date('Y-m-d');

            $records = CandidateJobInterview::select('candidate_job_interview.*', 'jobs.hiring_company_name', 'core_job_title.title as job_title', 'core_job_department.title as job_department', 'users.name as candidate_name', 'users.phone as candidate_phone', 'users.email as candidate_email')
                ->join('candidate_job_apply', 'candidate_job_apply.id', 'candidate_job_interview.candidate_job_apply_id')
                ->join('jobs', 'jobs.id', 'candidate_job_apply.job_id')
                ->join('core_job_title', 'core_job_title.id', 'jobs.job_title_id')
                ->join('core_job_department', 'core_job_department.id', 'jobs.department_id')
                ->join('users', 'users.id', 'candidate_job_apply.user_id')
                ->where('jobs.user_id', $user_id);

                if ($request->status && ($request->status != '')) {
                    $records->where('candidate_job_interview.status', $request->status);
                }

                if ($request->type && ($request->type === 'past')) {
                    $records->where('candidate_job_interview.interview_date', '<', $today_date);
                }
                if ($request->type && ($request->type === 'today')) {
                    $records->where('candidate_job_interview.interview_date', $today_date);
                }
                if ($request->type && ($request->type === 'upcoming')) {
                    $records->where('candidate_job_interview.interview_date', '>=', $today_date);
                }
                
                $records->orderBy('candidate_job_interview.created_at', 'desc');

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
                'applicant_uuid' => 'required',
                'duration' => 'required',
                'call_type' => 'required',
                'message' => 'required',
                'hiring_team_email' => 'required',
                'interview_date' => 'required',
                'interview_time' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $jobApply = CandidateJobApply::where('uuid', $request->applicant_uuid)->first();
            if (!$jobApply) {
                return $this->simpleReturn('error', 'Not valid!', 404);
            }

            $jobInterviewExist = CandidateJobInterview::where('candidate_job_apply_id', $jobApply->id)->first();
            if ($jobInterviewExist) {
                return $this->simpleReturn('error', 'Already scheduled!', 400);
            }

            $record = new CandidateJobInterview();
            $record->uuid = (string) Str::uuid();
            $record->candidate_job_apply_id = $jobApply->id;
            $record->duration = $request->duration;
            $record->call_type = $request->call_type;
            $record->message = $request->message;
            $record->hiring_team_email = $request->hiring_team_email;
            $record->interview_date = $request->interview_date;
            $record->interview_time = $request->interview_time;
            $record->status = "scheduled";
            if($record->save()) {
                $jobApply->status = "interview_scheduled";
                $jobApply->save();

                return $this->simpleReturn('success', 'Scheduled successfully');
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
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid)
    {
        //
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyProfile  $id
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
     * @param  \App\Jobs  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'duration' => 'required',
                'call_type' => 'required',
                'message' => 'required',
                'hiring_team_email' => 'required',
                'interview_date' => 'required',
                'interview_time' => 'required',
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateJobInterview::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->duration = $request->duration;
                $record->call_type = $request->call_type;
                $record->message = $request->message;
                $record->hiring_team_email = $request->hiring_team_email;
                $record->interview_date = $request->interview_date;
                $record->interview_time = $request->interview_time;
                $record->status = $request->status;
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
     * @param  \App\Jobs  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }
}