<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Employer;
use App\Models\EmployerSubscriptions;
use App\Models\SubscriptionFeature;
use App\Models\SubscriptionsTopupEmployer;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionsTopupEmployerController extends Controller
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

            $records = SubscriptionsTopupEmployer::with(['employer']);
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
                'type' => 'required',
                'value' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $today_date = new \DateTime();
            $today = $today_date->format('Y-m-d');

            $empRecord = Employer::where('uuid', $request->user_uuid)->where('user_type', 'employer')->first();
            if (!$empRecord) {
                return $this->simpleReturn('error', 'Employer not valid!', 404);
            }

            $empSubsRecord = EmployerSubscriptions::where('user_id', $empRecord->id)->where('status', 'active')->where('validity', '>=' , $today)->orderBy('id', 'desc')->first();
            if (!$empSubsRecord) {
                return $this->simpleReturn('error', 'Employer Subscriptions not valid!', 404);
            }

            $record = new SubscriptionsTopupEmployer();
            $record->uuid = (string) Str::uuid();
            $record->user_id = $empRecord->id;
            $record->employer_subscription_id = $empSubsRecord->id;
            $record->type = $request->type;
            $record->value = $request->value;
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
    public function edit($uuid)
    {
        try {

            $today_date = new \DateTime();
            $today = $today_date->format('Y-m-d');

            $record = SubscriptionsTopupEmployer::where('uuid', $uuid)->where('is_approved', '0')->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $empSubsRecord = EmployerSubscriptions::where('user_id', $record->user_id)->where('status', 'active')->where('validity', '>=' , $today)->orderBy('id', 'desc')->first();
            if (!$empSubsRecord) {
                return $this->simpleReturn('error', 'Employer Subscriptions not valid!', 404);
            }

            if($record->employer_subscription_id !== $empSubsRecord->id) {
                return $this->simpleReturn('error', 'Employer already updated thier Subscriptions', 404);
            }

            if ($record) {
                $record->is_approved = 1;
                $record->save();
                if($record){
                    if($record->type === 'job_post') {
                        $empSubsRecord->no_of_job_post = $empSubsRecord->no_of_job_post + $record->value;
                        $empSubsRecord->remaining_job_post = $empSubsRecord->remaining_job_post + $record->value;
                    } else if($record->type === 'urgent_hiring_tag') {
                        $empSubsRecord->no_of_urgent_hiring_tag = $empSubsRecord->no_of_urgent_hiring_tag + $record->value;
                        $empSubsRecord->remaining_urgent_hiring_tag = $empSubsRecord->remaining_urgent_hiring_tag + $record->value;
                    } else if($record->type === 'candidate_unlock') {
                        $empSubsRecord->no_of_candidate_unlock = $empSubsRecord->no_of_candidate_unlock + $record->value;
                        $empSubsRecord->remaining_candidate_unlock = $empSubsRecord->remaining_candidate_unlock + $record->value;
                    } else if($record->type === 'job_published') {
                        $empSubsRecord->no_of_job_published = $empSubsRecord->no_of_job_published + $record->value;
                        $empSubsRecord->remaining_job_published = $empSubsRecord->remaining_job_published + $record->value;
                    } else if($record->type === 'notification_via_email_sms_whatsapp') {
                        $empSubsRecord->notification_via_email_sms_whatsapp = $record->notification_via_email_sms_whatsapp;
                    } else if($record->type === 'job_boosts') {
                        $empSubsRecord->job_boosts = $record->job_boosts;
                    } else if($record->type === 'smart_boost_via_email_sms_whatsapp') {
                        $empSubsRecord->smart_boost_via_email_sms_whatsapp = $record->smart_boost_via_email_sms_whatsapp;
                    } else if($record->type === 'dedicated_relationship_manager') {
                        $empSubsRecord->dedicated_relationship_manager = $record->dedicated_relationship_manager;
                    } else if($record->type === 'campion_via_email_sms_whatsapp') {
                        $empSubsRecord->campion_via_email_sms_whatsapp = $record->campion_via_email_sms_whatsapp;
                    } else if($record->type === 'multiple_user_login') {
                        $empSubsRecord->multiple_user_login = $record->multiple_user_login;
                    } else if($record->type === 'reports') {
                        $empSubsRecord->reports = $record->reports;
                    } else if($record->type === 'company_branding') {
                        $empSubsRecord->company_branding = $record->company_branding;
                    }
                    $empSubsRecord->save();

                    return $this->simpleReturn('success', 'Record approved successfully');
                }
            }
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        } 
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
                'type' => 'required',
                'value' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = SubscriptionsTopupEmployer::where('uuid', $uuid)->where('is_approved', '0')->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->type = $request->type;
                $record->value = $request->value;
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
            $record = SubscriptionsTopupEmployer::where('uuid', $uuid)->where('is_approved', '0')->first();
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