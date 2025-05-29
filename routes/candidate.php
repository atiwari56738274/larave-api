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
