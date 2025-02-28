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
use App\Models\CandidateJobApply;
use App\Models\CandidateJobPreferenceSkills;
use App\Models\CandidateJobPreferenceLocations;
use App\Models\CandidateJobPreference;
use App\Models\Candidate;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateJobApplyController extends Controller
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
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CandidateJobApply::select('candidate_job_apply.*')
                ->join('jobs', 'jobs.id', 'candidate_job_apply.job_id')
                ->where('jobs.user_id', $user_id)
                ->orderBy('candidate_job_apply.created_at', 'desc');

                if ($request->status && ($request->status != '')) {
                    $records->where('candidate_job_apply.status', $request->status);
                }

            $records = $records->with(['candidate', 'candidate.basic_details', 'interview_schedule', 'job.department', 'job.job_title', 'job.job_skills', 'job.job_locations'])->paginate($perPageCount);
            $records->getCollection()->transform(function ($item) {

                $candidateDetail = CandidateJobPreference::where('user_id', $item->user_id)->first();
                $candidateSkills = CandidateJobPreferenceSkills::select('skill_id')->where('user_id', $item->user_id)->pluck('skill_id')->toArray();
                $candidateLocation = CandidateJobPreferenceLocations::select('location_id')->where('user_id', $item->user_id)->pluck('location_id')->toArray();

                $profile_matching_percantage = 0;
                $skillPercentage = 0;
                $locationPercentage = 0;
                $departmentPercentage = 0;
                $jobSkillArray = json_decode($item->job->job_skills, TRUE);
                $jobLocationArray = json_decode($item->job->job_locations, TRUE);
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

                if($item->job && $candidateDetail && ($item->job->department_id == $candidateDetail->department_id)) {
                    $departmentPercentage = 20;
                } 


                return [
                    'uuid'=> $item->uuid,
                    'status'=> $item->status,
                    'is_new_resume'=> $item->is_new_resume,
                    'new_resume'=> $item->new_resume,
                    'apply_note'=> $item->apply_note,
                    'employer_note'=> $item->employer_note,
                    'follow_up_date'=> $item->follow_up_date,
                    'created_at'=> $item->created_at,
                    'profile_matching_percantage' => round($departmentPercentage + $skillPercentage + $locationPercentage),
                    'job' => $item->job,
                    'candidate' => $item->candidate,
                    // 'interview_schedule' => $item->interview_schedule,
                ];
            });

            $totalJobCount = Jobs::where('user_id', $user_id)->count();
            $totalApplicantCount = CandidateJobApply::whereIn('job_id', function ($query) use ($user_id) {
                $query->select('id')->from('jobs')->where('user_id', $user_id);
            })->count();
    
            $statusCounts = CandidateJobApply::whereIn('job_id', function ($query) use ($user_id) {
                $query->select('id')->from('jobs')->where('user_id', $user_id);
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

            $response = [
                'records' => $records,
                'total_job_count' => $totalJobCount,
                'total_applicant_count' => $totalApplicantCount,
                'status_counts' => $statusCounts,
            ];

            if ($response) {
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
        try {
            $user_id = Auth::user()->id;
            $record = Jobs::where('user_id', $user_id)
                    ->where('uuid', $uuid)
                    ->with(['department', 'job_title'])
                    ->with(['job_locations', 'job_skills', 'job_educations'])
                    ->with(['city', 'state', 'country'])
                    ->orderBy('created_at', 'desc')->first();

            if ($record) {
                return $this->simpleReturn('success', $record);
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
    public function update(Request $request,$uuid)
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

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function updateApplicantStatus(Request $request, $status, $uuid)
    {
        try {
            $statusType = array('applied', 'rejected', 'shortlist', 'on_hold', 'hired', 'interview_scheduled', 'contacted');

            $record = CandidateJobApply::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }
            
            if (in_array($status, $statusType)) {
                $record->status = $status;
            } else {
                return $this->simpleReturn('error', $status.' not valid!', 400);
            }

            if ($record->save()) {
                return $this->simpleReturn('success', "Record updated");
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
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function getCandidateDetails(Request $request, $uuid)
    {
        try {
            $records = Candidate::where('uuid', $uuid);
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
}