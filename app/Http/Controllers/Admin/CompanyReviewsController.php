<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyReviews;
use Illuminate\Http\Request;

class CompanyReviewsController extends Controller
{
    /**
     * Fetch all company reviews with optional filters.
     */
    public function __construct()
     {
         $this->middleware('auth');
    }


    public function index(Request $request)
    {
        try {
            $records = CompanyReviews::with('company_profile','candidate')->get();
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
     * Delete a company review by UUID.
     */
    public function destroy($uuid)
    {
       try{
        $record = CompanyReviews::where('uuid',$uuid)->first();
        if($record){
            $record_delete = $record->delete();
            if($record_delete){
                return $this->simpleReturn('success','Successfully deleted');
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