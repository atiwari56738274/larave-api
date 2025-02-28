<?php

namespace App\Http\Controllers\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Candidate;
use App\Models\CoreJobSkills;
use App\Models\CoreJobCity;
use App\Models\CandidateJobPreference;
use App\Models\CandidateJobInterview;
use App\Models\CandidateJobPreferenceSkills;
use App\Models\CandidateJobPreferenceLocations;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Employer\EmployerSubscriptionsController;

class CandidateSearchController extends Controller
{
    private $employerSubscriptionsController;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EmployerSubscriptionsController $employerSubscriptionsController)
    {
        $this->middleware('auth');
        $this->employerSubscriptionsController = $employerSubscriptionsController;
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
            $coreSkills = [];
            $locationIds = [];


            if ($request->skills && ($request->skills != '')) {
                // Replace single quotes with double quotes
                $stringSkill = str_replace("'", '"', $request->skills);
                $skilCore = json_decode($stringSkill, true);
                $coreSkills = CoreJobSkills::whereIn('uuid', $skilCore)->pluck('id')->toArray();

                if(count($coreSkills) == 0){
                    return $this->simpleReturn('error', 'Skills are not found!', 404);
                }
            }

            if ($request->locations && $request->locations != '') {
                // Replace single quotes with double quotes
                $stringLocation = str_replace("'", '"', $request->locations);
                $locationUuids = json_decode($stringLocation, true);
                $locationIds = CoreJobCity::whereIn('uuid', $locationUuids)->pluck('id')->toArray();
    
                if (count($locationIds) == 0) {
                    return $this->simpleReturn('error', 'Locations not found!', 404);
                }
            }

            $records = CandidateJobPreference::where('deleted_at', NULL)
                        ->join('candidate_basic_details', 'candidate_basic_details.user_id', 'candidate_job_preference.user_id');

            if (count($coreSkills) > 0) {
                $records->whereHas('preference_skills', function($query) use($coreSkills) {
                    $query->whereIn('skill_id', $coreSkills);
                });
            }

            if (count($locationIds) > 0) {
                $records->whereHas('preference_location', function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                });
            }
    

            if ($request->notice_period && ($request->notice_period != '')) {
                $records->where('notice_period', $request->notice_period);
            }

            if ($request->work_status && ($request->work_status != '')) {
                $records->where('candidate_basic_details.work_status', $request->work_status);
            }
        
            $records = $records->with(['candidate', 'skills', 'preference_skills','preference_location.location','current_company_details', 'candidate_basic_details'])->withCount('candidate_bookmarked')->paginate($perPageCount);

            $modifiedRecords = $records->getCollection()->transform(function ($item) {
                return [
                    // 'candidate_id' => optional($item->candidate)->id ?? null,
                    'candidate_uuid' => optional($item->candidate)->uuid ?? null,
                    'candidate_name' => optional($item->candidate)->name ?? null,
                    'candidate_title' => optional(optional($item->current_company_details)->job_title)->title ?? null,
                    'candidate_company' => optional($item->current_company_details)->current_company ?? null,
                    'candidate_location' => optional(optional($item->preference_location->first())->location)->title,
                    'candidate_experience_month' => optional($item->candidate_basic_details)->experience_month ?? null,
                    'candidate_experience_year' => optional($item->candidate_basic_details)->experience_year ?? null,
                    'candidate_work_status' => optional($item->candidate_basic_details)->work_status ?? null,
                    'candidate_notice_period' => $item->notice_period,
                    'candidate_bookmarked' => $item->candidate_bookmarked_count,
                    'skills' => $item->skills ?? null,
                    'preference_skills' => $item->preference_skills ?? null,
                ];
            });

            if ($records) {

                $response = [
                    "data" => $modifiedRecords,
                    "current_page" => $records->currentPage(),
                    "last_page"=> $records->lastPage(),
                    "per_page"=> $perPageCount,
                    "total"=> $records->total()
                ];

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

            $checkEmployerSubscription = $this->employerSubscriptionsController->checkIfSubscriptionValid($user_id, 'candidate_unlock', $records->id);
            if(!$checkEmployerSubscription) {
                return $this->simpleReturn('error', "User not have valid subscription!", 400);
            }
            
            if($records){
                $this->employerSubscriptionsController->updateEmployerSubscriptionData($user_id, 'candidate_unlock', $records->id);
                return $this->simpleReturn('success', $records);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
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
}