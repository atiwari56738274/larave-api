<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateEmployment;
use App\Models\CoreJobDepartments;
use App\Models\CoreJobTitle;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateEmploymentController extends Controller
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
                'is_current_employement' => 'required',
                'department_uuid' => 'required',
                'employement_type' => 'required',
                'job_title_uuid' => 'required',
                'start_year' => 'required',
                'start_month' => 'required',
                'job_profile' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $department  = CoreJobDepartments::where('uuid',$request->department_uuid)->first();
            if(!$department){
                return $this->simpleReturn('error','Department not found by UUID', 404);
            }

            $job_title  = CoreJobTitle::where('uuid',$request->job_title_uuid)->first();
            if(!$department){
                return $this->simpleReturn('error','Job_title not found by UUID', 404);
            }

            $record = new CandidateEmployment();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->is_current_employement = $request->is_current_employement;
            $record->department_id = $department->id;
            $record->current_company = $request->current_company;
            $record->employement_type = $request->employement_type;
            $record->experience_year = $request->experience_year;
            $record->experience_month = $request->experience_month;
            $record->job_title_id = $job_title->id;
            $record->start_year = $request->start_year;
            $record->start_month = $request->start_month;
            $record->end_year = $request->end_year;
            $record->end_month = $request->end_month;
            $record->current_salary_currency = $request->current_salary_currency;
            $record->current_salary_amount = $request->current_salary_amount;
            $record->job_profile = $request->job_profile;
            $record->notice_period = $request->notice_period;
            $record->last_working_date = $request->last_working_date;
            $record->has_any_offer = $request->has_any_offer;
            $record->offered_salary_currency = $request->offered_salary_currency;
            $record->offered_salary_amount = $request->offered_salary_amount;
            $record->offered_company = $request->offered_company;
            $record->offered_job_title_id = $request->offered_job_title_id;
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
     * @param  \App\CandidateEmployment  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateEmployment  $id
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
     * @param  \App\CandidateEmployment  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'is_current_employement' => 'required',
                'department_uuid' => 'required',
                'employement_type' => 'required',
                'job_title_uuid' => 'required',
                'start_year' => 'required',
                'start_month' => 'required',
                'job_profile' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateEmployment::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $department  = CoreJobDepartments::where('uuid',$request->department_uuid)->first();
            if(!$department){
                return $this->simpleReturn('error','Department not found by UUID', 404);
            }

            $job_title  = CoreJobTitle::where('uuid',$request->job_title_uuid)->first();
            if(!$department){
                return $this->simpleReturn('error','Job_title not found by UUID', 404);
            }


           

            if ($record) {
                $record->department_id = $department->id;
                $record->is_current_employement = $request->is_current_employement;
                $record->current_company = $request->current_company;
                $record->employement_type = $request->employement_type;
                $record->experience_year = $request->experience_year;
                $record->experience_month = $request->experience_month;
                $record->job_title_id = $job_title->id;
                $record->start_year = $request->start_year;
                $record->start_month = $request->start_month;
                $record->end_year = $request->end_year;
                $record->end_month = $request->end_month;
                $record->current_salary_currency = $request->current_salary_currency;
                $record->current_salary_amount = $request->current_salary_amount;
                $record->job_profile = $request->job_profile;
                $record->notice_period = $request->notice_period;
                $record->last_working_date = $request->last_working_date;
                $record->has_any_offer = $request->has_any_offer;
                $record->offered_salary_currency = $request->offered_salary_currency;
                $record->offered_salary_amount = $request->offered_salary_amount;
                $record->offered_company = $request->offered_company;
                $record->offered_job_title_id = $request->offered_job_title_id;
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
     * @param  \App\CandidateEmployment  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = CandidateEmployment::where('uuid', $uuid)->first();
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