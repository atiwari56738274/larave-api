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
use App\Models\States;
use App\Models\Countries;
use App\Models\Cities;
use App\Models\CoreJobTitle;
use App\Models\CoreJobDepartments;
use App\Models\CoreJobSkills;
use App\Models\CoreJobCity;
use App\Models\CoreJobEducations;
use App\Models\Employer;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Employer\EmployerSubscriptionsController;

class JobsController extends Controller
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

            $records = Jobs::where('user_id', $user_id)
                ->with(['department', 'job_title'])
                ->with(['job_locations', 'job_skills', 'job_educations'])
                ->with(['city', 'state', 'country'])
                ->withCount(['shortlisted_candidate', 'applicants'])
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
        try {
            $rules = array(
                'department_uuid' => 'required',
                'job_name' => 'required',
                'hiring_company_name' => 'required',
                'job_title_uuid' => 'required',
                'work_location_type' => 'required',
                'job_location' => 'required',
                'job_skills' => 'required',
                'job_education' => 'required',
                'job_type' => 'required',
                'job_shift' => 'required',
                'pay_type' => 'required',
                'job_apply_age' => 'required',
                'is_planned_start_date' => 'required',
                'no_of_hire' => 'required',
                'salary_not_disclose' => 'required',
                'job_description' => 'required',
                'joining_amount_required' => 'required',
                'upload_cv' => 'required',
                'job_deadline' => 'required',
                'min_job_exp' => 'required',
                'max_job_exp' => 'required',
                'interview_type' => 'required',
                'candidate_contact_via' => 'required',
                'address1' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required',
                'status' => 'required',
                'is_published'=>'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $user_id = Auth::user()->id;

            $checkEmployerSubscription = $this->employerSubscriptionsController->checkIfSubscriptionValid($user_id, 'job_post');
            if(!$checkEmployerSubscription) {
                return $this->simpleReturn('error', "User not have valid subscription!", 400);
            }

            $title_exist = CoreJobTitle::where('uuid', $request->job_title_uuid)->first();
            if (!$title_exist) {
                return $this->simpleReturn('error', 'Title not valid!', 404);
            }

            $city_exist = Cities::where('uuid', $request->city_uuid)->first();
            if (!$city_exist) {
                return $this->simpleReturn('error', 'City not valid!', 404);
            }

            $state_exist = States::where('uuid', $request->state_uuid)->first();
            if (!$state_exist) {
                return $this->simpleReturn('error', 'State not valid!', 404);
            }

            $country_exist = Countries::where('uuid', $request->country_uuid)->first();
            if (!$country_exist) {
                return $this->simpleReturn('error', 'State not valid!', 404);
            }

            $department_exist = CoreJobDepartments::where('uuid', $request->department_uuid)->first();
            if (!$department_exist) {
                return $this->simpleReturn('error', 'Department not valid!', 404);
            }

            $record = new Jobs();
            $record->uuid = (string) Str::uuid();
            $record->user_id = $user_id;
            $record->department_id = $department_exist->id;
            $record->job_name = $request->job_name;
            $record->hiring_company_name = $request->hiring_company_name;
            $record->job_title_id = $title_exist->id;
            $record->work_location_type = $request->work_location_type;
            $record->job_type = $request->job_type;
            $record->job_shift = $request->job_shift;
            $record->job_tag = $request->job_tag;
            $record->pay_type = $request->pay_type;
            $record->job_apply_age = $request->job_apply_age;
            $record->additional_pay = $request->additional_pay;
            $record->is_planned_start_date = $request->is_planned_start_date;
            $record->plan_start_date = $request->plan_start_date;
            $record->no_of_hire = $request->no_of_hire;
            $record->salary_not_disclose = $request->salary_not_disclose;
            $record->salary_min_amount = $request->salary_min_amount;
            $record->salary_max_amount = $request->salary_max_amount;
            $record->salary_per_type = $request->salary_per_type;
            $record->job_description = $request->job_description;
            $record->joining_amount_required = $request->joining_amount_required;
            $record->upload_cv = $request->upload_cv;
            $record->job_deadline = $request->job_deadline;
            $record->min_job_exp = $request->min_job_exp;
            $record->max_job_exp = $request->max_job_exp;
            $record->interview_type = $request->interview_type;
            $record->candidate_contact_via = $request->candidate_contact_via;
            $record->address1 = $request->address1;
            $record->address2 = $request->address2;
            $record->city_id = $city_exist->id;
            $record->state_id = $state_exist->id;
            $record->country_id = $country_exist->id;
            $record->pincode = $request->pincode;
            $record->status = $request->status;
            $record->is_published = $request->is_published;
            $record->added_by = $user_id;
            if($record->save()) {

                $this->employerSubscriptionsController->updateEmployerSubscriptionData($user_id, 'job_post', $record->id);

                $this->processJobSkills($record->id, $request->job_skills);
                $this->processJobLocations($record->id, $request->job_location);
                $this->processJobEducations($record->id, $request->job_education);

                return $this->simpleReturn('success', 'Added successfully');
            }
            return $this->simpleReturn('error', 'Error in insertion');
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
        // try {
            $rules = array(
                'department_uuid' => 'required',
                'job_name' => 'required',
                'hiring_company_name' => 'required',
                'job_title_uuid' => 'required',
                'work_location_type' => 'required',
                'job_location' => 'required',
                'job_skills' => 'required',
                'job_education' => 'required',
                'job_type' => 'required',
                'job_shift' => 'required',
                'pay_type' => 'required',
                'job_apply_age' => 'required',
                'is_planned_start_date' => 'required',
                'no_of_hire' => 'required',
                'salary_not_disclose' => 'required',
                'job_description' => 'required',
                'joining_amount_required' => 'required',
                'upload_cv' => 'required',
                'job_deadline' => 'required',
                'min_job_exp' => 'required',
                'max_job_exp' => 'required',
                'interview_type' => 'required',
                'candidate_contact_via' => 'required',
                'address1' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required',
                'status' => 'required',
                'is_published'=>'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $title_exist = CoreJobTitle::where('uuid', $request->job_title_uuid)->first();
            if (!$title_exist) {
                return $this->simpleReturn('error', 'Title not valid!', 404);
            }

            $city_exist = Cities::where('uuid', $request->city_uuid)->first();
            if (!$city_exist) {
                return $this->simpleReturn('error', 'City not valid!', 404);
            }

            $state_exist = States::where('uuid', $request->state_uuid)->first();
            if (!$state_exist) {
                return $this->simpleReturn('error', 'State not valid!', 404);
            }

            $country_exist = Countries::where('uuid', $request->country_uuid)->first();
            if (!$country_exist) {
                return $this->simpleReturn('error', 'State not valid!', 404);
            }

            $department_exist = CoreJobDepartments::where('uuid', $request->department_uuid)->first();
            if (!$department_exist) {
                return $this->simpleReturn('error', 'Department not valid!', 404);
            }

            $record = Jobs::where('uuid', $uuid)->where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->department_id = $department_exist->id;
                $record->job_name = $request->job_name;
                $record->hiring_company_name = $request->hiring_company_name;
                $record->job_title_id = $title_exist->id;
                $record->work_location_type = $request->work_location_type;
                $record->job_type = $request->job_type;
                $record->job_shift = $request->job_shift;
                $record->job_tag = $request->job_tag;
                $record->pay_type = $request->pay_type;
                $record->job_apply_age = $request->job_apply_age;
                $record->additional_pay = $request->additional_pay;
                $record->is_planned_start_date = $request->is_planned_start_date;
                $record->plan_start_date = $request->plan_start_date;
                $record->no_of_hire = $request->no_of_hire;
                $record->salary_not_disclose = $request->salary_not_disclose;
                $record->salary_min_amount = $request->salary_min_amount;
                $record->salary_max_amount = $request->salary_max_amount;
                $record->salary_per_type = $request->salary_per_type;
                $record->job_description = $request->job_description;
                $record->joining_amount_required = $request->joining_amount_required;
                $record->upload_cv = $request->upload_cv;
                $record->job_deadline = $request->job_deadline;
                $record->min_job_exp = $request->min_job_exp;
                $record->max_job_exp = $request->max_job_exp;
                $record->interview_type = $request->interview_type;
                $record->candidate_contact_via = $request->candidate_contact_via;
                $record->address1 = $request->address1;
                $record->address2 = $request->address2;
                $record->city_id = $city_exist->id;
                $record->state_id = $state_exist->id;
                $record->country_id = $country_exist->id;
                $record->pincode = $request->pincode;
                $record->status = $request->status;
                $record->is_published = $request->is_published;
                $record->updated_by = Auth::user()->id;
                $record->save();
                if($record){
                    $this->processJobSkills($record->id, $request->job_skills);
                    $this->processJobLocations($record->id, $request->job_location);
                    $this->processJobEducations($record->id, $request->job_education);
                    return $this->simpleReturn('success', 'Updated successfully');
                }
            }
            return $this->simpleReturn('error', 'Error in updation');
        // } catch (\Exception $exception){
        //     Log::error($exception);
        //     return $this->simpleReturn('error', 'Something went wrong, please contact support');
        // }    
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
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateJobPreference $id
     * @param  array $job_skills
     */
    private function processJobSkills($id, $job_skills)
    {
        if(count($job_skills) === 0) {
            return;
        } 
        $jobSkill = array();
        foreach ($job_skills as $lvalue) {
            $core_skill_exist = CoreJobSkills::where('uuid', $lvalue)->first();
            if($core_skill_exist) {
                $skill_exist = JobSkills::where('job_id', $id)->where('job_skill_id', $core_skill_exist->id)->first();
                if ($skill_exist === null) {
                    $skill_records = JobSkills::create([
                        'uuid' => (string) Str::uuid(),
                        'job_id' => $id,
                        'job_skill_id' => $core_skill_exist->id
                    ]);
                }
                $jobSkill[] = $core_skill_exist->id;
            }
        }  

        if(count($jobSkill) > 0) {
            $skill_records_delete = JobSkills::where('job_id', $id)->whereNotIn('job_skill_id', $jobSkill)->delete();
        }

        return ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateJobPreference $id
     * @param  array $job_locations
     */
    private function processJobLocations($id, $job_locations)
    {
        if(count($job_locations) === 0) {
            return;
        } 
        $jobLocations = array();
        foreach ($job_locations as $lvalue) {
            $core_location_exist = CoreJobCity::where('uuid', $lvalue)->first();
            if($core_location_exist) {
                $location_exist = JobLocations::where('job_id', $id)->where('job_location_id', $core_location_exist->id)->first();
                if ($location_exist === null) {
                    $location_records = JobLocations::create([
                        'uuid' => (string) Str::uuid(),
                        'job_id' => $id,
                        'job_location_id' => $core_location_exist->id
                    ]);
                }
                $jobLocations[] = $core_location_exist->id;
            }
        }  

        if(count($jobLocations) > 0) {
            $location_records_delete = JobLocations::where('job_id', $id)->whereNotIn('job_location_id', $jobLocations)->delete();
        }

        return ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateJobPreference $id
     * @param  array $job_education
     */
    private function processJobEducations($id, $job_education)
    {
        if(count($job_education) === 0) {
            return;
        } 
        $jobEducation = array();
        foreach ($job_education as $lvalue) {
            $core_education_exist = CoreJobEducations::where('uuid', $lvalue)->first();
            if($core_education_exist) {
                $education_exist = JobEducations::where('job_id', $id)->where('job_education_id', $core_education_exist->id)->first();
                if ($education_exist === null) {
                    $education_records = JobEducations::create([
                        'uuid' => (string) Str::uuid(),
                        'job_id' => $id,
                        'job_education_id' => $core_education_exist->id
                    ]);
                }
                $jobEducation[] = $core_education_exist->id;
            }
        }  

        if(count($jobEducation) > 0) {
            $education_records_delete = JobEducations::where('job_id', $id)->whereNotIn('job_education_id', $jobEducation)->delete();
        }

        return ;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $status, $uuid)
    {
        try {
            $statusType = array("active", "inactive", "completed", "cancelled");
            $publishedType = array("published", "unpublished");

            $user_id = Auth::user()->id;

            $record = Jobs::where('uuid', $uuid)->where('user_id', $user_id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if($status =='published') {
                $checkEmployerSubscription = $this->employerSubscriptionsController->checkIfSubscriptionValid($user_id, 'job_published', $record->id);
                if(!$checkEmployerSubscription) {
                    return $this->simpleReturn('error', "User not have valid subscription!", 400);
                }
            }
            
            if (in_array($status, $statusType)) {
                $record->status = $status;
            } else if (in_array($status, $publishedType)) {
                $record->is_published = ($status =='published') ? 1 : 0;
                if($status =='published') {
                    $record->published_at = date('Y-m-d H:i:s');
                }
            } else {
                return $this->simpleReturn('error', $status.' not valid!', 400);
            }

            if ($record->save()) {
                if($status =='published') {
                    $this->employerSubscriptionsController->updateEmployerSubscriptionData($user_id, 'job_published', $record->id);
                }
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
    public function applicantsByJobUuid(Request $request, $job_uuid)
    {
        try {
            $job_record = Jobs::where('uuid', $job_uuid)->where('user_id', Auth::user()->id)->first();
            if (!$job_record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }
            
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;

            $records = CandidateJobApply::where('job_id', $job_record->id)
                ->with(['candidate'])
                ->orderBy('created_at', 'desc');

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
     * Display the specified resource.
     *
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function updateJobHiringTag(Request $request, $uuid)
    {
        try {
            $rules = array(
                'urgent_hiring_tag' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $user_id = Auth::user()->id;

            $record = Jobs::where('uuid', $uuid)->where('user_id', $user_id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $checkEmployerSubscription = $this->employerSubscriptionsController->checkIfSubscriptionValid($user_id, 'urgent_hiring_tag', $record->id);
            if(!$checkEmployerSubscription) {
                return $this->simpleReturn('error', "User not have valid subscription!", 400);
            }

            if($record->urgent_hiring_tag_enable && $request->urgent_hiring_tag) {
                return $this->simpleReturn('error', 'Urgent hiring tag already enabled', 400);
            } else if(!$record->urgent_hiring_tag_enable && !$request->urgent_hiring_tag) {
                return $this->simpleReturn('error', 'Urgent hiring tag already disabled', 400);
            } else if(!$record->urgent_hiring_tag_enable && $request->urgent_hiring_tag) {
                $record->urgent_hiring_tag_enable = true;
                if ($record->save()) {
                    $this->employerSubscriptionsController->updateEmployerSubscriptionData($user_id, 'urgent_hiring_tag', $record->id);
                    return $this->simpleReturn('success', "Job hiring tag enabled");
                }
            } 
            return $this->simpleReturn('success', "Record not updated");
                    
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }  
        
    }
}