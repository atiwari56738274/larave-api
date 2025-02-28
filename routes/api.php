<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\GlobalAddressController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\JobSettingsController;
use App\Http\Controllers\Api\JobsController;
use App\Http\Controllers\Api\DashboardController;
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
Route::resource('jobs', JobsController::class);

Route::get('global-address-countries', [GlobalAddressController::class, 'getCountryData']);
Route::get('global-address-states/{uuid}', [GlobalAddressController::class, 'getStateData']);
Route::get('global-address-cities/{uuid}', [GlobalAddressController::class, 'getCityData']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login-google', [AuthController::class, 'loginGoogle']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
      Route::post('change-password', [AuthController::class, 'resetPassword']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::resource('employer-company-profile', CompanyProfileController::class);
  Route::resource('job-settings', JobSettingsController::class);
  // Route::resource('jobs', JobsController::class);
});


Route::get('fetch-company-list', [DashboardController::class, 'fetchCompanyList']);
Route::get('fetch-company-job-list/{company_uuid}', [DashboardController::class, 'fetchJobListByCompanyUuid']);
Route::get('fetch-company-review-list/{company_uuid}', [DashboardController::class, 'fetchReviewListByCompanyUuid']);
Route::get('fetch-company-details/{company_uuid}', [DashboardController::class, 'fetchDetailsByCompanyUuid']);
Route::get('most-search-jobs-department', [DashboardController::class, 'fetchMostSearchJobsDepartment']);
Route::get('popular-job-roles', [DashboardController::class, 'fetchPopularJobRoles']);
Route::get('most-active-jobs-company', [DashboardController::class, 'fetchMostActiveJobsCompany']);
Route::get('most-active-recent-jobs', [DashboardController::class, 'fetchMostActiveRecentJobs']);
Route::get('get-title-skill-company-list', [DashboardController::class, 'getTitleSkillCompanyList']);
Route::get('product-reviews-list', [DashboardController::class, 'getproductReviewList']);
Route::get('fetch-jobs-list', [DashboardController::class, 'fetchJobSearch']);
Route::get('jobs-list-by-search-history/{uuid}', [DashboardController::class, 'fetchJobSearchBySearchHistory']);
Route::get('jobs-by-category/{department_uuid}', [DashboardController::class, 'fetchJobsByCategory']);
Route::get('jobs-by-roles/{job_title_uuid}', [DashboardController::class, 'fetchJobsByRoles']);
Route::get('jobs-by-skills/{job_uuid}', [DashboardController::class, 'fetchRelatedJobsBySkills']);
Route::get('fetch-core-location', [DashboardController::class, 'fetchCoreLocations']);


Route::get('fetch-subscription-list/{type}', [DashboardController::class, 'fetchSubscriptionList']);
