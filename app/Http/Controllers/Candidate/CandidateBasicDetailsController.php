<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateBasicDetails;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CandidateBasicDetailsController extends Controller
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
        //
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
                'gender' => 'required',
                'dob' => 'required',
                'marital_status' => 'required',
                'address' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required',
                'profile_summery' => 'required',
                'work_status' => 'required',
                'active_job_search' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $ifExist = CandidateBasicDetails::where('user_id', Auth::user()->id)->first();
            if ($ifExist) {
                return $this->simpleReturn('error', 'Details already exist!', 400);
            }

            $city = Cities::where('uuid',$request->city_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }
            $state = States::where('uuid',$request->state_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }
            $country = Countries::where('uuid',$request->country_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }



            $fileNameResume = null;
            $fileNameProfile = null;

            if (request()->hasFile('resume')) {
                $file = request()->file('resume');
                $fileNameResume = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('/uploads/candidate/resume/', $fileNameResume);
            }

            if (request()->hasFile('profile_pic')) {
                $file = request()->file('profile_pic');
                $fileNameProfile = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('/uploads/candidate/profile/', $fileNameProfile);
            }

            $record = new CandidateBasicDetails();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->gender = $request->gender;
            $record->dob = $request->dob;
            $record->marital_status = $request->marital_status;
            $record->address = $request->address;
            $record->address1 = $request->address1;
            $record->city_id = $city->id;
            $record->state_id = $state->id;
            $record->country_id = $country->id;
            $record->pincode = $request->pincode;
            $record->profile_summery = $request->profile_summery;
            $record->work_status = $request->work_status;
            $record->experience_year = $request->experience_year;
            $record->experience_month = $request->experience_month;
            $record->active_job_search = ($request->active_job_search == 'true') ? true : false;
            $record->resume = $fileNameResume;
            $record->profile_pic = $fileNameProfile;
            if($record->save()) {
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
     * @param  \App\CandidateBasicDetails  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateBasicDetails  $id
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
     * @param  \App\CandidateBasicDetails  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'gender' => 'required',
                'dob' => 'required',
                'marital_status' => 'required',
                'address' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required',
                'profile_summery' => 'required',
                'work_status' => 'required',
                'active_job_search' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateBasicDetails::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $record = CandidateBasicDetails::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $city = Cities::where('uuid',$request->city_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }
            $state = States::where('uuid',$request->state_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }
            $country = Countries::where('uuid',$request->country_uuid)->first();
            if(!$city){
                return $this->simpleReturn('error','City not found by UUID',404);
            }


            if ($record) {
                $record->gender = $request->gender;
                $record->dob = $request->dob;
                $record->marital_status = $request->marital_status;
                $record->address = $request->address;
                $record->address1 = $request->address1;
                $record->city_id = $city->id;
                $record->state_id = $state->id;
                $record->country_id = $country->id;
                $record->pincode = $request->pincode;
                $record->profile_summery = $request->profile_summery;
                $record->work_status = $request->work_status;
                $record->experience_year = $request->experience_year;
                $record->experience_month = $request->experience_month;
                $record->active_job_search = $request->active_job_search;

                if (request()->hasFile('resume')) {
                    $file = request()->file('resume');
                    $fileNameResume = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('/uploads/candidate/resume/', $fileNameResume);
                    $record->resume = $fileNameResume;
                }

                if (request()->hasFile('profile_pic')) {
                    $file = request()->file('profile_pic');
                    $fileNameProfile = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('/uploads/candidate/profile/', $fileNameProfile);
                    $record->profile_pic = $fileNameProfile;
                }

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CandidateBasicDetails  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        // try {
            $rules = array(
                'gender' => 'required',
                'dob' => 'required',
                'marital_status' => 'required',
                'address' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
                'pincode' => 'required',
                'profile_summery' => 'required',
                'work_status' => 'required',
                'active_job_search' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateBasicDetails::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->gender = $request->gender;
                $record->dob = $request->dob;
                $record->marital_status = $request->marital_status;
                $record->address = $request->address;
                $record->address1 = $request->address1;
                $record->city_id = $request->city_id;
                $record->state_id = $request->state_id;
                $record->country_id = $request->country_id;
                $record->pincode = $request->pincode;
                $record->profile_summery = $request->profile_summery;
                $record->work_status = $request->work_status;
                $record->experience_year = $request->experience_year;
                $record->experience_month = $request->experience_month;
                $record->active_job_search = $request->active_job_search;

                $record->save();
                if($record){
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
     * @param  \App\CandidateBasicDetails  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //   
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePic(Request $request)
    {
        try {
            $rules = array(
                'profile_pic' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateBasicDetails::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                if (request()->hasFile('profile_pic')) {
                    $file = $request->file('profile_pic');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = public_path('uploads/candidate/profile');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0775, true); // true for recursive directory creation
                    }

                    // delete existing file
                    if($record->profile_pic) {
                        $deletefilePath = public_path('uploads/candidate/profile/'.$record->profile_pic);
                        if (file_exists($deletefilePath)) {
                            unlink($deletefilePath);
                        }
                    }

                    $file->move($path, $fileName);
                    $record->profile_pic = $fileName;

                    $record->save();
                    if($record){
                        return $this->simpleReturn('success', 'File Updated successfully');
                    }
                } else {
                    return $this->simpleReturn('error', 'Image not uploaded', 404);
                }

            }
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function updateResume(Request $request)
    {
        try {
            $rules = array(
                'resume' => 'required|file|mimes:pdf,doc,docx|max:2048'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CandidateBasicDetails::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                if (request()->hasFile('resume')) {
                    $file = $request->file('resume');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = public_path('uploads/candidate/resume');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0775, true); // true for recursive directory creation
                    }

                    // delete existing file
                    if($record->resume) {
                        $deletefilePath = public_path('uploads/candidate/resume/'.$record->resume);
                        if (file_exists($deletefilePath)) {
                            unlink($deletefilePath);
                        }
                    }

                    $file->move($path, $fileName);
                    $record->resume = $fileName;

                    $record->save();
                    if($record){
                        return $this->simpleReturn('success', 'File Updated successfully');
                    }
                } else {
                    return $this->simpleReturn('error', 'Image not uploaded', 404);
                }

            }
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }    
    }

}