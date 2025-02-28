<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CompanyProfile;
use App\Models\Jobs;
use App\Models\CoreJobTitle;
use App\Models\CoreJobDepartments;
use App\Models\CoreJobSkills;
use App\Models\JobSkills;
use App\Models\ProductReviews;
use App\Models\CoreJobCity;
use App\Models\CoreJobCompany;
use App\Models\CompanyReviews;
use App\Models\Subscriptions;
use App\Models\CandidateJobSearchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Validator;
use DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchCoreLocations(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CoreJobCity::select('uuid', 'title', )
                ->where('status', 'active')
                ->orderBy('title', 'desc');

                if ($request->title && ($request->title != '')) {
                    $records->where('title', 'like', '%'.$request->title.'%');
                }
            $records = $records->limit(15)->get();

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
    public function fetchCompanyList(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CompanyProfile::select('uuid', 'company_name', 'company_logo','number_of_employee' ,'company_type', 'industry_type', 'state_id', 'country_id','city_id')
                ->with(['state:id,name', 'country:id,name','city:id,name'])
                ->withCount(['jobs', 'reviews'])
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

            $records = Jobs::select('uuid', 'id', 'job_name','job_tag', 'no_of_hire', 'created_at', 'work_location_type', 'salary_min_amount', 'salary_max_amount', 'salary_per_type', 'job_title_id')
                ->with(['job_title'])
                ->with(['job_locations'])
                ->withCount(['candidate_job_saved'])
                ->where('user_id', $isExit->user_id)
                ->where('is_published', '1')
                ->where('status', 'active');

                if (Auth::guard('sanctum')->check()) {
                    $records->withCount(['candidate_job_apply', 'candidate_job_saved']);
                }

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchReviewListByCompanyUuid(Request $request, $company_uuid)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $isExit = CompanyProfile::where('uuid', $company_uuid)->first();
            if (!$isExit) {
                return $this->simpleReturn('error', 'Company not found!', 404);
            }

            $records = CompanyReviews::where('company_profile_id', $isExit->id)
                ->with(['candidate', 'job_title', 'job_location', 'candidate'])
                ->orderBy('created_at', 'desc');
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
    public function fetchDetailsByCompanyUuid(Request $request, $company_uuid)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CompanyProfile::where('uuid', $company_uuid)->with(['company_interview_process', 'company_media','city','state','country'])->withCount(['reviews', 'jobs'])->first();

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
    public function fetchMostSearchJobsDepartment(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $mostJobDept = DB::table('jobs')
                ->select('department_id', DB::raw('COUNT(department_id) as count_num'))
                ->where('is_published', '1')
                ->where('status', 'active')
                ->groupBy('department_id')
                ->orderBy('count_num', 'desc')
                ->limit($perPageCount)
                ->pluck('department_id')
                ->toArray();

            $jobDeptArray = CoreJobDepartments::whereIn('id', $mostJobDept)->get();
            if(count($jobDeptArray)) {
                $resultArray = array();
                foreach ($jobDeptArray as $key => $value) {
                    $jobCount = Jobs::where('department_id', $value->id)->where('is_published', '1')->where('status', 'active')->count('id');

                    $resultArray[] = [
                                        "uuid"=> $value->uuid,
                                        "title"=> $value->title,
                                        "job_count"=> $jobCount
                                    ];
                }

                return $this->simpleReturn('success', $resultArray);
            }

            return $this->simpleReturn('error', 'Data not found!', 404);
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
    public function fetchPopularJobRoles(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $popularJobRoles = DB::table('jobs')
                ->select('job_title_id', DB::raw('COUNT(job_title_id) as count_num'))
                ->where('is_published', '1')
                ->where('status', 'active')
                ->groupBy('job_title_id')
                ->orderBy('count_num', 'desc')
                ->limit($perPageCount)
                ->pluck('job_title_id')
                ->toArray();

            $jobTitleArray = CoreJobTitle::whereIn('id', $popularJobRoles)->get();
            if(count($jobTitleArray)) {
                $resultArray = array();
                foreach ($jobTitleArray as $key => $value) {
                    $jobCount = Jobs::where('job_title_id', $value->id)->where('is_published', '1')->where('status', 'active')->count('id');

                    $jobImages = Jobs::select('company_profile.company_logo')->join('company_profile', 'company_profile.user_id', 'jobs.user_id')->where('job_title_id', $value->id)->where('is_published', '1')->where('status', 'active')->limit(5)->pluck('company_profile.company_logo')->toArray();

                    $resultArray[] = [
                                        "uuid"=> $value->uuid,
                                        "title"=> $value->title,
                                        "job_count"=> $jobCount,
                                        "job_images"=> $jobImages,
                                    ];
                }

                return $this->simpleReturn('success', $resultArray);
            }

            return $this->simpleReturn('error', 'Data not found!', 404);
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
    public function fetchMostActiveJobsCompany(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $mostByCompany = DB::table('jobs')
                ->select('user_id', DB::raw('COUNT(user_id) as count_num'))
                ->where('is_published', '1')
                ->where('status', 'active')
                ->groupBy('user_id')
                ->orderBy('count_num', 'desc')
                ->limit($perPageCount)
                ->pluck('user_id')
                ->toArray();

            $jobCompanyArray = CompanyProfile::whereIn('user_id', $mostByCompany)->get();
            if(count($jobCompanyArray)) {
                $resultArray = array();
                foreach ($jobCompanyArray as $key => $value) {
                    $resultArray[] = [
                                        "uuid"=> $value->uuid,
                                        "name"=> $value->company_name,
                                        "logo"=> $value->company_logo
                                    ];
                }

                return $this->simpleReturn('success', $resultArray);
            }

            return $this->simpleReturn('error', 'Data not found!', 404);
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
    public function fetchMostActiveRecentJobs(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;
            $records = Jobs::select(
                'jobs.id',
                'jobs.uuid',
                'jobs.job_name',
                'jobs.job_title_id',
                'jobs.salary_min_amount',
                'jobs.salary_max_amount',
                'jobs.salary_per_type',
                'jobs.job_type',
                'jobs.job_tag',
                'jobs.state_id',
                'jobs.country_id',
                'jobs.published_at',
                'company_profile.company_name',
                'company_profile.company_logo'
            )
                    ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
                    ->with(['department', 'job_title'])
                    ->with(['job_locations'])
                    ->with(['state', 'country'])
                    ->where('jobs.is_published', '1')
                    ->where('jobs.status', 'active')
                    ->orderBy('jobs.published_at', 'desc');
    

                    if (Auth::guard('sanctum')->check()) {
                        $records->withCount(['candidate_job_apply', 'candidate_job_saved']);
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
    public function getproductReviewList(Request $request)
    {
        try {
            $records = ProductReviews::where('status', 'active')->get();
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTitleSkillCompanyList(Request $request)
    {
        try {
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;  

            $jobTitle = CoreJobTitle::selectRaw("CONCAT('jt_', uuid) as uuid, title")
                ->where('status', 'active')
                ->limit(10);
                if(($request->keyword) && ($request->keyword != '')){
                    $jobTitle->where('title', 'like', '%' . $request->keyword . '%');
                }

            $coreSkill = CoreJobSkills::selectRaw("CONCAT('js_', uuid) as uuid, title")
                ->where('status', 'active')
                ->limit(10);
                if(($request->keyword) && ($request->keyword != '')){
                    $coreSkill->where('title', 'like', '%' . $request->keyword . '%');
                }

            $companyName = CompanyProfile::selectRaw("CONCAT('cn_', uuid) as uuid, company_name as title")
                ->limit(10);
                if(($request->keyword) && ($request->keyword != '')){
                    $companyName->where('company_name', 'like', '%' . $request->keyword . '%');
                }

            $records = $jobTitle->union($coreSkill)->union($companyName)->orderBy('title', 'asc')->get();

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
    public function fetchJobSearch(Request $request)
    {
        
        try {

            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $jobTitles = [];
            $jobSkills = [];
            $jobLocations = [];
            $jobCompany = [];

            $locations = json_decode($request->locations, TRUE);
            $skills = json_decode($request->skills, TRUE);
            $titles = json_decode($request->titles, TRUE);
            $company = json_decode($request->company, TRUE);
          

            if ($locations && count($locations) > 0) {
                $jobLocations = CoreJobCity::select('id')->whereIn('uuid', $locations)->pluck('id')->toArray();
            }

            if ($skills && count($skills) > 0) {
                $jobSkills = CoreJobSkills::select('id')->whereIn('uuid', $skills)->pluck('id')->toArray();
            }

            if ($titles && count($titles) > 0) {
                $jobTitles = CoreJobTitle::select('id')->whereIn('uuid', $titles)->pluck('id')->toArray();
            }

            if ($company && count($company) > 0) {
                $jobCompany = CompanyProfile::select('user_id')->whereIn('uuid', $company)->pluck('user_id')->toArray();
            }

            $records = $this->jobListUsingFilter($jobLocations, $jobSkills, $jobTitles, $jobCompany, $perPageCount);

            if (Auth::guard('sanctum')->check()) {
                $searchHistory = new CandidateJobSearchHistory();
                $searchHistory->uuid = (string) Str::uuid();
                $searchHistory->user_id = Auth::guard('sanctum')->user()->id;
                $searchHistory->job_titles = ($titles && count($titles) > 0) ? json_encode($jobTitles) : null;
                $searchHistory->job_skills = ($skills && count($skills) > 0) ? json_encode($jobSkills) : null;
                $searchHistory->job_locations = ($locations && count($locations) > 0) ? json_encode($jobLocations) : null;
                $searchHistory->job_company = ($company && count($company) > 0) ? json_encode($jobCompany) : null;
                $searchHistory->save();
            }

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchJobSearchBySearchHistory(Request $request, $uuid)
    {
        
        try {

            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $item = CandidateJobSearchHistory::where('uuid', $uuid)->first();
            if (!$item) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $locations = json_decode($item->job_locations, TRUE);
            $skills = json_decode($item->job_skills, TRUE);
            $titles = json_decode($item->job_titles, TRUE);
            $company = json_decode($item->job_company, TRUE);
            $experience = $item->experience;

            $records = $this->jobListUsingFilter($locations, $skills, $titles, $company, $experience, $perPageCount);

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function jobListUsingFilter($locations, $skills, $titles, $company, $perPageCount)
    {

        $records = Jobs::select('uuid', 'id', 'job_name','job_tag', 'no_of_hire', 'created_at', 'work_location_type', 'salary_min_amount', 'salary_max_amount', 'salary_per_type', 'job_title_id', 'min_job_exp', 'max_job_exp')
            ->where('is_published', '1')
            ->where('status', 'active')
            ->with(['job_title', 'job_locations','job_skills'])
            ->withCount(['candidate_job_saved'])
            ->orderBy('created_at', 'desc');

        if ($titles && count($titles) > 0) {
            $records->whereIn('job_title_id', $titles);
        }

        if ($skills && count($skills) > 0) {
            $records->whereHas('job_skills', function($query) use($skills) {
                $query->whereIn('job_skill_id', $skills);
            });
        }

        if ($company && count($company) > 0) {
            $records->whereIn('user_id', $company);
        }

        if ($locations && count($locations) > 0) {
            $records->whereHas('job_locations', function($query) use($locations) {
                $query->whereIn('job_location_id', $locations);
            });
        }

        if (Auth::guard('sanctum')->check()) {
            $records->withCount('candidate_job_apply');
        }
    
        return $records->paginate($perPageCount);        
    }
    
    public function fetchJobsByCategory($deparmentUuid)
    {
        try {
           
            $deparment = CoreJobDepartments::where('uuid', $deparmentUuid)
                ->whereNull('deleted_at')  
                ->first();
    
            if (!$deparment) {
                return $this->simpleReturn('error', 'Category not found.', 404);
            }

            $jobsQuery = Jobs::select('jobs.id', 'jobs.uuid', 'jobs.job_name', 'jobs.job_type','job_tag','jobs.salary_min_amount', 'jobs.salary_max_amount', 'jobs.department_id', 'jobs.job_title_id','company_profile.company_name',
            'company_profile.company_logo')
            ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
            ->with(['department', 'job_title', 'company'])
            ->where('department_id', $deparment->id)  
            ->where('jobs.is_published', 1)
            ->where('jobs.status', 'active')
            ->whereNull('jobs.deleted_at');

            if (Auth::guard('sanctum')->check()) {
                $jobsQuery->withCount(['candidate_job_apply', 'candidate_job_saved']);
            }
    
            $jobs = $jobsQuery->paginate(10); 
    
            if (count($jobs) > 0) {
                return $this->simpleReturn('success', $jobs);
            }
    
            return $this->simpleReturn('error', 'No jobs found for this category.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->simpleReturn('error', 'Failed to fetch jobs by category', 500);
        }
    }
    
    
    public function fetchJobsByRoles(Request $request, $jobTitleUuid)
    {
        try {
           $jobTitle = CoreJobTitle::where('uuid', $jobTitleUuid)
                ->whereNull('deleted_at')
                ->first();
    
            if (!$jobTitle) {
                return $this->simpleReturn('error', 'Job title not found', 404);
            }

          $jobsQuery = Jobs::select('jobs.id', 'jobs.uuid', 'jobs.job_name','job_tag', 'jobs.job_type', 'jobs.salary_min_amount', 'jobs.salary_max_amount', 'jobs.department_id', 'jobs.job_title_id', 'company_profile.company_name',
          'company_profile.company_logo')
            ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
            ->with(['department', 'job_title'])
            ->where('job_title_id', $jobTitle->id)
            ->where('jobs.is_published', 1)
            ->where('jobs.status', 'active')
            ->whereNull('jobs.deleted_at');

            if (Auth::guard('sanctum')->check()) {
                $jobsQuery->withCount(['candidate_job_apply', 'candidate_job_saved']);
            }
    
            $jobs = $jobsQuery->get();
    
            if (count($jobs) > 0) {
                return $this->simpleReturn('success', $jobs);
            }
    
            return $this->simpleReturn('error', 'No jobs found for the given role', 404);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }
    }
    
    public function fetchRelatedJobsBySkills(Request $request, $jobUuid) {
        try {
            $job = Jobs::where('uuid', $jobUuid)
                ->whereNull('deleted_at')
                ->first();
    
            if (!$job) {
                return $this->simpleReturn('error', 'Job not found', 404);
            }
            $jobSkills = JobSkills::where('job_id', $job->id)->pluck('job_skill_id');
    
            if ($jobSkills->isEmpty()) {
                return $this->simpleReturn('error', 'No skills found for the given job', 404);
            }

            $jobsQuery = Jobs::select(
                'jobs.id',
                'jobs.uuid',
                'jobs.job_name',
                'jobs.hiring_company_name',
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
                // 'company_profile.company_name',
                'company_profile.company_logo'
                )
                ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
                ->leftJoin('job_skills', 'job_skills.job_id', '=', 'jobs.id')
                ->with(['department', 'job_title'])
                ->with(['job_locations'])
                ->with(['city','state', 'country'])
                ->whereIn('job_skills.job_skill_id', $jobSkills)
                ->where('jobs.is_published', 1)
                ->where('jobs.status', 'active')
                ->whereNull('jobs.deleted_at')
                ->where('jobs.uuid', '!=', $jobUuid)
                ->distinct()
                ->orderBy('jobs.created_at', 'desc');
    
            if (Auth::guard('sanctum')->check()) {
                $jobsQuery->withCount(['candidate_job_apply', 'candidate_job_saved']);
            }
    
            $jobs = $jobsQuery->get();
    
            if ($jobs->count() > 0) {
                return $this->simpleReturn('success', $jobs);
            }
    
            return $this->simpleReturn('error', 'No jobs found for the given role', 404);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchSubscriptionList(Request $request, $type)
    {
        try {
            $records = Subscriptions::where('type', $type)->where('status', 'active')->get();
            
            if(count($records) > 0){
                return $this->simpleReturn('success', $records);
            }

            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        } 
        
    }

}