<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateJobPreference;
use App\Models\CandidateJobPreferenceSkills;
use App\Models\CandidateJobPreferenceLocations;
use App\Models\CoreJobDepartments;
use App\Models\CoreJobSkills;
use App\Models\CoreJobCity;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateJobPreferenceController extends Controller
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
            $records = CandidateJobPreference::where('user_id', Auth::user()->id)->with(['department', 'preference_skills', 'preference_location'])->first();
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
                'job_type' => 'required',
                'employment_type' => 'required',
                'preferred_shift' => 'required',
                'expected_salary_currency' => 'required',
                'expected_salary_amount' => 'required',
                'notice_period' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $department_exist = CoreJobDepartments::where('uuid', $request->department_uuid)->first();
            if (!$department_exist) {
                return $this->simpleReturn('error', 'Department not valid!', 404);
            }

            $ifExist = CandidateJobPreference::where('user_id', Auth::user()->id)->first();
            if ($ifExist) {
                return $this->simpleReturn('error', 'Preference already exist!');
            }

            $record = new CandidateJobPreference();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->department_id = $department_exist->id;
            $record->job_type = $request->job_type;
            $record->employment_type = $request->employment_type;
            $record->preferred_shift = $request->preferred_shift;
            $record->expected_salary_currency = $request->expected_salary_currency;
            $record->expected_salary_amount = $request->expected_salary_amount;
            $record->notice_period = $request->notice_period;
            if($record->save()) {
                
                $this->processJobPreferenceSkills($record->id, $request->preferred_skills);
                $this->processJobPreferenceLocations($record->id, $request->preferred_location);

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
     * @param  \App\CandidateJobPreference  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateJobPreference  $id
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
     * @param  \App\CandidateJobPreference  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            $rules = array(
                'department_uuid' => 'required',
                'job_type' => 'required',
                'employment_type' => 'required',
                'preferred_shift' => 'required',
                'expected_salary_currency' => 'required',
                'expected_salary_amount' => 'required',
                'notice_period' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $department_exist = CoreJobDepartments::where('uuid', $request->department_uuid)->first();
            if (!$department_exist) {
                return $this->simpleReturn('error', 'Department not valid!', 404);
            }

            $record = CandidateJobPreference::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->department_id = $department_exist->id;
                $record->job_type = $request->job_type;
                $record->employment_type = $request->employment_type;
                $record->preferred_shift = $request->preferred_shift;
                $record->expected_salary_currency = $request->expected_salary_currency;
                $record->expected_salary_amount = $request->expected_salary_amount;
                $record->notice_period = $request->notice_period;
                $record->save();
                if($record){

                    $this->processJobPreferenceSkills($record->id, $request->preferred_skills);
                    $this->processJobPreferenceLocations($record->id, $request->preferred_location);

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
     * @param  \App\CandidateJobPreference  $id
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
     * @param  array $preferred_location
     */
    private function processJobPreferenceLocations($id, $preferred_location)
    {
        if(count($preferred_location) === 0) {
            return;
        } 
        $prefLocation = array();
        foreach ($preferred_location as $lvalue) {
            $core_location_exist = CoreJobCity::where('uuid', $lvalue)->first();
            if($core_location_exist) {
                $location_exist = CandidateJobPreferenceLocations::where('preference_id', $id)->where('location_id', $core_location_exist->id)->first();
                if ($location_exist === null) {
                    $location_records = CandidateJobPreferenceLocations::create([
                        'uuid' => (string) Str::uuid(),
                        'user_id' => Auth::user()->id,
                        'preference_id' => $id,
                        'location_id' => $core_location_exist->id
                    ]);
                }
                $prefLocation[] = $core_location_exist->id;
            }
        }  

        if(count($prefLocation) > 0) {
            $location_records_delete = CandidateJobPreferenceLocations::where('preference_id', $id)->whereNotIn('location_id', $prefLocation)->delete();
        }

        return ;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateJobPreference $id
     * @param  array $preferred_skills
     */
    private function processJobPreferenceSkills($id, $preferred_skills)
    {
        if(count($preferred_skills) === 0) {
            return;
        } 
        $prefLocation = array();
        foreach ($preferred_skills as $lvalue) {
            $core_skill_exist = CoreJobSkills::where('uuid', $lvalue)->first();
            if($core_skill_exist) {
                $skill_exist = CandidateJobPreferenceSkills::where('preference_id', $id)->where('skill_id', $core_skill_exist->id)->first();
                if ($skill_exist === null) {
                    $skill_records = CandidateJobPreferenceSkills::create([
                        'uuid' => (string) Str::uuid(),
                        'user_id' => Auth::user()->id,
                        'preference_id' => $id,
                        'skill_id' => $core_skill_exist->id
                    ]);
                }
                $prefSkill[] = $core_skill_exist->id;
            }
        }  

        if(count($prefSkill) > 0) {
            $skill_records_delete = CandidateJobPreferenceSkills::where('preference_id', $id)->whereNotIn('skill_id', $prefSkill)->delete();
        }

        return ;
    }
}