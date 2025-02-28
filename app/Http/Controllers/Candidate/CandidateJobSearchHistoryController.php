<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CompanyProfile;
use App\Models\CoreJobCity;
use App\Models\CoreJobTitle;
use App\Models\CoreJobSkills;
use App\Models\CandidateJobSearchHistory;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateJobSearchHistoryController extends Controller
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

            $records = CandidateJobSearchHistory::where('user_id', $user_id);
        
            $records = $records->paginate($perPageCount)->map(function ($item) {

                $jobTitles = [];
                $jobSkills = [];
                $jobLocations = [];
                $jobCompany = [];
                $itemLocation = json_decode($item->job_locations, TRUE);
                $itemCompany = json_decode($item->job_company, TRUE);
                $itemSkills = json_decode($item->job_skills, TRUE);
                $itemTitles = json_decode($item->job_titles, TRUE);

                if ($itemCompany && count($itemCompany) > 0) {
                    $jobCompany = CompanyProfile::select('company_name')->whereIn('id', $itemCompany)->pluck('company_name')->toArray();
                }

                if ($itemLocation && count($itemLocation) > 0) {
                    $jobLocations = CoreJobCity::select('title')->whereIn('id', $itemLocation)->pluck('title')->toArray();
                }

                if ($itemTitles && count($itemTitles) > 0) {
                    $jobTitles = CoreJobTitle::select('title')->whereIn('id', $itemTitles)->pluck('title')->toArray();
                }

                if ($itemSkills && count($itemSkills) > 0) {
                    $jobSkills = CoreJobSkills::select('title')->whereIn('id', $itemSkills)->pluck('title')->toArray();
                }

                return [
                    'uuid' => $item->uuid,
                    'experience' => $item->experience,
                    'alert_enable' => $item->alert_enable,
                    'job_company' => $jobCompany,
                    'job_location' => $jobLocations,
                    'job_title' => $jobTitles,
                    'job_skill' => $jobSkills
                ];
            });

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
        //  
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CandidateJobSearchHistory  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateJobSearchHistory  $id
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
     * @param  \App\CandidateJobSearchHistory  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'alert_enable' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }
            $user_id = Auth::user()->id;
            $record = CandidateJobSearchHistory::where('uuid', $uuid)->where('user_id', $user_id)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->alert_enable = $request->alert_enable;
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
     * @param  \App\CandidateJobSearchHistory  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $user_id = Auth::user()->id;
            $record = CandidateJobSearchHistory::where('uuid', $uuid)->where('user_id', $user_id)->first();
            if($record){
                $record_delete = $record->delete();
                if($record_delete){
                    return $this->simpleReturn('success', 'Successfully deleted');
                }
                return $this->simpleReturn('error', 'deletion error', 422);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }  
    }
}