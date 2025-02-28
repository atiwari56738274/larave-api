<?php

namespace App\Http\Controllers\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\EmployerSubscriptions;
use App\Models\EmployerSubscriptionsDetails;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class EmployerSubscriptionsController extends Controller
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

            $records = Subscriptions::select('uuid', 'type', 'id', 'subscription_type', 'title', 'description', 'mrp_amount', 'amount', 'logo', 'validity', 'validity_type', 'profile_creation', 'notification_via_email_sms_whatsapp', 'no_of_job_post', 'no_of_urgent_hiring_tag', 'no_of_candidate_unlock', 'no_of_job_published', 'job_boosts', 'smart_boost_via_email_sms_whatsapp', 'dedicated_relationship_manager', 'campion_via_email_sms_whatsapp', 'multiple_user_login', 'reports', 'company_branding')->where('type', 'employer');
        
            $records = $records->where('status', 'active')->withCount(['is_employer_subscribe'])->orderBy('sort_order', 'ASC')->get();

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employerSubscriptionHistory(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = EmployerSubscriptions::where('user_id', $user_id);
        
            $records = $records->with(['subscriptions'])->orderBy('id', 'DESC')->paginate($perPageCount);

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
        //    
        
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
    public function update(Request $request,$id)
    {
        //    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscriptions  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }

    public static function checkIfSubscriptionValid($user_id, $type = null, $type_id = null) {
        try {
            $today_date = new \DateTime();
            $today = $today_date->format('Y-m-d');

            $eSRecord = EmployerSubscriptions::where('user_id', $user_id)->where('status', 'active')->where('validity', '>=' , $today)->orderBy('id', 'desc')->first();
            if($eSRecord) {
                if(($type === 'job_post') && ($eSRecord->remaining_job_post > 0)) {
                    return true;
                }
                if(($type === 'urgent_hiring_tag') && ($eSRecord->remaining_urgent_hiring_tag > 0)) {
                    return true;
                }
                if(($type === 'candidate_unlock') && ($eSRecord->remaining_candidate_unlock > 0)) {
                    return true;
                }
                if(($type === 'job_published') && ($eSRecord->remaining_job_published > 0)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        } 
    }

    public static function updateEmployerSubscriptionData($user_id, $type = null, $type_id = null) {
        try {
            $today_date = new \DateTime();
            $today = $today_date->format('Y-m-d');

            $data = EmployerSubscriptions::where('user_id', $user_id)->where('status', 'active')->where('validity', '>=' , $today)->orderBy('id', 'desc')->first();
            if($data) {
                if(($type === 'job_post') && ($data->remaining_job_post > 0)) {
                    $data->remaining_job_post = $data->remaining_job_post - 1;
                }
                if(($type === 'urgent_hiring_tag') && ($data->remaining_urgent_hiring_tag > 0)) {
                    $data->remaining_urgent_hiring_tag = $data->remaining_urgent_hiring_tag - 1;
                }
                if(($type === 'candidate_unlock') && ($data->remaining_candidate_unlock > 0)) {
                    $data->remaining_candidate_unlock = $data->remaining_candidate_unlock - 1;
                }
                if(($type === 'job_published') && ($data->remaining_job_published > 0)) {
                    $data->remaining_job_published = $data->remaining_job_published - 1;
                }
                if($data->save()) {
                    self::addEmployerSubscriptionDetails($user_id, $type, $type_id, $data->id);
                    return true;
                }
                
                return false;
            }

            return false;
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        } 
    }


    public static function addEmployerSubscriptionDetails($user_id, $type = null, $type_id = null, $subscription_id) {
        $eSRecordDetails = EmployerSubscriptionsDetails::where('user_id', $user_id)->where('employer_subscriptions_id', $subscription_id)->where('type', $type)->where('type_id', $type_id)->first();
        if(!$eSRecordDetails) {
            $record = new EmployerSubscriptionsDetails();
            $record->uuid = (string) Str::uuid();
            $record->employer_subscriptions_id = $subscription_id;
            $record->user_id  = $user_id;
            $record->type = $type;
            $record->type_id = $type_id;
            if($record->save()) {
                return true;
            }
        }
        
        return false;
    }

}