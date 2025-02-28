<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscriptions;
use App\Models\SubscriptionFeature;
use App\Models\EmployerSubscriptions;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionsController extends Controller
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
            $records = Subscriptions::with('subscription_feature')->orderBy('sort_order', 'ASC')->get();
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
                'type' => 'required|in:candidate,employer',
                'subscription_type' => 'required',
                'title' => 'required',
                'description' => 'required',
                'mrp_amount' => 'required',
                'amount' => 'required',
                'is_default' => 'required',
                'sort_order' => 'required',
                'validity' => 'required',
                'subscription_feature' => 'required',
                'validity_type' => 'required|in:day,month,year',
                'status' => 'required|in:active,inactive',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $fileName = null;

            if (request()->hasFile('logo')) {
                $file = request()->file('logo');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('/uploads/subscriptions/', $fileNameResume);
            }

            if($request->is_default === true) {
                $updateDefault = Subscriptions::where('type', $request->type)->update(['is_default'=> false]);
            }

            $record = new Subscriptions();
            $record->uuid = (string) Str::uuid();
            $record->type = $request->type;
            $record->subscription_type = $request->subscription_type;
            $record->title = $request->title;
            $record->is_default = $request->is_default;
            $record->description = $request->description;
            $record->mrp_amount = $request->mrp_amount;
            $record->amount = $request->amount;
            $record->logo = $fileName;
            $record->sort_order = $request->sort_order;
            $record->validity = $request->validity;
            $record->validity_type = $request->validity_type;
            $record->custom_permission = $request->custom_permission;
            $record->status = $request->status;

            $record->profile_creation = $request->profile_creation;
            $record->no_of_job_apply = $request->no_of_job_apply;
            $record->resume_builder = $request->resume_builder;
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
                $this->saveUpdateFeature($record->id, $request->subscription_feature);
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
                'type' => 'required|in:candidate,employer',
                'subscription_type' => 'required',
                'title' => 'required',
                'description' => 'required',
                'mrp_amount' => 'required',
                'is_default' => 'required',
                'amount' => 'required',
                'sort_order' => 'required',
                'validity' => 'required',
                'subscription_feature' => 'required',
                'validity_type' => 'required|in:day,month,year',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = Subscriptions::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if($request->is_default === true) {
                $updateDefault = Subscriptions::where('type', $request->type)->where('uuid', '!=', $uuid)->update(['is_default'=> false]);
            }

            if ($record) {
                if (request()->hasFile('logo')) {
                    $file = request()->file('logo');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('/uploads/subscriptions/', $fileName);
                    $record->logo = $fileName;
                }
                $record->type = $request->type;
                $record->subscription_type = $request->subscription_type;
                $record->title = $request->title;
                $record->is_default = $request->is_default;
                $record->description = $request->description;
                $record->mrp_amount = $request->mrp_amount;
                $record->amount = $request->amount;
                $record->sort_order = $request->sort_order;
                $record->validity = $request->validity;
                $record->validity_type = $request->validity_type;
                $record->custom_permission = $request->custom_permission;
                $record->status = $request->status;

                $record->profile_creation = $request->profile_creation;
                $record->no_of_job_apply = $request->no_of_job_apply;
                $record->resume_builder = $request->resume_builder;
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
                    $this->saveUpdateFeature($record->id, $request->subscription_feature);
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
            $record = Subscriptions::where('uuid', $uuid)->first();
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Subscriptions $id
     * @param  array $subscription_feature
     */
    private function saveUpdateFeature($id, $subscription_feature)
    {
        $feature_delete = SubscriptionFeature::where('subscription_id', $id)->delete();

        if(count($subscription_feature) === 0) {
            return;
        }

        foreach ($subscription_feature as $key=>$lvalue) {
            $feature_records = SubscriptionFeature::create([
                'uuid' => (string) Str::uuid(),
                'subscription_id' => $id,
                'feature' => $lvalue['feature'],
                'sort_order' => $lvalue['sort_order']
            ]);
        }  

        return ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSubscriptionDetails(Request $request, $uuid)
    {
        try {
            $rules = array(
                'profile_creation' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = Subscriptions::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->profile_creation = $request->profile_creation;
                $record->no_of_job_apply = $request->no_of_job_apply;
                $record->resume_builder = $request->resume_builder;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployerSubscriptionHistory(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = EmployerSubscriptions::with('employer')->orderBy('id', 'DESC')->paginate($perPageCount);
            if($records){
                return $this->simpleReturn('success', $records);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }   
    }
}