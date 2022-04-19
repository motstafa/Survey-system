<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use App\Models\Survey_Answer;
use App\Models\Question;
use App\Models\Document;
use App\Models\Survey;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Validator;

class StandardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Standard::all()->groupBy('country');
        if ($data->isNotEmpty()) {
            return good_response("Standards Retrieved Successfully", $data);
        }
        return bad_response("Standards Can't Be Retrieved ");
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
            'title' => 'required|string',
            'logo' => 'required|mimes:jpeg,png',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'title_ar' => 'required|string',
        ];
        $parameters = $request->only([
            'title',
            'logo',
            'description',
            'description_ar',
            'title_ar',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        $parameters['active'] = 1;
        $standard = Standard::create($parameters);
        if ($standard) {
            $logo = $request->file('logo');
            $filename = $parameters['title'] . "_" . Carbon::now()->timestamp . "." . $logo->extension();
            $logo->storeAs("Public/Standards_Logos/", $filename);
            $standard->update([
                'logo' => 'Standards_Logos/' . $filename]);


            return good_response("Standard Added Standard", $standard);
        }
        return bad_response("Couldn't add Standard");
    }

    /**
     * Display the specified resource.
     *
     * @param Standard $standard
     * @return Response
     */

    public function show(Standard $standard)
    {
        return good_response('Standard Retrieved Successfully', $standard);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Standard $standard
     * @return Response
     */
    public function update(Request $request, Standard $standard)
    {
        $rules = [
            'title' => 'string',
            'logo' => 'mimes:jpeg,png',
            'description' => 'string',
            'description_ar' => 'string',
            'title_ar' => 'string',
        ];
        $parameters = $request->only([
            'title',
            'logo',
            'description',
            'description_ar',
            'title_ar',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        if ($request->has('logo')) {
            File::delete(Storage::path('public\\' . $standard->logo));
            $logo = $request->file('logo');
            $filename = $parameters['title'] . "_" . Carbon::now()->timestamp . "." . $logo->extension();
            $logo->storeAs("Public/Standards_Logos/", $filename);
            $parameters['logo'] = 'Standards_Logos/' . $filename;
        }
        if ($standard->update($parameters)) {
            return good_response("Standard Updated Successfully", $standard);
        }
        return bad_response("Unable to Update Standard");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Standard $standard
     * @return Response
     */
    public function destroy(Standard $standard)
    {
        if ($standard->delete()){
            return good_response('Standard Deleted Successfully',$standard);
        }
        return bad_response('Unable to Delete Standard');
    }

    public function questions(Standard $standard)
    {
        $user = Auth::user();
        if (check_roles($user, ['NGO'])) {
            $survey = $user->NGO->surveys()->orderByDesc('surveys.date_started')->first();
            $questions = $standard->questions;
            $data = ['survey_id' => $survey->id, 'questions' => $questions];
            if ($questions->isNotEmpty()) {
                return good_response("Questions Retrieved", $data);
            }
        }

        return bad_response('Unable to Retrieved Questions');
    }

    public function getStandards()
    {
        //$user = Auth::user();
        //$settings = get_current_user_settings();
        $data = [];
        $stds = Standard::get();
        foreach ($stds as $std) {
            $data[] = $std->frontend();
             }

        return $data;
    }

    public function getStandards_status()
    {
        //$user = Auth::user();
        $settings = getCurrentNGOSurvey();
        $all_standards=array();
        $temp_standards=array();
        // this query returns all the standards id that have a least 1 answer in survey_answers table
        $standards = Standard::get(['id','title','title_ar']);
        foreach($standards as $standard)
        {
            $all_standards['id']=$standard->id;
            $all_standards['questions_number'] = $standard->questions()->count();

            $all_standards['na_questions'] = $standard->questions()->where('is_not_applicable','1')->count();
            $all_standards['questions_answerd'] = Survey::find($settings->id)->answers()->where('standard_id',$standard->id)->count();
            $all_standards['documents_answerd'] =Survey::find($settings->id)->join('sdocument_answers','surveys.id','document_answers.survey_id')
             ->join('documents','documents.id','document_answers.document_id')
             ->join('questions','questions.id','documents.id')->join('survey_answers','survey_answers.question_id','questions.id')->where('answer',"!=",'Not Applicable')->where('questions.standard_id',$standard->id)->count();

             /*$not_met_questions= Survey_Answer::where('survey_id',$settings->id)->where('standard_id',$standard->id)->where('answer','Not Met')->count();
             $all_standards['documents_number'] = Document::join('questions','questions.id','documents.question_id')
            ->join('standards','questions.standard_id','standards.id')->join('survey_answers','survey_answers.question_id','questions.id')->where('answer',"!=",'Not Applicable')
            ->where('standards.id',$standard->id)->count();
             if($not_met_questions>0){
                $all_standards['documents_answerd']+=$not_met_questions;
                $all_standards['documents_number']-=$not_met_questions;
               }*/
            // $all_standards['questions_number']-=$not_met_questions;
             $all_standards['na_answers']=0;
             $all_standards['na_comments']=0;
            if($all_standards['na_questions']>0)
            {
                $all_standards['na_answers']= Survey_Answer::where('survey_id',$settings->id)->where('standard_id',$standard->id)->where('answer','Not Applicable')->count();
                if($all_standards['na_answers']>0)
                {
                    $all_standards['na_comments']= Survey_Answer::join('not_applicable_comments','not_applicable_comments.answer_id','survey_answers.id')
                    ->where('survey_id',$settings->id)->where('standard_id',$standard->id)->where('answer','Not Applicable')->count();
                }
            }
            $all_standards['id']=$standard->id;
            $all_standards['title']=$standard->title;
            $all_standards['title_ar']=$standard->title_ar;
          array_push($temp_standards,$all_standards);
        }
        $status = new Standard;
        return $status->survey_standards_status($temp_standards);
        return $answerd_standard;
    }

    /*public function getStandards_status()
    {
        //$user = Auth::user();
        $settings = getCurrentNGOSurvey();
        
        // this query returns all the standards id that have a least 1 answer in survey_answers table
        
        $answerd_standard = Question::leftJoin('survey_answers',function ($join)use ($settings) {
            $join->on('survey_answers.question_id', '=' ,'questions.id') ;
            $join->where('survey_answers.survey_id',$settings->id) ;
            //$join->leftjoin('standards','survey_answers.standard_id','standards.id');
        })
        ->groupBy('questions.standard_id','survey_answers.question_id','questions.id')
        ->orderBy('questions.standard_id')
        ->get(['questions.standard_id','survey_answers.question_id as answer_id','questions.id as question_id']);
        $status = new Standard;
        return $status->survey_standards_status($answerd_standard);
        return $answerd_standard;
    }*/
}
