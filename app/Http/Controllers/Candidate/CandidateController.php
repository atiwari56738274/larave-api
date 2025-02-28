<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateEmployment;
use App\Models\CoreJobCity;
use App\Models\CoreJobTitle;
use App\Models\CoreJobSkills;
use App\Models\Candidate;
use App\Models\CandidateJobPreference;
use App\Models\CandidateJobPreferenceSkills;
use App\Models\CandidateJobPreferenceLocations;
use App\Models\Jobs;
use App\Models\CandidateJobSaved;
use App\Models\CandidateJobApply;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateController extends Controller
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
    public function fetchCandidateDetails(Request $request)
    {
        try {
            $records = Candidate::where('id', Auth::user()->id);
                    if($request->basic_details && $request->basic_details != '') {
                        $records->with('basic_details');
                    }
                    if($request->job_preference && $request->job_preference != '') {
                        $records->with('job_preference');
                    }
                    if($request->employment && $request->employment != '') {
                        $records->with('employment');
                    }
                    if($request->projects && $request->projects != '') {
                        $records->with('projects');
                    }
                    if($request->skills && $request->skills != '') {
                        $records->with('skills');
                    }
                    if($request->education && $request->education != '') {
                        $records->with('education');
                    }
                    if($request->certifications && $request->certifications != '') {
                        $records->with('certifications');
                    }
                    if($request->it_skills && $request->it_skills != '') {
                        $records->with('it_skills');
                    }
                    if($request->current_company && $request->current_company != '') {
                        $records->with('current_company_details');
                    }
                    if($request->candidate_languages && $request->candidate_languages != '') {
                        $records->with('candidate_languages');
                    }
            $records = $records->first();
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchCandidateCareerProfile(Request $request)
    {
        try {
            $records = CandidateEmployment::where('user_id', Auth::user()->id)->where('is_current_employement', 1)->first();
            if($records){
                $records->load('job_title', 'offered_job_title', 'department');
                $data = [
                    'user_id' => $records->user_id,

                    'is_current_employement' => $records->is_current_employement,
                    'job_title' => $records->job_title ? $records->job_title->uuid : null,
                    'offered_job_title' => $records->offered_job_title ? $records->offered_job_title->uuid : null,
                    'department' => $records->department ? $records->department->uuid : null,
                ];
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
    public function fetchCoreLocation(Request $request)
    {
        try {
            $records = CoreJobCity::select('id' ,'title')->where('status', 'active')->orderBy('title', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('title', 'like', '%' . $request->keyword . '%');
            }
            $records = $records->limit(15)->get();
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
    public function fetchCoreSkills(Request $request)
    {
        try {
            $records = CoreJobSkills::select('id' ,'title')->where('status', 'active')->orderBy('title', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('title', 'like', '%' . $request->keyword . '%');
            }
            $records = $records->limit(15)->get();
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
    public function fetchCoreCompany(Request $request)
    {
        try {
            $records = CoreJobCompany::select('id' ,'title')->where('status', 'active')->orderBy('title', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('title', 'like', '%' . $request->keyword . '%');
            }
            $records = $records->limit(15)->get();
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
    public function fetchCoreTitle(Request $request)
    {
        try {
            $records = CoreJobTitle::select('id' ,'title')->where('status', 'active')->orderBy('title', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('title', 'like', '%' . $request->keyword . '%');
            }
            $records = $records->limit(15)->get();
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
    public function fetchRecommendedJobs(Request $request)
    {
        try {

            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $candidateDetail = CandidateJobPreference::where('user_id', Auth::user()->id)->first();
            $candidateSkills = CandidateJobPreferenceSkills::select('skill_id')->where('user_id', Auth::user()->id)->pluck('skill_id')->toArray();
            $candidateLocation = CandidateJobPreferenceLocations::select('location_id')->where('user_id', Auth::user()->id)->pluck('location_id')->toArray();
            

            $records = Jobs::select('jobs.uuid', 'jobs.id', 'jobs.job_name', 'jobs.no_of_hire', 'jobs.created_at', 'jobs.work_location_type','jobs.job_type','jobs.job_tag','jobs.salary_min_amount', 'jobs.salary_max_amount', 'jobs.salary_per_type', 'jobs.job_title_id',  'company_profile.company_name',
            'company_profile.company_logo')
                ->leftJoin('company_profile', 'jobs.user_id', '=', 'company_profile.user_id')
                ->where('department_id', $candidateDetail->department_id)
                ->where('jobs.is_published', '1')
                ->where('jobs.status', 'active')
                ->with(['job_title', 'job_locations', 'job_skills'])
                ->orderBy('jobs.created_at', 'desc');

            if (count($candidateSkills) > 0) {
                $records->whereHas('job_skills', function($query) use($candidateSkills) {
                    $query->whereIn('job_skill_id', $candidateSkills);
                });
            }

            if (count($candidateLocation) > 0) {
                $records->whereHas('job_locations', function($query) use($candidateLocation) {
                    $query->whereIn('job_location_id', $candidateLocation);
                });
            }
        
            $records = $records->paginate($perPageCount)->map(function ($item) use ($candidateLocation, $candidateSkills) {
                $profile_matching_percantage = 0;
                $skillPercentage = 0;
                $locationPercentage = 0;
                $jobSkillArray = json_decode($item->job_skills, TRUE);
                $jobLocationArray = json_decode($item->job_locations, TRUE);
                $matchingCountSkill = 0;
                $matchingCountLocation = 0;

                if((count($jobLocationArray) > 0) && (count($candidateLocation) > 0)) {
                    $jobLocationIds = array_map(function($item) {
                        return $item['job_location_id'];
                    }, $jobLocationArray);

                    // Find the common elements between the two arrays and count matching record
                    $commonElementsLocation = array_intersect($jobLocationIds, $candidateLocation);
                    $matchingCountLocation = count($commonElementsLocation);

                    $jobLocatPer = (40 / count($jobLocationArray));
                    $locationPercentage = ($matchingCountLocation * $jobLocatPer);
                }

                if((count($jobSkillArray) > 0) && (count($candidateSkills) > 0)) {
                    $jobSkillIds = array_map(function($item) {
                        return $item['job_skill_id'];
                    }, $jobSkillArray);

                    // Find the common elements between the two arrays and count matching record
                    $commonElementsSkill = array_intersect($jobSkillIds, $candidateSkills);
                    $matchingCountSkill = count($commonElementsSkill);

                    $jobSkillPer = (40 / count($jobSkillArray));
                    $skillPercentage = ($matchingCountSkill * $jobSkillPer);
                }

                $candidate_job_saved = CandidateJobSaved::where('job_id', $item->id)
                ->where('user_id', Auth::id())
                ->exists()? 1:0 ;

                $candidate_job_apply = CandidateJobApply::where('job_id', $item->id)
                ->where('user_id', Auth::id())
                ->exists()? 1:0 ;

                return [
                    'uuid' => $item->uuid,
                    'job_name' => $item->job_name,
                    'no_of_hire' => $item->no_of_hire,
                    'created_at' => $item->created_at,
                    'work_location_type' => $item->work_location_type,
                    'job_type' => $item->job_type,
                    'job_tag' => $item->job_tag,
                    'salary_min_amount' => $item->salary_min_amount,
                    'salary_max_amount' => $item->salary_max_amount,
                    'salary_per_type' => $item->salary_per_type,
                    'company_name' =>$item->company_name,
                    'company_logo'=>$item->company_logo,
                    'job_title' => $item->job_title,
                    'job_locations' => $item->job_locations,
                    'candidate_job_saved' => $candidate_job_saved,
                    'candidate_job_apply' => $candidate_job_apply,
                    'profile_matching_percantage' => round(20 + $skillPercentage + $locationPercentage),
                    // 'matchingCountLocation' => $matchingCountLocation,
                    // 'matchingCountSkill' => $matchingCountSkill,
                    // 'jobLocationIds' => $jobLocationIds,
                    // 'jobSkillIds' => $jobSkillIds,
                    // 'candidateLocation' => $skillPercentage,
                    // 'candidateSkills' => $locationPercentage,
                ];
            });;

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