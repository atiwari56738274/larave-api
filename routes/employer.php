<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employer\JobsController;
use App\Http\Controllers\Employer\CompanyReviewsController;
use App\Http\Controllers\Employer\CandidateJobApplyController;
use App\Http\Controllers\Employer\ApplicantInterviewScheduleController;
use App\Http\Controllers\Employer\CandidateSearchController;
use App\Http\Controllers\Employer\EmployerCandidateSavedController;
use App\Http\Controllers\Employer\CompanyProfileController;
use App\Http\Controllers\Employer\EmployerSubscriptionsController;
use App\Http\Controllers\Employer\CompanyInterviewProcessController;
use App\Http\Controllers\Employer\CompanyMediaController;
use App\Http\Controllers\Employer\EmployerSubscriptionsPaymentController;

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function() {

  Route::resource('employer-company-profile', CompanyProfileController::class);
  Route::resource('company-interview-process', CompanyInterviewProcessController::class);
  Route::resource('company-media', CompanyMediaController::class);
  Route::post('company-media/{uuid}', [CompanyMediaController::class, 'update']);
  Route::post('employer-company-logo', [CompanyProfileController::class, 'updateCompanyLogo']);

  Route::resource('jobs', JobsController::class);
  Route::post('jobs/{status}/{uuid}', [JobsController::class, 'updateStatus']);
  Route::get('jobs/applicants/{job_uuid}', [JobsController::class, 'applicantsByJobUuid']);
  Route::post('job-hiring-tag/{job_uuid}', [JobsController::class, 'updateJobHiringTag']);

  Route::resource('company-reviews', CompanyReviewsController::class);
  Route::resource('candidate-job-apply', CandidateJobApplyController::class);
  Route::post('candidate-job-apply/{status}/{uuid}', [CandidateJobApplyController::class, 'updateApplicantStatus']);
  Route::get('candidate-details/{uuid}', [CandidateJobApplyController::class, 'getCandidateDetails']);


  Route::resource('applicant-interview-schedule', ApplicantInterviewScheduleController::class);
  Route::get('search-candidate', [CandidateSearchController::class, 'index']);
  Route::get('candidate-details/{uuid}', [CandidateSearchController::class, 'show']);


  Route::resource('employer-candidate-saved', EmployerCandidateSavedController::class);

  Route::post('employer-subscriptions-payment', [EmployerSubscriptionsPaymentController::class, 'store']);
  Route::get('employer-subscriptions-payment/{uuid}', [EmployerSubscriptionsPaymentController::class, 'checkRazorpayPaymentStatus']);

  Route::resource('employer-subscriptions', EmployerSubscriptionsController::class);
  Route::get('employer-subscriptions-history', [EmployerSubscriptionsController::class, 'employerSubscriptionHistory']);
  Route::post('employer-mannual-subscriptions/{uuid}', [EmployerSubscriptionsController::class, 'addEmployerMannualSubscriptions']);
});