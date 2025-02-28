<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;

use Validator;
use Illuminate\Support\Facades\Log;

class GlobalAddressController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCountryData()
    {
        try {
            $records = Countries::get();
            if(count($records) > 0) {
                return $this->simpleReturn('success', $records);
            }
            return $this->simpleReturn('error', 'Records not found');
        } catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['error'=> $exception]);
        }   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStateData($uuid)
    {
        try {
            // First, check if the country exists by UUID
            $country = Countries::where('uuid', $uuid)->first();
    
            if (!$country) {
                return $this->simpleReturn('error', 'Country with the given UUID not found');
            }
            $records = States::where('country_id', $country->id)->get();
    
            if ($records->isEmpty()) {
                return $this->simpleReturn('error', 'No states found for this country');
            }
    
            return $this->simpleReturn('success', $records);
    
        } catch (\Exception $exception) {
            Log::error('Error in fetching states by UUID: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while fetching states', 'exception' => $exception->getMessage()]);
        }
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCityData($uuid)
    {
        try {
          
             $state = States::where('uuid', $uuid)->first();
    
             if (!$state) {
                 return $this->simpleReturn('error', 'State with the given UUID not found');
             }

            $records = Cities::where('state_id',$state->id)->get();
            if(count($records) > 0) {
                return $this->simpleReturn('success', $records);
            }
            return $this->simpleReturn('error', 'Records not found');
        } catch (\Exception $exception){
            Log::error($exception);
            return response()->json(['error'=> $exception]);
        }   
    }
    
}