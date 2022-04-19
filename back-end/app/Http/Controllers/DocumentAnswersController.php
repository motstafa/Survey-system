<?php

namespace App\Http\Controllers;

use App\Models\DocumentAnswers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Support\Facades\Storage;
class DocumentAnswersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = DocumentAnswers::all();
        if ($data->isNotEmpty()) {
            return good_response('Document Answers', $data);
        }
        return bad_response('Unable To retrieve Document Answers');
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
            'survey_id' => 'required|exists:surveys,id|integer',
            'document_id' => 'required|exists:documents,id|integer',
            'document' => 'required|mimes:pdf,docx,doc,png,jpg,jpeg'
        ];
        $parameters = $request->only([
            'survey_id',
            'document_id',
            'document',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        
        $docs = DocumentAnswers::firstOrNew(['survey_id'=>$parameters['survey_id'],'document_id'=>$parameters['document_id']]);
        if(!is_null($docs->path))
        {
            Storage::delete('public/'.$docs->path);
        }
        $key = $parameters['survey_id'] . '_' . $parameters['document_id'];
            $file = [$key => $request->file('document')];
            $filenames = save_files($file, "Documents/" . $parameters['document_id'] . '_files');
            if ($filenames) {
                $docs->path = $filenames[$key];
            } else {
                return bad_response('Unable to save files');
            }
            $docs->save();
        if ($docs) {
            return good_response('Document Answers added Successfully', $docs);
        }

        return bad_response('Unable to save Document Answer');
    }

    /**
     * Display the specified resource.
     *
     * @param DocumentAnswers $documentAnswer
     * @return Response
     */
    public function show(DocumentAnswers $documentAnswer)
    {
        return good_response('Document Answer Retrieved', $documentAnswer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param DocumentAnswers $documentAnswer
     * @return Response
     */
    public function update(Request $request, DocumentAnswers $documentAnswer)
    {

        $rules = [
            'survey_id' => 'exists:surveys,id|integer',
            'document_id' => 'exists:documents,id|integer',
            'document' => 'mimes:pdf,docx,doc,png,jpg'
        ];
        $parameters = $request->only([
            'survey_id',
            'document_id',
            'document',
        ]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }

        if (!$request->has('survey_id')) {
            $survey_id = $documentAnswer->survey_id;
        } else {
            $survey_id = $parameters['survey_id'];
        }

        if (!$request->has('document_id')) {
            $document_id = $documentAnswer->document_id;
        } else {
            $document_id = $parameters['document_id'];
        }
        if ($request->has('document')) {
            $key = $survey_id . '_' . $document_id;
            $file = [$key => $request->file('document')];
            $filenames = save_files($file, "Documents/" . $document_id . '_files');
            if ($filenames) {
                $parameters['path'] = $filenames[$key];
            } else {
                return bad_response('Unable to save files');
            }

        }

        if ($documentAnswer->update()) {
            return good_response('Document Answers Updated Successfully', $documentAnswer);
        }
        return bad_response('Unable to update Document');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DocumentAnswers $documentAnswer
     * @return Response
     */
    public function destroy(DocumentAnswers $documentAnswer)
    {
        if ($documentAnswer->delete()) {
            return good_response('Document Answers deleted', $documentAnswer);
        }
        return bad_response('Document Answers Cant be deleted');
    }
}
