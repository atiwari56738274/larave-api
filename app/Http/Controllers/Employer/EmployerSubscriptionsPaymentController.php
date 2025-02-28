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
use Carbon\Carbon;
use Razorpay\Api\Api as RApi;

class EmployerSubscriptionsPaymentController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
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
                'subscription_uuid' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $subscriptionExist = Subscriptions::where('uuid', $request->subscription_uuid)->first();
            if (!$subscriptionExist) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $user_id = Auth::user()->id;
            $txnid = md5(uniqid(rand(), true));

            $record = new EmployerSubscriptions();
            $record->uuid = (string) Str::uuid();
            $record->user_id = $user_id;
            $record->subscription_id = $subscriptionExist->id;
            $record->txnid = $txnid;
            $record->total_amount = $subscriptionExist->amount;
            $record->payment_status = 'pending';
            $record->status = 'pending';
            if ($record->save()) {
                //create order payment information by razorpay_payment_id
                $payApi = new RApi(env('RPAY_KEY', ''), env('RPAY_SECRET', ''));
                $payment = $payApi->order->create([
                    'receipt'         => 'od_rcp_' . rand(),
                    'amount'          => $subscriptionExist->amount * 100, // Amount in paisa
                    'currency'        => 'INR',
                    'payment_capture' => 1, // Auto capture payment
                    "notes"           => [
                            "type"=> "emp_subscription",
                            "checkout_id"=> $record->id
                        ]
                    ]);
                if($payment->status == 'created') {
                    $order_checkout = EmployerSubscriptions::where('id', $record->id)->first();
                    $order_checkout->order_id = $payment->id;
                    $order_checkout->save();

                    $order_checkout = EmployerSubscriptions::select('uuid', 'txnid', 'order_id', 'payment_id', 'status', 'total_amount','created_at')->where('id', $record->id)->first();

                    $order_checkout['msg'] = 'Payment initiated Successfully.';
                    return $this->simpleReturn('success', $order_checkout);
                }

                return $this->simpleReturn('error', 'Error in razorpay order creation', 400);
            } else {
                return $this->simpleReturn('error', 'Record not found!', 404);
            }
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }   
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployerSubscriptions  $uuid
     * @return \Illuminate\Http\Response
     */
    public function checkRazorpayPaymentStatus($uuid)
    {
        try {

            $records = EmployerSubscriptions::select('uuid', 'txnid', 'order_id', 'payment_id', 'status', 'total_amount','created_at')->where('uuid', $uuid)->first();;
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
     * @param $txnid
     */
    public function razorpayWebhookPaymentUpdate(Request $request)
    {
        try {
            if (empty($request['event'])) {
                Log::info("Razorpay Payment -- Event not triggered");
                return $this->simpleReturn('error', "No event triggered");
            }

            if (($request['event'] != 'payment.captured') && ($request['event'] != 'payment.failed')) {
                Log::info("Razorpay Payment -- Invalid event");
                return $this->simpleReturn('error', "Invalid event");
            }
    
            if (empty($request['payload']['payment']['entity']['notes'])) {
                Log::info("Razorpay Payment -- Notes not available");
                return $this->simpleReturn('error', "Notes not available");
            }

            if (empty($request['payload']['payment']['entity']['notes']['type'])) {
                Log::info("Razorpay Payment -- Razorpay credentials not found");
                return $this->simpleReturn('error', "Razorpay credentials not found");
            } else if ($request['payload']['payment']['entity']['notes']['type'] != 'emp_subscription') {
                Log::info("Razorpay Payment -- This is not emp subscription payment");
                return $this->simpleReturn('error', "This is not subscription payment");
            }                               

            
            Log::info("Razorpay Payment started--- ");                           

            $notes = $request['payload']['payment']['entity']['notes'];
            $webhookSecret    = env('WEBHOOK_SECRET','1234');
            $webhookSignature = $request->header('X-Razorpay-Signature');

            $api = new RApi(env('RPAY_KEY', ''), env('RPAY_SECRET', ''));
            $api->utility->verifyWebhookSignature($request->getContent(), $webhookSignature, $webhookSecret);

            if (!empty($request['event'])) {
                $payment_event = $request['event'];
                $payment_data = $request['payload']['payment']['entity'];
                $payment_id = $payment_data['id'];
                Log::info("Razorpay Payment event--- ". $payment_event);
                Log::info("Razorpay Payment pay_id--- ". $payment_id);

                $record = EmployerSubscriptions::where('id', $notes['checkout_id'])->first();
                if(!$record)
                {
                    Log::info("Razorpay Payment -- Checkout record not found");
                    return $this->simpleReturn('error', "Checkout record not found");
                }

                $record->payment_id = $payment_id;
                $record->payment_mode = $payment_data['method'];
                $record->payment_status = ($payment_event == 'payment.captured') ? 'success' : 'failed';
                $record->save();

                //++++++++++++++++++++++++++++++++++++++++++++++++
                if ($payment_event == 'payment.captured') 
                {
                    $this->updateEmployerSubscriptionInfo($record->uuid);
                    Log::info("Razorpay payment process done.");
                } else {
                    Log::info("Razorpay payment failed. event: ". $payment_event);
                }
                //++++++++++++++++++++++++++++++++++++++++++++++++
            }
            //++++++++++++++++++++++++++++++++++++++++++++++++
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::info("Razorpay error --- ".$errorMessage);
            return $this->simpleReturn('error',  $errorMessage);
        }
        return $this->simpleReturn('success',  "Razorpay payment opration ends.. ");

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\EmployerSubscriptions  $uuid
     * @return \Illuminate\Http\Response
     */
    private function updateEmployerSubscriptionInfo($uuid)
    {
        $recordEmpSubs = EmployerSubscriptions::where('uuid', $uuid)->first();

        $updateExistingRecord = EmployerSubscriptions::where('uuid', "!=" ,$uuid)->Where('user_id', $recordEmpSubs->user_id)->Where('status', 'active')->update(['status'=> 'expired']);

        $recordSubs = Subscriptions::where('id', $recordEmpSubs->subscription_id)->first();

        $currentDate = Carbon::now();
        if($recordSubs->validity_type === 'day') {                      
            $newDate = $currentDate->addDay($recordSubs->validity);
        } else if($recordSubs->validity_type === 'month') {
            $newDate = $currentDate->addMonth($recordSubs->validity);
        } else if($recordSubs->validity_type === 'year') {
            $newDate = $currentDate->addYear($recordSubs->validity);
        }


        $recordEmpSubs->payment_type = 'online';
        $recordEmpSubs->validity = $newDate;
        $recordEmpSubs->subscription_status = 'subscribe';
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
        if ($recordEmpSubs->save()) {
            return true;
        } else {
            return false;
        }
    }

}