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
use App\Models\CompanyReviews;
use App\Models\CompanyProfile;
use App\Models\EmployerCandidateSaved;
use App\Models\Candidate;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmployerCandidateSavedController extends Controller
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

            $records = EmployerCandidateSaved::where('employer_id', $user_id)
                ->with(['candidate'])
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
        // try {
            $rules = array(
                'candidate_uuid' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $candidateValid = Candidate::where('uuid', $request->candidate_uuid)->first();
            if (!$candidateValid) {
                return $this->simpleReturn('error', 'Not valid candidate!', 404);
            }

            $isExist = EmployerCandidateSaved::where('employer_id', Auth::user()->id)->where('candidate_id', $candidateValid->id)->first();
            if ($isExist) {
                if($isExist->delete()) {
                    return $this->simpleReturn('success', 'Removed successfully');
                }
                return $this->simpleReturn('error', 'Error in insertion');
            }

            $record = new EmployerCandidateSaved();
            $record->uuid = (string) Str::uuid();
            $record->employer_id = Auth::user()->id;
            $record->candidate_id = $candidateValid->id;
            if($record->save()) {
                return $this->simpleReturn('success', 'Saved/Bookmarked successfully');
            }
            return $this->simpleReturn('error', 'Error in insertion');
        // } catch (\Exception $exception){
        //     Log::error($exception);
        //     return $this->simpleReturn('error', 'Something went wrong, please contact support');
        // } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyProfile  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid)
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
     * @param  \App\CompanyReviews  $id
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