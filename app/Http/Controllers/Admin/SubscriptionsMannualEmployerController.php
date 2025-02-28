<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Employer;
use App\Models\SubscriptionFeature;
use App\Models\SubscriptionsMannualEmployer;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionsMannualEmployerController extends Controller
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

            $records = SubscriptionsMannualEmployer::with(['subscription', 'employer']);
            $records = $records->paginate($perPageCount);
            if($records){
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
                'user_uuid' => 'required',
                'subscription_uuid' => 'required',
                'amount' => 'required',
                'validity' => 'required',
                'validity_type' => 'required|in:day,month,year',
                'status' => 'required|in:active,inactive',
                'profile_creation' => 'required',
                'notification_via_email_sms_whatsapp' => 'required',
                'no_of_job_post' => 'required',
                'no_of_urgent_hiring_tag' => 'required',
                'no_of_candidate_unlock' => 'required',
                'no_of_job_published' => 'required',
                'job_boosts' => 'required',
                'smart_boost_via_email_sms_whatsapp' => 'required',
                'dedicated_relationship_manager' => 'required',
                'campion_via_email_sms_whatsapp' => 'required',
                'multiple_user_login' => 'required',
                'reports' => 'required',
                'company_branding' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $subsRecord = Subscriptions::where('uuid', $request->subscription_uuid)->where('subscription_type', 'mannual')->first();
            if (!$subsRecord) {
                return $this->simpleReturn('error', 'Subscriptions not valid!', 404);
            }

            $empRecord = Employer::where('uuid', $request->user_uuid)->where('user_type', 'employer')->first();
            if (!$empRecord) {
                return $this->simpleReturn('error', 'Employer not valid!', 404);
            }

            $record = new SubscriptionsMannualEmployer();
            $record->uuid = (string) Str::uuid();
            $record->user_id = $empRecord->id;
            $record->subscription_id = $subsRecord->id;
            $record->amount = $request->amount;
            $record->validity = $request->validity;
            $record->validity_type = $request->validity_type;
            $record->status = $request->status;
            $record->profile_creation = $request->profile_creation;
            $record->notification_via_email_sms_whatsapp = $request->notification_via_email_sms_whatsapp;
            $record->no_of_job_post = $request->no_of_job_post;
            $record->no_of_urgent_hiring_tag = $request->no_of_urgent_hiring_tag;
            $record->no_of_candidate_unlock = $request->no_of_candidate_unlock;
            $record->no_of_job_published = $request->no_of_job_published;
            $record->job_boosts = $request->job_boosts;
            $record->smart_boost_via_email_sms_whatsapp = $request->smart_boost_via_email_sms_whatsapp;
            $record->dedicated_relationship_manager = $request->dedicated_relationship_manager;
            $record->campion_via_email_sms_whatsapp = $request->campion_via_email_sms_whatsapp;
            $record->multiple_user_login = $request->multiple_user_login;
            $record->reports = $request->reports;
            $record->company_branding = $request->company_branding;
            if($record->save()) {
                return $this->simpleReturn('success', "Added successfully");
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
     * @param  \App\Subscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subscriptions  $id
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
     * @param  \App\Subscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'subscription_uuid' => 'required',
                'amount' => 'required',
                'validity' => 'required',
                'validity_type' => 'required|in:day,month,year',
                'status' => 'required|in:active,inactive',
                'profile_creation' => 'required',
                'notification_via_email_sms_whatsapp' => 'required',
                'no_of_job_post' => 'required',
                'no_of_urgent_hiring_tag' => 'required',
                'no_of_candidate_unlock' => 'required',
                'no_of_job_published' => 'required',
                'job_boosts' => 'required',
                'smart_boost_via_email_sms_whatsapp' => 'required',
                'dedicated_relationship_manager' => 'required',
                'campion_via_email_sms_whatsapp' => 'required',
                'multiple_user_login' => 'required',
                'reports' => 'required',
                'company_branding' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $subsRecord = Subscriptions::where('uuid', $request->subscription_uuid)->where('subscription_type', 'mannual')->first();
            if (!$subsRecord) {
                return $this->simpleReturn('error', 'Subscriptions not valid!', 404);
            }


            $record = SubscriptionsMannualEmployer::where('uuid', $uuid)->whereNull('employer_subscription_id')->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->subscription_id = $subsRecord->id;
                $record->amount = $request->amount;
                $record->validity = $request->validity;
                $record->validity_type = $request->validity_type;
                $record->status = $request->status;
                $record->profile_creation = $request->profile_creation;
                $record->notification_via_email_sms_whatsapp = $request->notification_via_email_sms_whatsapp;
                $record->no_of_job_post = $request->no_of_job_post;
                $record->no_of_urgent_hiring_tag = $request->no_of_urgent_hiring_tag;
                $record->no_of_candidate_unlock = $request->no_of_candidate_unlock;
                $record->no_of_job_published = $request->no_of_job_published;
                $record->job_boosts = $request->job_boosts;
                $record->smart_boost_via_email_sms_whatsapp = $request->smart_boost_via_email_sms_whatsapp;
                $record->dedicated_relationship_manager = $request->dedicated_relationship_manager;
                $record->campion_via_email_sms_whatsapp = $request->campion_via_email_sms_whatsapp;
                $record->multiple_user_login = $request->multiple_user_login;
                $record->reports = $request->reports;
                $record->company_branding = $request->company_branding;
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
     * @param  \App\Subscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = SubscriptionsMannualEmployer::where('uuid', $uuid)->whereNull('employer_subscription_id')->first();
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