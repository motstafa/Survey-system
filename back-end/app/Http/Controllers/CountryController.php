<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Str;
use Validator;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Country::all();
        if ($data->isEmpty()) {
            return bad_response("No countries");
        }
        return good_response('Country List Retrieved', $data);
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
            'name' => 'required|string|unique:countries'
        ];
        $parameters = $request->only(['name']);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        $country = Country::create($parameters);
        if ($country) {
            return good_response('Country Added Successfully', $country);
        }
        return bad_response('Country Cannot be Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Country $country
     * @return Response
     */
    public function show(Country $country)
    {
        return good_response('Country Retrieved Successfully', $country);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Country $country
     * @return Response
     */
    public function update(Request $request, Country $country)
    {
        $rules = [
            'name' => 'string'
        ];
        $parameters = $request->only(['name']);
        $validator = Validator::make($parameters, $rules);
        if ($validator->fails()) {
            return bad_response($validator->messages());
        }
        if ($country->update($parameters)) {
            return good_response('Country Added Successfully', $country);
        }
        return bad_response('Country Cannot be Added Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Country $country
     * @return Response
     */
    public function destroy(Country $country)
    {
        if ($country->delete()) {
            return good_response('Country Deleted Successfully', $country);
        }
        return bad_response('Country Cannot be Deleted');
    }

    public function getStandards(Country $country)
    {
        $data = $country->standards;
        if ($data->isNotEmpty()) {
            return good_response('Standers of ' . $country->name, $data);
        }
        return bad_response('No Standers for ' . $country->name);
    }

    public function test(Country $country)
    {
        $stds = $country->standards;
        $data = [];
        $question_index = 1;
        foreach ($stds as $std) {
            $data[Str::title($std->id)] = $std->only(['id', 'title', 'logo']);
            foreach ($std->questions as $question) {
                $question->number = $question_index;
                $data[Str::title($std->id)]['questions'][] = $question;
                $question_index++;
            }
        }
        if ($data) {
            return good_response('Standards and Questions of ' . $country->name . ' is retrieved', $data);
        }
        return bad_response('Unable to get Standards and Questions');
    }
}
