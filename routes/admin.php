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

