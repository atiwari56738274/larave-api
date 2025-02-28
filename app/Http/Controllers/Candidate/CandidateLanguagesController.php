<?php

namespace App\Http\Controllers\Candidate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CandidateLanguages;
use App\Models\CoreJobSkills;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidateLanguagesController extends Controller
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
    public function index()
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
                'language' => 'required',
                'can_read' => 'required',
                'can_write' => 'required',
                'can_speak' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = new CandidateLanguages();
            $record->uuid = (string) Str::uuid();
            $record->user_id = Auth::user()->id;
            $record->language = $request->language;
            $record->can_read = $request->can_read;
            $record->can_write = $request->can_write;
            $record->can_speak = $request->can_speak;
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
     * @param  \App\CandidateLanguages  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CandidateLanguages  $id
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
     * @param  \App\CandidateLanguages  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        try {
            $rules = array(
                'language' => 'required',
                'can_read' => 'required',
                'can_write' => 'required',
                'can_speak' => 'required'
            );
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }
    
            $record = CandidateLanguages::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('Record not found', 404);
            }
    
            $record->language = $request->language;
            $record->can_read = $request->can_read;
            $record->can_write = $request->can_write;
            $record->can_speak = $request->can_speak;    
          
            if ($record->save()) {
                return $this->simpleReturn('success', 'Updated successfully');
            }
    
            return $this->simpleReturn('error', 'Error in updation');
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->simpleReturn('error', 'Something went wrong, please contact support');
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CandidateLanguages  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = CandidateLanguages::where('uuid', $uuid)->first();
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