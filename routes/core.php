<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Core\CoreJobTitleController;
use App\Http\Controllers\Core\CoreJobCityController;
use App\Http\Controllers\Core\CoreJobEducationsController;
use App\Http\Controllers\Core\CoreJobEducationCourseController;
use App\Http\Controllers\Core\CoreJobEducationCourseSpecialtyController;
use App\Http\Controllers\Core\CoreJobSkillsController;
use App\Http\Controllers\Core\CoreJobDepartmentsController;
use App\Http\Controllers\Core\CoreJobCompanyController;
use App\Http\Controllers\Core\CoreIndustryController;
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
