<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\Exception;
use App\Models\NotApplicableComments;
class QuestionController extends Controller
//TODO Added Default and is Applicable Functions
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $result = [];
        try {
            $standards = Standard::all();
            foreach ($standards as $standard) {
                $result[$standard->title] = $standard->questions()->get();
            }

        } catch (Exception $exception) {
            return bad_response('Contact-Admin');
        }
        if (!empty($result)) {
            return good_response('questions retrieved successfully ', $result);
        }
        return bad_response('Empty List');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //TODO: For the Admin Panel
    }

    /**
     * Display the specified resource.
     *
     * @param Question $question
     * @return Response
     */
    public function show(Question $question)
    {
        return good_response('question retrieved', $question);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Question $question
     * @return Response
     */
    public function update(Request $request, Question $question)
    {
        //TODO: For the Admin Panel
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Question $question
     * @return Response
     */
    public function destroy(Question $question)
    {
        return good_response('question Deleted', $question);
    }

    public function questionOfCountry(Country $country)
    {
        $stds = $country->standards()->get();
        $questions = [];
        foreach ($stds as $std) {
            $questions[$std->title] = $std->questions()->get();
        }
        return $questions;
    }

    public function questionsAndAnswersOfStandard(Standard $standard)
    {
        $data = [];
        /*
         id: '',
         answer: '', //can be Met ot notMet or partiallyMet or notApplicable or ""
         documentTitle: "..", // ex "By laws",
         fileName: "exmaple.pdf",
         documentUploaded: true or false,
         explanationContent: "", // "" if notApplicable or content vide
         applicable: true or false,
         documentId: ...,
        */
        $survey = getCurrentNGOSurvey();
        $questions = $standard->questions()->get();
        foreach ($questions as $question) {
            $record = [];
            $record['id']=$question->id;
            $record['question']=$question->title;
            $record['answer'] = '';
            $record['explanationContent'] = '';
            $record['not_applicable'] = false;
            
            $record['documents']=array();
/*
            $record['documentTitle'] = '';
            $record['fileName'] = '';
            $record['documentUploaded'] = false;
            $record['documentId'] = '';*/
            //Check for Old Answer
            $answer = $question->answers()->where('survey_id', '=', $survey->id)->where('standard_id', '=', $standard->id)->first();
            if ($answer) {
                $record['answer'] = $answer->answer;
                if ($record['answer'] === DEFAULT_ANSWERS[3]) {
                    if ($question->is_not_applicable === 1) {
                        $record['explanationContent'] = NotApplicableComments::where('answer_id',$answer->id)->first('text');
                    }
                }
            }
            //Get if not Applicable
            if ($question->is_not_applicable === 1) {
                $record['not_applicable'] = true;
            }
            //Get Doc Title and upload path
            $documents = $question->document()->get();
            foreach($documents as $document)
            if ($document) {

                $temp_document=array();
                $temp_document['documentTitle'] = $document->name;
                $temp_document['documentId'] = $document->id;
                $doc_answer = $document->answers()->get();
                if ($doc_answer->isEmpty()) {
                    $temp_document['documentUploaded'] = false;
                    $temp_document['fileName'] = '';

                } else {
                    $path = $doc_answer->where('survey_id', '=', $survey->id)->where('document_id', '=', $document->id)->first()->path;
                    $temp_document['fileName'] = str($path)->basename()->toString();
                    $temp_document['documentUploaded'] = true;
                }
                array_push($record['documents'],$temp_document);                
            }

            $data[] = $record;

        }
        if ($data) {
            return good_response('questions and answers', $data);
        }
        return bad_response('not questions and answers');
    }
}
