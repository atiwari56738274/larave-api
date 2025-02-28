<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\CandidateTestimonial;

class CandidateTestimonialController extends Controller
{
    /**
     * Constructor with middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of testimonials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $records = CandidateTestimonial::all();
            if ($records->count() > 0) {
                return response()->json(['status' => 'success', 'data' => $records]);
            }
            return response()->json(['status' => 'error', 'message' => 'No records found'], 404);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please contact support'], 500);
        }
    }

    /**
     * Store a newly created testimonial.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            Log::info('Received request to store testimonial', $request->all());
            $rules = [
                'name' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'feedback' => 'required|string',
                'photo_url' => 'nullable|url',
                'review_rating' => 'required|integer|min:1|max:5',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()]);
            }

            $record = new CandidateTestimonial();
            $record->uuid = Str::uuid();
            $record->fill($request->only(['name', 'designation', 'feedback', 'photo_url', 'review_rating']));
            
            if ($record->save()) {
                return response()->json(['status' => 'success', 'message' => 'Testimonial added successfully']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in saving testimonial'], 422);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please contact support'], 500);
        }
    }

    /**
     * Update the specified testimonial.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uuid)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'feedback' => 'required|string',
                'photo_url' => 'nullable|url',
                'review_rating' => 'required|integer|min:1|max:5',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()]);
            }

            $record = CandidateTestimonial::where('uuid', $uuid)->first();
            if (!$record) {
                return response()->json(['status' => 'error', 'message' => 'Testimonial not found'], 404);
            }

            $record->fill($request->only(['name', 'designation', 'feedback', 'photo_url', 'review_rating']));

            if ($record->save()) {
                return response()->json(['status' => 'success', 'message' => 'Testimonial updated successfully']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in updating testimonial'], 422);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please contact support'], 500);
        }
    }

    /**
     * Remove the specified testimonial.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($uuid)
    {
        try {
            $record = CandidateTestimonial::where('uuid', $uuid)->first();
            if (!$record) {
                return response()->json(['status' => 'error', 'message' => 'Testimonial not found'], 404);
            }

            if ($record->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Testimonial deleted successfully']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in deleting testimonial'], 422);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please contact support'], 500);
        }
    }
}
