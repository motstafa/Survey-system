<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use App\Models\Survey;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class SurveyController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Survey::class, 'survey');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Survey::all();
        if ($data->isNotEmpty()) {
            return good_response("Surveys Retrieved Successfully", $data);
        }
        return bad_response("Surveys Can't Be Retrieved ");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $rules = [
            'name_of_org' => "required|string",
            'status' => "required|string",
            'date_completed' => "required|date",
            'date_started' => "required|date",
            'expiry_date' => "required|date",
        ];
        $parameters = $request->only([
            'name_of_org',
            'status',
            'date_completed',
            'date_started',
            'expiry_date',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        if (!Auth::user()->NGO()) {
            return bad_response('User is not a ngo User');
        }
        $parameters['ngo_id'] = Auth::user()->NGO()->first()->id;
        $survey = Survey::create($parameters);
        if ($survey) {
            return good_response('Survey Created Successfully', $survey);
        }
        return bad_response('Cannot create Survey');
    }

    /**
     * Display the specified resource.
     *
     * @param Survey $survey
     * @return Response
     */
    public function show(Survey $survey)
    {
        $base_user = Auth::user();
        $settings = get_current_user_settings();
        $standards_and_questions = [];
        if ($settings->questions_set === SETTINGS[1]) {
            $stds = Standard::whereCountryId($base_user->country_id)->get();
            foreach ($stds as $std) {
                $standards_and_questions[str($std->id)->toString()] = $std->append('questions')->only(['id', 'title', 'questions']);
            }
        }
        if ($settings->questions_set === SETTINGS[0]) {
            $stds = Standard::whereCountryId(1)->get();
            foreach ($stds as $std) {
                $standards_and_questions[str($std->id)->toString()] = $std->append('questions')->only(['id', 'title', 'questions']);
            }
        }
        if ($standards_and_questions) {
            $data = ['survey' => $survey, 'standards and questions' => $standards_and_questions];
            return good_response("Survey retrieved successfully", $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function update(Request $request, Survey $survey)
    {
        $rules = [
            'name_of_org' => "string",
            'status' => "string",
            'date_completed' => "date",
            'date_started' => "date",
            'expiry_date' => "date",
        ];
        $parameters = $request->only([
            'name_of_org',
            'status',
            'date_completed',
            'date_started',
            'expiry_date',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        if ($survey->update($parameters)) {
            return good_response('Survey Updated Successfully', $survey);
        }
        return bad_response('Cannot Update Survey');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Survey $survey
     * @return Response
     */
    public function destroy(Survey $survey)
    {
        if ($survey->delete()) {
            return good_response("Survey Deleted Successful", $survey);
        }
        return bad_response('Cannot Delete Survey');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function get_survey_status($id)
    {
      try{
         $survey = Survey::find($id)->with('answers')->first();
         if(isset($survey->answers))
         {
           $survey->status="inprogress";
           $survey->save();
         }
         return good_response('survey found',$survey->status);
      }
      catch(\Exception $e)
      {
        return bad_response('survey id is wrong');
      }
    }


  

}
