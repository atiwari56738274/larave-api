<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductReviewsController;
use App\Http\Controllers\Admin\SubscriptionsController;
use App\Http\Controllers\Admin\CandidateTestimonialController;
use App\Http\Controllers\Admin\CompanyReviewsController;
use App\Http\Controllers\Admin\CandidateDataController;
use App\Http\Controllers\Admin\EmployerDataController;
use App\Http\Controllers\Admin\SubscriptionsMannualEmployerController;
use App\Http\Controllers\Admin\SubscriptionsTopupEmployerController;
use App\Http\Controllers\Admin\JobsController;

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
  Route::resource('jobs', JobsController::class);
  Route::resource('product-reviews', ProductReviewsController::class);
  Route::resource('subscriptions', SubscriptionsController::class);
  Route::put('subscriptions-details/{uuid}', [SubscriptionsController::class, 'updateSubscriptionDetails']);
  Route::get('subscriptions-details-employer', [SubscriptionsController::class, 'getEmployerSubscriptionHistory']);
  Route::resource('subscriptions-manuual-employer', SubscriptionsMannualEmployerController::class);
  Route::resource('subscriptions-topup-employer', SubscriptionsTopupEmployerController::class);
  Route::post('subscriptions-topup-employer/approved/{uuid}', [SubscriptionsTopupEmployerController::class, 'edit']);
  Route::resource('candidate-testimonials', CandidateTestimonialController::class);
  Route::get('company-reviews', [CompanyReviewsController::class, 'index']);
  Route::delete('company-reviews/{uuid}', [CompanyReviewsController::class, 'destroy']);

  Route::resource('candidate-data', CandidateDataController::class);
  Route::post('candidate-profile-verified/{uuid}', [CandidateDataController::class, 'updateProfileVerifiedStatus']);
  Route::resource('employer-data', EmployerDataController::class);
  Route::post('employer-data-verified/{uuid}', [EmployerDataController::class, 'updateEmployerVerifiedStatus']);
  Route::post('employer-profile-verified/{uuid}', [EmployerDataController::class, 'updateProfileVerifiedStatus']);
});
 
