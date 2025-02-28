<?php

namespace App\Http\Controllers\Admin;
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
     * Constructor with middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of testimonials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
            'jobs.status',
            'jobs.is_published',
            'company_profile.company_name',
            'company_profile.company_logo'
        )
            ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
            ->with(['department', 'job_title', 'job_locations', 'city', 'state', 'country'])
            ->orderBy('jobs.created_at', 'desc');

        if ($request->department_id && ($request->department_id != '')) {
            $records->where('department_id', $request->department_id);
        }

       
        if ($request->hiring_company_name && ($request->hiring_company_name != '')) {
            $records->where('company_profile.company_name', 'like', '%' . $request->hiring_company_name . '%');
        }
    
        $records = $records->paginate($perPageCount);
    
        if ($records) {
            return $this->simpleReturn('success', $records);
        } else {
            return $this->simpleReturn('error', 'Record not found!', 404);
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
     * @param  \App\Jobs  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
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