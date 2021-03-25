<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/lti', [App\Http\Controllers\LtiController::class, 'ltiMessage']);
Route::get('/lti_check', [\App\Http\Controllers\LtiController::class, 'launchCheck']);
Route::get('/lti_redirect', [\App\Http\Controllers\LtiController::class, 'launchRedirect']);
Route::get('/app', [\App\Http\Controllers\AppController::class, 'getTool']);
Route::post('/app/config', [\App\Http\Controllers\AppController::class, 'postToolConfig']);
Route::get('/app/response', [\App\Http\Controllers\AppController::class, 'getToolResponse']);
Route::post('/app/resend_grade', [\App\Http\Controllers\AppController::class, 'postResendGrade']);
Route::get('/app/test_begin', [\App\Http\Controllers\AppController::class, 'getTestBegin']);
Route::get('/app/test_end', [\App\Http\Controllers\AppController::class, 'getTestEnd']);
