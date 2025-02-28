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
use App\Models\CoreJobCompany;
use App\Models\CoreJobEducations;
use App\Models\CoreJobEducationCourse;
use App\Models\CoreJobEducationCourseSpecialty;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\States;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoreDataController extends Controller
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
    public function fetchCoreLocation(Request $request)
    {
        try {
            $records = CoreJobCity::select('id' ,'title', 'uuid')->where('status', 'active')->orderBy('title', 'ASC');
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
            $records = CoreJobSkills::select('id' ,'title', 'uuid')->where('status', 'active')->orderBy('title', 'ASC');
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
            $records = CoreJobCompany::select('id' ,'title', 'uuid')->where('status', 'active')->orderBy('title', 'ASC');
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
    public function fetchCoreEducations(Request $request)
    {
        try {
            $records = CoreJobEducations::select('id' ,'title', 'uuid')->with('core_job_education_course')->where('status', 'active')->orderBy('title', 'ASC');
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
    public function fetchCoreEducationCourse(Request $request, $job_education_id)
    {
        try {
            $records = CoreJobEducationCourse::select('id' ,'title', 'uuid')->where('job_education_id', $job_education_id)->with('core_job_education_course_specialty')->where('status', 'active')->orderBy('title', 'ASC');
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
    public function fetchCoreEducationCourseSpecialty(Request $request, $job_education_course_uuid)
    {
        try {
            $educationCourseExist = CoreJobEducationCourse::where('uuid', $job_education_course_uuid)->where('status', 'active')->first();
            if(!$educationCourseExist) {
                return $this->simpleReturn('error', 'job_education_course_uuid not valid', 404);
            }
            $records = CoreJobEducationCourseSpecialty::where('job_education_course_id', $educationCourseExist->id)->with('core_job_education_course')->where('status', 'active')->orderBy('title', 'ASC');
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
            $records = CoreJobTitle::select('id' ,'title', 'uuid')->where('status', 'active')->orderBy('title', 'ASC');
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
    public function fetchCoreCountry(Request $request)
    {
        try {
            $records = Countries::select('id' ,'name', 'shortname', 'uuid')->orderBy('name', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('name', 'like', '%' . $request->keyword . '%')->limit(15);
            }
            $records = $records->get();
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
    public function fetchCoreState(Request $request, $country_id)
    {
        try {
            $records = States::select('id' ,'name', 'uuid')->where('country_id', $country_id)->orderBy('name', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('name', 'like', '%' . $request->keyword . '%')->limit(15);
            }
            $records = $records->get();
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
    public function fetchCoreCity(Request $request, $state_id)
    {
        try {
            $records = Cities::select('id' ,'name', 'uuid')->where('state_id', $state_id)->orderBy('name', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('name', 'like', '%' . $request->keyword . '%')->limit(15);
            }
            $records = $records->get();
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