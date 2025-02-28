<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Jobs;
use App\Models\CandidateSkills;
use App\Models\JobCity;
use App\Models\JobSkills;
use App\Models\JobEducations;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class JobsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $perPageCount = $request->perPageCount ? $request->perPageCount : 15;
        $records = Jobs::select(
            'jobs.id',
            'jobs.uuid',
            'jobs.job_name',
            'jobs.job_title_id',
            'jobs.department_id',
            'jobs.salary_min_amount',
            'jobs.salary_max_amount',
            'jobs.salary_per_type',
            'jobs.job_type',
            'jobs.job_tag',
            'jobs.city_id',
            'jobs.state_id',
            'jobs.country_id',
            'jobs.published_at',
            'jobs.created_at as job_post_date',
            'company_profile.company_name',
            'company_profile.company_logo'
        )
                ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
                ->with(['department', 'job_title'])
                ->with(['job_locations'])
                ->with(['city','state', 'country'])
                ->where('jobs.is_published', '1')
                ->where('jobs.status', 'active')
                ->orderBy('jobs.created_at', 'desc');

                if (Auth::guard('sanctum')->check()) {
                    $records->withCount(['candidate_job_apply', 'candidate_job_saved']);
                }

            if ($request->department_id && ($request->department_id != '')) {
                $records->where('department_id', $request->department_id);
            }

            if ($request->hiring_company_name && ($request->hiring_company_name != '')) {
                $records->where('hiring_company_name', 'like', '%'.$request->hiring_company_name.'%');
            }

        $records = $records->paginate($perPageCount);

        if ($records) {
            return $this->simpleReturn('success', $records);
        } else {
            return $this->simpleReturn('error', 'Record not found!', 404);
        }
        // if (Auth::guard('sanctum')->check()) {
        //     return $this->simpleReturn('success', "successfully");
        // } else {
        //     return $this->simpleReturn('success', "failed");
        // }
 
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
     * @param  \App\Jobs  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        try {
            $record = Jobs::select(
                    'jobs.*',
                    'company_profile.company_logo'
                    )
                    ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
                    ->where('jobs.uuid', $uuid)
                    ->with(['department', 'job_title'])
                    ->with(['job_locations', 'job_skills', 'job_educations'])
                    ->with(['city', 'state', 'country']);
                    if (Auth::guard('sanctum')->check()) {
                        $record->withCount('candidate_job_apply','candidate_job_saved');
                    }
                    $record = $record->orderBy('created_at', 'desc')->first();

            if ($record) {
        
                $alreadyApplied = $record->applicants()
                    ->where('user_id', Auth::guard('sanctum')->id())
                    ->exists();

                $isExpired = strtotime($record->job_deadline) < time();

                $response = $record->toArray();
                $response['already_applied'] = $alreadyApplied;
                $response['is_expired'] = $isExpired ? 1 : 0;

                return $this->simpleReturn('success', $response);
            } else {
                return $this->simpleReturn('error', 'Record not found!', 404);
            }
              
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        } 
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
     * @param  \App\Jobs  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //    
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