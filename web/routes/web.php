<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\LtiController;
use App\Http\Middleware\CheckLtiLogin;
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

Route::post('/lti', [LtiController::class, 'ltiMessage']);
Route::get('/lti/help', [LtiController::class, 'ltiHelp']);
Route::get('/lti/jwks', [LtiController::class, 'getJWKS']);

Route::middleware([CheckLtiLogin::class])->group(function () {
    Route::get('/app/{uuid}', [AppController::class, 'getTool']);
    Route::post('/app/{uuid}/config', [AppController::class, 'postToolConfig']);
    Route::get('/app/{uuid}/response', [AppController::class, 'getToolResponse']);
    Route::post('/app/{uuid}/resend_grade', [AppController::class, 'postResendGrade']);
    Route::get('/app/{uuid}/test_begin', [AppController::class, 'getTestBegin']);
    Route::get('/app/{uuid}/test_end', [AppController::class, 'getTestEnd']);
    Route::get('/app/{uuid}/exportCSV', [AppController::class, 'getCsvExport']);
});

// Test survey which behaves similarly to a Qualtrics survey, for testing without Qualtrics
Route::get('/test/survey', function() { return view('dev/test_survey'); });

if (env('DEV_MODE_ENABLE')) {
    Route::get('/dev/launch', [AppController::class, 'getDevModeLaunch']);
    Route::post('/dev/launch', [AppController::class, 'postDevModeLaunch']);
}

