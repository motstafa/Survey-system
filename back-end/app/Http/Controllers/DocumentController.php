<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Document::all();
        if ($data->isNotEmpty()) {
            return good_response('Documents', $data);
        }
        return bad_response('Empty Documents');
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
            'question_id' => 'required|exists:questions,id|integer',
            'name' => 'required|string',
        ];
        $parameters = $request->only(['question_id', 'name',]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        $doc = Document::create($parameters);
        if ($doc) {
            return good_response('Document Added Successfully', $doc);
        }
        return bad_response('Unable to add Document');
    }

    /**
     * Display the specified resource.
     *
     * @param Document $document
     * @return Response
     */
    public function show(Document $document)
    {
        return good_response('Document retrieved', $document);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Document $document
     * @return Response
     */
    public function update(Request $request, Document $document)
    {
        $rules = [
            'question_id' => 'exists:questions,id|integer',
            'name' => 'string',
        ];
        $parameters = $request->only(['question_id', 'name',]);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        if ($document->update($parameters)) {
            return good_response('Document Updated Successfully', $document);
        }
        return bad_response('Unable to add Document');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Document $document
     * @return Response
     */
    public function destroy(Document $document)
    {
        if ($document->delete()) {
            return good_response('Docment Deleted Successfully', $document);
        }
        return bad_response('Unable to Delete Document');
    }
}
