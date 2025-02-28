<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employer;
use App\Models\EmployerSubscriptions;
use App\Models\Subscriptions;
use App\Models\ProductReviews;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployerDataController extends Controller
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
            $records = Employer::where('user_type', 'employer')->paginate($perPageCount);
            if(count($records) > 0){
                return $this->simpleReturn('success', $records);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
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
                'name' => 'required',
                'designation' => 'required',
                'company_name' => 'required',
                'review_rating' => 'required',
                'review_description' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $fileName = null;

            if (request()->hasFile('image')) {
                $file = request()->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('/uploads/product_reviews/', $fileNameResume);
            }

            $record = new ProductReviews();
            $record->uuid = (string) Str::uuid();
            $record->name = $request->name;
            $record->designation = $request->designation;
            $record->company_name = $request->company_name;
            $record->review_rating = $request->review_rating;
            $record->review_description = $request->review_description;
            $record->status = $request->status;
            $record->image = $fileName;
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductReviews  $id
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'name' => 'required',
                'designation' => 'required',
                'company_name' => 'required',
                'review_rating' => 'required',
                'review_description' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = ProductReviews::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                if (request()->hasFile('resume')) {
                    $file = request()->file('resume');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('/uploads/product_reviews/', $fileNameResume);
                    $record->image = $fileName;
                }
                $record->name = $request->name;
                $record->designation = $request->designation;
                $record->company_name = $request->company_name;
                $record->review_rating = $request->review_rating;
                $record->review_description = $request->review_description;
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = ProductReviews::where('uuid', $uuid)->first();
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

    /**
     * Update the specified resource from storage.
     *
     * @param  \App\Employer  $uuid
     * @return \Illuminate\Http\Response
     */
    public function updateEmployerVerifiedStatus(Request $request, $uuid)
    {
        // try {
            $rules = array(
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            if($request->status === false) {
                return $this->simpleReturn('error', 'user can not unverified once verified!', 422);
            }

            $record = Employer::where('user_type', 'employer')->where('uuid', $uuid)->first();
            if($record){
                if(($record->employer_verified == '1') && ($request->status == 'true')) {
                    return $this->simpleReturn('error', 'User already verified', 422);
                }
                $record->employer_verified = "1";
                if($record->save()){

                    $recordSubs = Subscriptions::where('type', 'employer')->where('is_default', '1')->first();
                    if($recordSubs) {
                        $currentDate = Carbon::now();
                        if($recordSubs->validity_type === 'day') {                      
                            $newDate = $currentDate->addDay($recordSubs->validity);
                        } else if($recordSubs->validity_type === 'month') {
                            $newDate = $currentDate->addMonth($recordSubs->validity);
                        } else if($recordSubs->validity_type === 'year') {
                            $newDate = $currentDate->addYear($recordSubs->validity);
                        }

                        $recordEmpSubs = new EmployerSubscriptions();
                        $recordEmpSubs->uuid = (string) Str::uuid();
                        $recordEmpSubs->user_id = $record->id;
                        $recordEmpSubs->subscription_id = $recordSubs->id;
                        $recordEmpSubs->payment_type = 'default';
                        $recordEmpSubs->txnid = null;
                        $recordEmpSubs->order_id = null;
                        $recordEmpSubs->payment_id = null;
                        $recordEmpSubs->payment_mode = null;
                        $recordEmpSubs->discount_type = null;
                        $recordEmpSubs->discount_code = null;
                        $recordEmpSubs->payment_status = null;
                        $recordEmpSubs->notes = null;
                        $recordEmpSubs->validity = $newDate;
                        $recordEmpSubs->amount = null;
                        $recordEmpSubs->gst_amount = null;
                        $recordEmpSubs->discount_amount = null;
                        $recordEmpSubs->total_amount = null;
                        $recordEmpSubs->subscription_status = 'default';
                        $recordEmpSubs->status = 'active';
                        $recordEmpSubs->profile_creation = $recordSubs->profile_creation;
                        $recordEmpSubs->notification_via_email_sms_whatsapp = $recordSubs->notification_via_email_sms_whatsapp;
                        $recordEmpSubs->job_boosts = $recordSubs->job_boosts;
                        $recordEmpSubs->smart_boost_via_email_sms_whatsapp = $recordSubs->smart_boost_via_email_sms_whatsapp;
                        $recordEmpSubs->dedicated_relationship_manager = $recordSubs->dedicated_relationship_manager;
                        $recordEmpSubs->campion_via_email_sms_whatsapp = $recordSubs->campion_via_email_sms_whatsapp;
                        $recordEmpSubs->multiple_user_login = $recordSubs->multiple_user_login;
                        $recordEmpSubs->reports = $recordSubs->reports;
                        $recordEmpSubs->company_branding = $recordSubs->company_branding;
                        $recordEmpSubs->no_of_job_post = $recordSubs->no_of_job_post;
                        $recordEmpSubs->remaining_job_post = $recordSubs->no_of_job_post;
                        $recordEmpSubs->no_of_urgent_hiring_tag = $recordSubs->no_of_urgent_hiring_tag;
                        $recordEmpSubs->remaining_urgent_hiring_tag = $recordSubs->no_of_urgent_hiring_tag;
                        $recordEmpSubs->no_of_candidate_unlock = $recordSubs->no_of_candidate_unlock;
                        $recordEmpSubs->remaining_candidate_unlock = $recordSubs->no_of_candidate_unlock;
                        $recordEmpSubs->no_of_job_published = $recordSubs->no_of_job_published;
                        $recordEmpSubs->remaining_job_published = $recordSubs->no_of_job_published;
                        $recordEmpSubs->save();
                    }

                    return $this->simpleReturn('success', 'Successfully verified');
                }
                return $this->simpleReturn('error', 'updation error', 422);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        // } catch (\Exception $exception){
        //     Log::error($exception);
        //     return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        // }  
    }

    /**
     * Update the specified resource from storage.
     *
     * @param  \App\Employer  $uuid
     * @return \Illuminate\Http\Response
     */
    public function updateProfileVerifiedStatus(Request $request, $uuid)
    {
        try {
            $rules = array(
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = Employer::where('user_type', 'employer')->where('uuid', $uuid)->first();
            if($record){
                if(($record->profile_verified == "1" ) && ($request->status)) {
                    return $this->simpleReturn('error', 'User already verified', 422);
                }
                if(($record->profile_verified == "0" ) && (!$request->status == "false")) {
                    return $this->simpleReturn('error', 'User already unverified', 422);
                }
                $record->profile_verified = $request->status;
                if($record->save()){
                    return $this->simpleReturn('success', 'Successfully profile status updated');
                }
                return $this->simpleReturn('error', 'updation error', 422);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }  
    }
}