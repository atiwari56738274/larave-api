<?php

namespace App\Http\Controllers\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Jobs;
use App\Models\JobLocations;
use App\Models\JobSkills;
use App\Models\JobEducations;
use App\Models\CompanyReviews;
use App\Models\CompanyProfile;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user_id = Auth::user()->id;

            // check if company exists
            $companyRecord = CompanyProfile::where('user_id', $user_id)->first();
            if (!$companyRecord) {
                return $this->simpleReturn('error', 'No company found!', 400);
            }

            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CompanyReviews::where('company_profile_id', $companyRecord->id)
                ->with(['job_title', 'job_location', 'candidate'])
                ->orderBy('created_at', 'desc');

                if ($request->department_id && ($request->department_id != '')) {
                    $records->where('department_id', $request->department_id);
                }

                if ($request->hiring_company_name && ($request->hiring_company_name != '')) {
                    $records->where('hiring_company_name', 'like', '%'.$request->hiring_company_name.'%');
                }

                if ($request->is_published && ($request->is_published != '')) {
                    $records->where('is_published', $request->is_published);
                }

                if ($request->status && ($request->status != '')) {
                    $records->where('status', $request->status);
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
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyProfile  $id
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
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'review_reply' => 'required',
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CompanyReviews::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            // check if company exists
            $companyRecord = CompanyProfile::where('id', $record->company_profile_id)->where('user_id', Auth::user()->id)->first();
            if (!$companyRecord) {
                return $this->simpleReturn('error', 'Unauthorized Company access!', 400);
            }

            if ($record) {
                $record->status = $request->status;
                $record->review_reply = $request->review_reply;
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
     * @param  \App\Jobs  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }

}