<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CompanyReviews;
use App\Models\CoreJobTitle;
use App\Models\CoreJobCity;
use App\Models\Jobs;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanyReviewsController extends Controller
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
                'company_uuid' => 'required',
                'job_title_uuid' => 'required',
                'job_location_uuid' => 'required',
                'review_title' => 'required',
                'review_rating' => 'required',
                'review_description' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $user_id = Auth::user()->id;

            // check if job_title exists
            $jobTitleRecord = CoreJobTitle::where('uuid', $request->job_title_uuid)->first();
            if (!$jobTitleRecord) {
                return $this->simpleReturn('error', 'Title not exist!', 404);
            }

            // check if job_location exists
            $jobLocationRecord = CoreJobCity::where('uuid', $request->job_location_uuid)->first();
            if (!$jobLocationRecord) {
                return $this->simpleReturn('error', 'Location not exist!', 404);
            }

            // check if company exists
            $companyRecord = CompanyProfile::where('uuid', $request->company_uuid)->first();
            if (!$companyRecord) {
                return $this->simpleReturn('error', 'Company not exist!', 404);
            }

            // check if job exists
            $reviewRecord = CompanyReviews::where('user_id', $user_id)->where('company_profile_id', $companyRecord->id)->first();
            if ($reviewRecord) {
                return $this->simpleReturn('error', 'Review already exist!', 400);
            }

            $add = new CompanyReviews();
            $add->uuid = (string) Str::uuid();
            $add->user_id = $user_id;
            $add->company_profile_id = $companyRecord->id;
            $add->job_title_id = $jobTitleRecord->id;
            $add->job_location_id = $jobLocationRecord->id;
            $add->review_title = $request->review_title;
            $add->review_rating = $request->review_rating;
            $add->review_description = $request->review_description;
            $add->status = "approved";
            if($add->save()) {
                return $this->simpleReturn('success', 'Job Saved!');
            }
            return $this->simpleReturn('error', 'Error in insertion');
            
            return $this->simpleReturn('error', 'Error in insertion');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyReviews  $id
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
     * @param  \App\CompanyReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }
}