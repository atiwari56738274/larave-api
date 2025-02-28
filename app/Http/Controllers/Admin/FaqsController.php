<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ProductReviews;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductReviewsController extends Controller
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
            $records = ProductReviews::get();
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
                'name' => 'required',
                'designation' => 'required',
                'company_name' => 'required',
                'review_rating' => 'required',
                'review_description' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $fileName = null;

            if (request()->hasFile('image')) {
                $file = request()->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('/uploads/product_reviews/', $fileNameResume);
            }

            $record = new ProductReviews();
            $record->uuid = (string) Str::uuid();
            $record->name = $request->name;
            $record->designation = $request->designation;
            $record->company_name = $request->company_name;
            $record->review_rating = $request->review_rating;
            $record->review_description = $request->review_description;
            $record->status = $request->status;
            $record->image = $fileName;
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
        try {
            $rules = array(
                'name' => 'required',
                'designation' => 'required',
                'company_name' => 'required',
                'review_rating' => 'required',
                'review_description' => 'required',
                'status' => 'required|in:active,inactive'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->simpleReturn('error', $validator->errors());
            }

            $record = ProductReviews::where('uuid', $uuid)->first();
            if (!$record) {
                return $this->simpleReturn('error', 'Details not exist!', 404);
            }

            if ($record) {
                if (request()->hasFile('resume')) {
                    $file = request()->file('resume');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('/uploads/product_reviews/', $fileNameResume);
                    $record->image = $fileName;
                }
                $record->name = $request->name;
                $record->designation = $request->designation;
                $record->company_name = $request->company_name;
                $record->review_rating = $request->review_rating;
                $record->review_description = $request->review_description;
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
     * @param  \App\ProductReviews  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $record = ProductReviews::where('uuid', $uuid)->first();
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