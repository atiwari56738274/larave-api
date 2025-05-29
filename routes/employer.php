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
