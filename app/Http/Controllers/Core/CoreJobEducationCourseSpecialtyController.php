<?php

namespace App\Http\Controllers\Core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CoreJobEducationCourseSpecialty;
use App\Models\CoreJobEducationCourse;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoreJobEducationCourseSpecialtyController extends Controller
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
            $records = CoreJobEducationCourseSpecialty::with('core_job_education_course')->orderBy('title', 'ASC');
            if($request->keyword && $request->keyword != '') {
                $records->where('title', 'like', '%' . $request->keyword . '%');
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
                'job_education_course_uuid' => 'required',
                'title' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $CoreJobEducationCourseExist = CoreJobEducationCourse::where('uuid', $request->job_education_course_uuid)->first();
            if (!$CoreJobEducationCourseExist) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $record = new CoreJobEducationCourseSpecialty();
            $record->uuid = (string) Str::uuid();
            $record->job_education_course_id = $CoreJobEducationCourseExist->id;
            $record->title = $request->title;
            $record->status = $request->status;
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
     * @param  \App\CoreJobEducationCourseSpecialty  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        try{
        $coreJobEducationCourse = CoreJobEducationCourse::where('uuid',$uuid)->first();
        if(!$coreJobEducationCourse){
            return $this->simpleReturn('error','Specialization with this given UUID not found');
        }

        $records = CoreJobEducationCourseSpecialty::where('job_education_course_id', $coreJobEducationCourse->id)->get();
        if($records->isEmpty()){
            return $this->simpleReturn('error','No specialization found for this course');
        }

        return $this->simpleReturn('success',$records);
        }catch (\Exception $exception) {
            Log::error('Error in fetching specialization by UUID: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while fetching specialization', 'exception' => $exception->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CoreJobEducationCourseSpecialty  $id
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
     * @param  \App\CoreJobEducationCourseSpecialty  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$uuid)
    {
        try {
            $rules = array(
                'job_education_course_uuid' => 'required',
                'title' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = CoreJobEducationCourseSpecialty::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            $CoreJobEducationCourseExist = CoreJobEducationCourse::where('uuid', $request->job_education_course_uuid)->first();
            if (!$CoreJobEducationCourseExist) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                $record->job_education_course_id = $CoreJobEducationCourseExist->id;
                $record->title = $request->title;
                $record->status = $request->status;
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
     * @param  \App\CoreJobEducationCourseSpecialty  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = CoreJobEducationCourseSpecialty::where('uuid', $uuid)->first();
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