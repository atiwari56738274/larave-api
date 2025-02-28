<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CompanyReviews;
use App\Models\CoreJobTitle;
use App\Models\CompanyProfile;
use App\Models\CoreJobCity;
use App\Models\Jobs;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanyInfoController extends Controller
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
    public function getCompanyList(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CompanyProfile::with(['city', 'state', 'country', 'reviews'])
                ->orderBy('company_name', 'desc');

                if ($request->company_name && ($request->company_name != '')) {
                    $records->where('company_name', 'like', '%'.$request->company_name.'%');
                }
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveReviews(Request $request)
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
                return $this->simpleReturn('success', 'Review Saved!');
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
     * @param  \App\CompanyReviews  $uuid
     * @return \Illuminate\Http\Response
     */
    public function getCompanyProfile($uuid)
    {
        try {
            $record = CompanyProfile::where('uuid', $uuid)->with(['city', 'state', 'country', 'reviews'])->first();
            if ($record) {
                return $this->simpleReturn('success', $record);
            }
            return $this->simpleReturn('error', 'Company not exist!', 404);
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
    public function fetchCompanyList(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CompanyProfile::select('uuid', 'company_name', 'company_logo', 'company_type', 'industry_type', 'state_id', 'country_id')
                ->with(['state:id,name', 'country:id,name'])
                ->withCount(['jobs'])
                ->orderBy('company_name', 'desc');

                if ($request->company_name && ($request->company_name != '')) {
                    $records->where('company_name', 'like', '%'.$request->company_name.'%');
                }
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchJobListByCompanyUuid(Request $request, $company_uuid)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $isExit = CompanyProfile::where('uuid', $company_uuid)->first();
            if (!$isExit) {
                return $this->simpleReturn('error', 'Company not found!', 404);
            }

            $records = Jobs::select('uuid', 'id','no_of_hire', 'created_at', 'work_location_type', 'salary_min_amount', 'salary_max_amount', 'salary_exact_amount', 'salary_per_type', 'job_title_id')
                ->with(['job_title'])
                ->with(['job_locations'])
                ->withCount(['candidate_job_saved'])
                ->where('user_id', $isExit->user_id)
                ->where('is_published', '1')
                ->where('status', 'active');

                if ($request->job_title_id && ($request->job_title_id != '')) {
                    $records->where('job_title_id', $request->job_title_id);
                }
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

}