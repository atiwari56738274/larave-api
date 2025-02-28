<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Candidate;
use App\Models\ProductReviews;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateDataController extends Controller
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
            $perPageCount = $request->perPageCount ? $request->perPageCount : 15;
            $records = Candidate::where('user_type', 'candidate')->paginate($perPageCount);
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductReviews  $id
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        //    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        //   
    }


    /**
     * Update the specified resource from storage.
     *
     * @param  \App\Candidate  $uuid
     * @return \Illuminate\Http\Response
     */
    public function updateProfileVerifiedStatus(Request $request, $uuid)
    {
        try {
            $rules = array(
                'status' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = Candidate::where('user_type', 'candidate')->where('uuid', $uuid)->first();
            if($record){
                if(($record->profile_verified == "1" ) && ($request->status)) {
                    return $this->simpleReturn('error', 'User already verified', 422);
                }
                if(($record->profile_verified == "0" ) && (!$request->status == "false")) {
                    return $this->simpleReturn('error', 'User already unverified', 422);
                }
                $record->profile_verified = $request->status;
                if($record->save()){
                    return $this->simpleReturn('success', 'Successfully profile status updated');
                }
                return $this->simpleReturn('error', 'updation error', 422);
            }
            return $this->simpleReturn('error', 'Record not found', 404);
        } catch (\Exception $exception){
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support', 500);
        }  
    }
}