<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateSubscriptions;
use App\Models\Subscriptions;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateSubscriptionsController extends Controller
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
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = Subscriptions::select('uuid', 'type', 'title', 'description', 'mrp_amount', 'amount', 'logo', 'validity', 'validity_type', 'profile_creation', 'no_of_job_apply', 'resume_builder', 'notification_via_email_sms_whatsapp')->where('type', 'candidate');
        
            $records = $records->where('status', 'active')->orderBy('sort_order', 'ASC')->paginate($perPageCount);

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
                'education_id' => 'required',
                'course_id' => 'required',
                'insitute_university' => 'required',
                'course_type' => 'required',
                'course_duration_start_year' => 'required',
                'course_duration_end_year' => 'required',
                'grading_system' => 'required',
                'marks' => 'required',
                'primary_education' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = new CandidateSubscriptions();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->education_id = $request->education_id;
            $record->course_id = $request->course_id;
            $record->insitute_university = $request->insitute_university;
            $record->course_type = $request->course_type;
            $record->course_duration_start_year = $request->course_duration_start_year;
            $record->course_duration_end_year = $request->course_duration_end_year;
            $record->specialization = $request->specialization;
            $record->grading_system = $request->grading_system;
            $record->marks = $request->marks;
            $record->primary_education = $request->primary_education;
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
     * @param  \App\CandidateSubscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateSubscriptions  $id
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
     * @param  \App\CandidateSubscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            $rules = array(
                'education_id' => 'required',
                'course_id' => 'required',
                'insitute_university' => 'required',
                'course_type' => 'required',
                'course_duration_start_year' => 'required',
                'course_duration_end_year' => 'required',
                'grading_system' => 'required',
                'marks' => 'required',
                'primary_education' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateSubscriptions::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->education_id = $request->education_id;
                $record->course_id = $request->course_id;
                $record->insitute_university = $request->insitute_university;
                $record->course_type = $request->course_type;
                $record->course_duration_start_year = $request->course_duration_start_year;
                $record->course_duration_end_year = $request->course_duration_end_year;
                $record->specialization = $request->specialization;
                $record->grading_system = $request->grading_system;
                $record->marks = $request->marks;
                $record->primary_education = $request->primary_education;
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
     * @param  \App\CandidateSubscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $record = CandidateSubscriptions::where('id', $id)->first();
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