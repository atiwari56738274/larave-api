<?php

namespace App\Http\Controllers\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CompanyProfile;
use App\Models\Cities;
use App\Models\States;
use App\Models\Countries;
use App\Models\Jobs;
use App\Models\CandidateJobApply;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CompanyProfileController extends Controller
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
            $userId = Auth::id();
            if (!$userId) {
                return $this->simpleReturn('error', 'User not authenticated', 401);
            }
            $records = CompanyProfile::where('user_id', $userId)->with(['city', 'state', 'country'])->first();
            if ($records) {
                $totalJobCount = Jobs::where('user_id', $userId)->count();
                $totalApplicantCount = CandidateJobApply::whereIn('job_id', function ($query) {
                    $query->select('id')
                        ->from('jobs')
                        ->where('user_id', Auth::user()->id);
                        // ->where('status', 'active')
                        // ->where('is_published', '1');
                })->count();

                $statusCounts = CandidateJobApply::whereIn('job_id', function ($query) use ($userId) {
                    $query->select('id')
                        ->from('jobs')
                        ->where('user_id', $userId);
                })
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');
    
               
                $records->total_job_count = $totalJobCount;
                $records->total_applicant_count = $totalApplicantCount;
                $records->status_counts = $statusCounts;
                
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
                'company_name' => 'required',
                'number_of_employee' => 'required',
                'company_type' => 'required',
                'address1' => 'required',
                // 'address2' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $ifExist = CompanyProfile::where('user_id', Auth::user()->id)->first();
            if ($ifExist) {
                return $this->simpleReturn('error', 'Profile already exist!');
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

            $companyProfile = new CompanyProfile();
            $companyProfile->uuid = Auth::user()->uuid;
            $companyProfile->user_id = Auth::user()->id;
            $companyProfile->company_name = $request->company_name;
            $companyProfile->number_of_employee = $request->number_of_employee;
            $companyProfile->company_type = $request->company_type;
            $companyProfile->address1 = $request->address1;
            $companyProfile->address2 = $request->address2;
            $companyProfile->city_id = $city_exist->id;
            $companyProfile->state_id = $state_exist->id;
            $companyProfile->country_id = $country_exist->id;
            $companyProfile->pincode = $request->pincode;
            $companyProfile->phone = Auth::user()->phone;
            $companyProfile->email = Auth::user()->email;
            $companyProfile->added_by = Auth::user()->id;
            if($companyProfile->save()) {
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
    public function show($id)
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
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            $rules = array(
                'company_name' => 'required',
                'number_of_employee' => 'required',
                'company_type' => 'required',
                'address1' => 'required',
                // 'address2' => 'required',
                'city_uuid' => 'required',
                'state_uuid' => 'required',
                'country_uuid' => 'required',
                'pincode' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CompanyProfile::where('user_id', Auth::user()->id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
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

            if ($record) {
                if (request()->hasFile('company_logo')) {
                    $file = $request->file('company_logo');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = public_path('uploads/company');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0775, true); // true for recursive directory creation
                    }
                    // Move the uploaded file to the specified directory
                    $file->move($path, $fileName);
                    $record->company_logo = $fileName;
                }

                $record->company_name = $request->company_name;
                $record->contact_person_name = $request->contact_person_name;
                $record->email = $request->email;
                $record->mobile = $request->mobile;
                $record->phone = $request->phone;
                $record->url = $request->url;
                $record->company_description = $request->company_description;
                $record->industry_type = $request->industry_type;
                $record->role = $request->role;
                $record->gst_number = $request->gst_number;
                $record->tan_number = $request->tan_number;
                $record->number_of_employee = $request->number_of_employee;
                $record->name_as_pan_number = $request->name_as_pan_number;
                $record->pan_number = $request->pan_number;
                $record->company_type = $request->company_type;
                $record->address1 = $request->address1;
                $record->address2 = $request->address2;
                $record->city_id = $city_exist->id;
                $record->state_id = $state_exist->id;
                $record->country_id = $country_exist->id;
                $record->pincode = $request->pincode;
                $record->updated_by = Auth::user()->id;
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
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyProfile  $id
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
    public function updateCompanyLogo(Request $request)
    {
        // try {
            $rules = array(
                'company_logo' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CompanyProfile::where('uuid', Auth::user()->uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                if (request()->hasFile('company_logo')) {
                    $file = $request->file('company_logo');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = public_path('uploads/company');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0775, true); // true for recursive directory creation
                    }

                    // delete existing file
                    if($record->company_logo) {
                        $deletefilePath = public_path('uploads/company/'.$record->company_logo);
                        if (file_exists($deletefilePath)) {
                            unlink($deletefilePath);
                        }
                    }

                    $file->move($path, $fileName);
                    $record->company_logo = $fileName;

                    $record->save();
                    if($record){
                        return $this->simpleReturn('success', 'File Updated successfully');
                    }
                } else {
                    return $this->simpleReturn('error', 'Image not uploaded', 404);
                }

            }
            return $this->simpleReturn('error', 'Error in updation');
        // } catch (\Exception $exception){
        //     Log::error($exception);
        //     return $this->simpleReturn('error', 'Something went wrong, please contact support');
        // }    
    }
}