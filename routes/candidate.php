<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Candidate\CandidateSkillsController;
use App\Http\Controllers\Candidate\CandidateItSkillsController;
use App\Http\Controllers\Candidate\CandidateEmploymentController;
use App\Http\Controllers\Candidate\CandidateProjectsController;
use App\Http\Controllers\Candidate\CandidateController;
use App\Http\Controllers\Candidate\CoreDataController;
use App\Http\Controllers\Candidate\CandidateJobPreferenceController;
use App\Http\Controllers\Candidate\CandidateEducationsController;
use App\Http\Controllers\Candidate\CandidateBasicDetailsController;
use App\Http\Controllers\Candidate\CandidateCertificationsController;
use App\Http\Controllers\Candidate\CandidateJobSavedController;
use App\Http\Controllers\Candidate\CandidateJobApplyController;
use App\Http\Controllers\Candidate\CompanyInfoController;
use App\Http\Controllers\Candidate\JobsController;
use App\Http\Controllers\Candidate\CandidateJobSearchHistoryController;
use App\Http\Controllers\Candidate\CandidateSubscriptionsController;
use App\Http\Controllers\Candidate\CandidateLanguagesController;

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
  Route::resource('candidate-basic-details', CandidateBasicDetailsController::class);
  Route::post('candidate-basic-details-update', [CandidateBasicDetailsController::class, 'updateProfile']);
  Route::post('candidate-basic-details-resume', [CandidateBasicDetailsController::class, 'updateResume']);
  Route::post('candidate-basic-details-profile', [CandidateBasicDetailsController::class, 'updateProfilePic']);
  Route::resource('candidate-skills', CandidateSkillsController::class);
  Route::resource('candidate-it-skills', CandidateItSkillsController::class);
  Route::resource('candidate-employment', CandidateEmploymentController::class);
  Route::resource('candidate-educations', CandidateEducationsController::class);
  Route::resource('candidate-projects', CandidateProjectsController::class);
  Route::resource('candidate-job-preference', CandidateJobPreferenceController::class);
  Route::resource('candidate-certifications', CandidateCertificationsController::class);
  Route::resource('candidate-languages', CandidateLanguagesController::class);

  Route::resource('candidate-job-saved', CandidateJobSavedController::class);
  Route::resource('candidate-job-apply', CandidateJobApplyController::class);

  Route::get('fetch-candidate', [CandidateController::class, 'fetchCandidateDetails']);
  Route::get('fetch-candidate-career-profile', [CandidateController::class, 'fetchCandidateCareerProfile']);
  Route::get('fetch-core-locations', [CoreDataController::class, 'fetchCoreLocation']);
  Route::get('fetch-core-skills', [CoreDataController::class, 'fetchCoreSkills']);
  Route::get('fetch-core-titles', [CoreDataController::class, 'fetchCoreTitle']);
  Route::get('fetch-core-educations', [CoreDataController::class, 'fetchCoreEducations']);
  Route::get('fetch-core-education-course/{job_education_id}', [CoreDataController::class, 'fetchCoreEducationCourse']);
  Route::get('fetch-core-education-course-specialty/{job_education_course_uuid}', [CoreDataController::class, 'fetchCoreEducationCourseSpecialty']);
  Route::get('fetch-core-company', [CoreDataController::class, 'fetchCoreCompany']);
  Route::get('fetch-core-city/{state_id}', [CoreDataController::class, 'fetchCoreCity']);
  Route::get('fetch-core-state/{country_id}', [CoreDataController::class, 'fetchCoreState']);
  Route::get('fetch-core-country', [CoreDataController::class, 'fetchCoreCountry']);

  Route::get('company-list', [CompanyInfoController::class, 'getCompanyList']);
  Route::get('company-profile/{uuid}', [CompanyInfoController::class, 'getCompanyProfile']);
  Route::post('company-review-save', [CompanyInfoController::class, 'saveReviews']);

  Route::get('fetch-company-list', [CompanyInfoController::class, 'fetchCompanyList']);
  Route::get('fetch-job-list/{company_uuid}', [CompanyInfoController::class, 'fetchJobListByCompanyUuid']);

  Route::resource('jobs', JobsController::class);
  Route::get('fetch-recommended-jobs', [CandidateController::class, 'fetchRecommendedJobs']);
  Route::resource('candidate-search-history', CandidateJobSearchHistoryController::class);
  Route::resource('candidate-subscriptions', CandidateSubscriptionsController::class);
});