<?php

namespace App\Http\Controllers;

use App\Models\NotApplicableComments;
use App\Models\Question;
use App\Models\Standard;
use App\Models\Survey;
use App\Models\DocumentAnswers;
use App\Models\Survey_Answer;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use DB;
class SurveyAnswerController extends Controller
{

    public $survey;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Survey_Answer::all();
        if ($data->isNotEmpty()) {
            return good_response('All Survey Answers Retrieved', $data);
        }
        return bad_response('Failed to Retrieved All Survey Answers');
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
            'survey_id' => 'required|integer|exists:surveys,id',
            'standard_id' => 'required|integer|exists:standards,id',
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'required|string',
            'comment' => 'sometimes|string|nullable',
        ];
        $parameters = $request->only([
            'survey_id',
            'standard_id',
            'answer',
            'question_id',
            'comment'
        ]);
        $validator = Validator::make($parameters, $rules);

        if (!in_array($parameters['answer'], DEFAULT_ANSWERS, true)) {
            return bad_response('Invalid Answer');
        }

        if ($validator->fails()) {
            return bad_response($validator->messages());
        }

        $question = Question::find($parameters['question_id']);

        if ($question->standard_id !== (int)$parameters['standard_id']) {
            return bad_response('Invalid Survey ID');
        }

        if (!Auth::user()->NGO) {
            return bad_response('User is not a NGO User');
        }
        $user = Auth::user()->NGO;
        $survey = $user->surveys()->find($parameters['survey_id']);
        if (!$survey) {
            return bad_response('Check Your Input');
        }
        $answer = Survey_Answer::make($parameters);
        $save_flag = false;

        if ($question->is_not_applicable === 1) {
            if ($answer->answer === DEFAULT_ANSWERS[3] && $request->has('comment')) {
                $save_flag = $answer->save();
                NotApplicableComments::create([
                    'answer_id' => $answer->id,
                    'comment' => $parameters['comment']
                ]);
            }
        }

        if ($answer) {
            if (!$save_flag) {
                $answer->save();
            }
            return good_response('Answer Submitted Successfully', $answer);
        }
        return bad_response('Unable to Submit Answers');
    }

    /**
     * Display the specified resource.
     *
     * @param Survey_Answer $answer
     * @return Response
     */
    public function show(Survey_Answer $answer)
    {

        return good_response('Answer retrieved successfully', $answer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Survey_Answer $answer
     * @return Response
     */
    public function update(Request $request, Survey_Answer $answer)
    {
        //TODO Sync with Store
        $rules = [
            'survey_id' => 'required|integer|exists:surveys,id',
            'standard_id' => 'required|integer|exists:standards,id',
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'required|string',
        ];
        $parameters = $request->only([
            'survey_id',
            'standard_id',
            'question_id',
            'answer',
        ]);

        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        $question = Question::find($parameters['question_id']);
        if ($question->standard_id !== (int)$parameters['standard_id']) {
            return bad_response('Invalid Survey ID');
        }
        if (!Auth::user()->NGO) {
            return bad_response('User is not a NGO User');
        }
        if ($answer->update($parameters)) {
            return good_response('Answer Updated', $answer);
        }
        return bad_response('Unable to update Answers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Survey_Answer $answer
     * @return Response
     */
    public function destroy(Survey_Answer $answer)
    {
        //
    }

    public function getAllAnswersOfSurvey(Request $request, $id)
    {
        try {
            Survey::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return bad_response('Survey Not Found');
        }
        $data = Survey_Answer::where('survey_id', '=', $id)->get();
        if ($data->isEmpty()) {
            return bad_response('NO Answers');
        }
        return good_response('Answers retrieved Successful', $data);
    }

    public function bulkCreate(Request $request)
    {
        return Survey_Answer::bulkCreate($request);
    }

    public function answerCount(Survey $survey, Standard $standard)
    {
        //TODO Check why the not_applicable_comments is appended
        $answers = $survey->answers()->where('standard_id', '=', $standard->id)->get()->count();
        return good_response('percentage', $answers);
    }

    public function currentAnswers()
    {
        $user = Auth::user();
        $survey = $user->NGO->surveys()->orderByDesc('surveys.date_started')->first();
        $data = [];
        if (!$survey) {
            return bad_response('Empty survey');
        }
        foreach ($survey->document_answers as $answer) {
            $data[] = $answer->get();
        }
        return $data;
    }

    //El-Shami
    public function submitAnswer(Request $request, Standard $standard)
    {
        /*
            id: 1, // id question
            answer: "notMet", // can be vide string ("") or Met or notMet or partiallyMet or notApplicable
            explanationContent: "bbbb",
         * */
        $answers = $request->json('data');
        if (empty($answers)) {
            return bad_response('Empty Body');
        }
        $this->survey = getCurrentNGOSurvey();
        $flag = false;
        foreach ($answers as $answer) {
            if (Survey_Answer::validateAnswer($this->survey->id, $standard->id, $answer['id'], $answer['answer'])) {
                $answerObject = Survey_Answer::firstOrNew(
                    ['survey_id' =>  $this->survey->id,
                     'standard_id' => $standard->id,
                     'question_id' => $answer['id'] ]);
                $answerObject->answer=$answer['answer'];
                $answerObject->save();
                $flag = true;
                $std_status= $this->survey->standards_status->where('standard_id','=',$standard->id)->first();
                   $std_status->update(
                       ['status'=>"Progress"]
                   );
                if ($answerObject) {
                    $flag = true;
                    if(empty($answer['explanationContent']))
                    {
                        $answer['explanationContent']=null;
                    }
                    $commentObject = $answerObject->generateNotApplicableComment($answer['explanationContent']);
                    if ($commentObject) {
                        $flag = true;
                    }
                }
            }
        }
        if ($flag) {
            return good_response('Submitted', $flag);
        }
        return bad_response($flag);
    }
      /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function survey_scores($id)
    {
      try{

        // get survey questions with the following standards infos
        $survey_answers = Survey_Answer::join('standards','standards.id','survey_answers.standard_id')
        ->where('survey_id',$id)->orderBy('standard_id')
        ->get(['survey_answers.standard_id','survey_answers.question_id','survey_answers.answer','standards.description','standards.logo','standards.title']);

        // create survey_answer model to calculate total scores
        $survey_score= new Survey_Answer;
        return $survey_score->calculate_scores($survey_answers);
       }
      catch(\Exception $e)
      {
        return bad_response('survey id is wrong');
      }
    }
    
    public function survey_standards_scores($id)
    {
          // get all answers with the corresponding standards details 
        $survey_answers = Survey_Answer::join('questions','survey_answers.question_id','questions.id')
         ->join('standards','standards.id','survey_answers.standard_id')
         ->where('survey_id',$id)->orderBy('standard_id')
         ->get(['survey_answers.standard_id','survey_answers.question_id',
                'survey_answers.answer',
                'standards.title as standard_title',
                'questions.title as question_title','questions.improvement','standards.capacity_building']);
  
        $data= new Survey_Answer;
         return $data->standards_scores($survey_answers);
    }

    public function check_survey_status(Request $request, Standard $standard)
    {
        $status='';
        $this->submitAnswer($request,$standard);
        $questions_number =Question::count();
        $answers_number=Survey_Answer::where("survey_id",$this->survey->id)->count();
        if($questions_number==$answers_number)
        {
            Survey::where("id",$this->survey->id)->update(["status"=>"completed"]);
            $status='completed';
        }
        if($answers_number>0)
        {
            Survey::where("id",$this->survey->id)->update(["status"=>"inprogress"]);
            $status='inprogress';
        }
        return good_response($status,true);
    }
}
