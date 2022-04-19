<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CFPController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DocumentAnswersController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NationalExpertController;
use App\Http\Controllers\NGOController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\SurveyAnswerController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\reportGenerator;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Questions
Route::apiResource('/questions', QuestionController::class)->middleware('auth:sanctum');
Route::get('/questions/selfAssessment/{standard}', [QuestionController::class, 'questionsAndAnswersOfStandard']);//->middleware('auth:sanctum');

//National Expert
Route::apiResource('/users/nationalExpert', NationalExpertController::class)->except('update');
Route::post('/users/nationalExpert/{nationalExpert}', [NationalExpertController::class, 'update']);
Route::get('/users/nationalExpert/{nationalExpert}/activate', [NationalExpertController::class, 'activate']);

//CFP
Route::apiResource('/users/CFP', CFPController::class)->except('update');
Route::post('/users/CFP/{CFP}', [CFPController::class, 'update']);
Route::get('/users/CFP/{CFP}/activate', [CFPController::class, 'activate']);

//NGO
Route::apiResource('/users/NGO', NGOController::class)->except('update');
Route::post('/users/NGO/{NGO}', [NGOController::class, 'update']);
Route::get('/users/NGO/{NGO}/activate', [NGOController::class, 'activate']);

//Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/ngo', [AuthController::class, 'login_ngo']);
Route::post('/login/cfp', [AuthController::class, 'login_cfp']);
Route::post('/login/nationalExpert', [AuthController::class, 'login_national_expert']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
    if (Auth::check()) {
        return good_response('You can Access this ROUTE', [
            'Auth::check()' => Auth::check(),
            'Auth::user()' => Auth::user(),
            'Auth::authenticate()' => Auth::authenticate(),
        ]);
    }
    return bad_response('PROTECTED ROUTE');
});

//Standards
Route::post('/standards/{standard}', [StandardController::class, 'update'])->middleware('auth:sanctum');
Route::apiResource('/standards', StandardController::class)->middleware('auth:sanctum');
Route::get('/standards/{standard}/questions', [StandardController::class, 'questions'])->middleware('auth:sanctum');
Route::get('/standards/selfAssessment/standards', [StandardController::class, 'getStandards'])->middleware('auth:sanctum');

//Surveys
//Route::apiResource('/surveys', SurveyController::class)->only(['store', 'update', 'destroy'])->middleware('auth:sanctum');
//Route::apiResource('/surveys', SurveyController::class)->except(['store', 'update', 'destroy']);
Route::apiResource('/surveys', SurveyController::class)->middleware('auth:sanctum');

//Answers
Route::apiResource('/answers', SurveyAnswerController::class);//->middleware('auth:sanctum');
Route::get('/answers/getAnswers/{id}', [SurveyAnswerController::class, 'getAllAnswersOfSurvey'])->middleware('auth:sanctum');
Route::get('/answers/bulk/submit', [SurveyAnswerController::class, 'bulkCreate'])->middleware('auth:sanctum');
Route::get('/answers/{survey}/count/{standard}', [SurveyAnswerController::class, 'answerCount'])->middleware('auth:sanctum');
Route::post('/answers/selfAssessment/{standard}', [SurveyAnswerController::class, 'submitAnswer'])->middleware('auth:sanctum');
Route::get('/protected-routes', function () {
    $collection = Route::getRoutes();
    $routes = [];
    foreach ($collection as $route) {
        if (in_array('auth:sanctum', $route->middleware(), true)) {
            $routes[] = $route->uri();
        }
    }
    return good_response('List Of Protected Routes', $routes);
})->middleware('auth:sanctum');

Route::apiResource('/roles', RoleController::class)->middleware('auth:sanctum');
Route::apiResource('/country', CountryController::class)->middleware('auth:sanctum');
Route::get('/country/standards/{country}', [CountryController::class, 'getStandards'])->middleware('auth:sanctum');
Route::apiResource('/documents', DocumentController::class)->middleware('auth:sanctum');
Route::apiResource('/documentAnswers', DocumentAnswersController::class)->except('update');//->middleware('auth:sanctum');
Route::post('/documentAnswers/{documentAnswer}', [DocumentAnswersController::class, 'update'])->middleware('auth:sanctum');

Route::get('/questions/country/{country}', [QuestionController::class, 'questionOfCountry'])->middleware('auth:sanctum');
Route::get('/country/test/{country}', [CountryController::class, 'test'])->middleware('auth:sanctum');
Route::get('/test', [SurveyAnswerController::class, 'currentAnswers'])->middleware('auth:sanctum');


// mostafa
//Route::middleware('auth:sanctum')->group(function () {
Route::get('survey/status/{id}',[SurveyController::class,'get_survey_status']);
Route::get('survey/scores/{id}',[SurveyAnswerController::class,'survey_scores']);
Route::get('survey/standards/scores/{id}',[SurveyAnswerController::class,'survey_standards_scores']);
Route::get('hakonaMatata/{id}',[reportGenerator::class,'viewReport']);
Route::get('downloadCSV/{id}',[reportGenerator::class,'viewCSV']);
Route::post('addLogos',[reportGenerator::class,'addLogos']);

Route::post('survey/completed/{standard}',[SurveyAnswerController::class,'check_survey_status']);

Route::get('survey/standards/status',[StandardController::class,'getStandards_status']);

//});