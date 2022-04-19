<?php

namespace App\Http\Controllers;

use App\Models\NGO;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class NGOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $NGOs = NGO::all();
        $data = [];
        foreach ($NGOs as $NGO) {
            $data[] = $NGO->prettify();
        }
        if ($data) {
            return good_response('List of NGOs retrieved Successfully', $data);
        }
        return bad_response('Unable to retrieve list of NGOs');
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone_number' => 'required|string',
            'country_id' => 'required|integer|exists:countries,id',
            'name_of_the_organization' => 'required|string',
            'countries_of_operation' => 'required|string',
            'type_of_organization' => 'required|string',
            'year_of_establishment' => 'required|integer',
            'logo' => 'nullable|mimes:png,jpg',
            'logo_disclaimer' => 'required|bool',
            'establishment_notice' => 'required|mimes:pdf,doc,docx,xlsx,xls',
            'bylaws' => 'required|mimes:pdf,doc,docx,xlsx,xls',
            'articles_of_association' => 'required|mimes:pdf,doc,docx,xlsx,xls',
            'role_or_position_in_organization' => 'required|string',
            'address' => 'required|string',
            'username' => 'required|string|unique:users',

        ];
        $parameters = $request->only([
            'first_name',
            'last_name',
            'email',
            'password',
            'phone_number',
            'country_id',
            'name_of_the_organization',
            'countries_of_operation',
            'type_of_organization',
            'year_of_establishment',
            'logo',
            'logo_disclaimer',
            'establishment_notice',
            'bylaws',
            'role_or_position_in_organization',
            'articles_of_association',
            'address',
            'username'
        ]);
        $validator = validation_response($rules, $parameters);
        if ($validator) {
            return $validator;
        }
        $parameters['name'] = $parameters['first_name'] . ' ' . $parameters['last_name'];
        $parameters['password'] = Hash::make($parameters['password']);
        $NGO = NGO::create($parameters);
        if ($NGO) {
            $files = [
                'logo' => $request->file('logo'),
                'establishment_notice' => $request->file('establishment_notice'),
                'bylaws' => $request->file('bylaws'),
                'articles_of_association' => $request->file('articles_of_association'),
            ];
            $saved_files = save_files($files, '/NGOs');
            if ($saved_files) {
                $NGO->update($saved_files);
            } else {
                $NGO->user()->forceDelete();
            }
            return good_response('National Expert User Registered Successfully', $NGO->prettify());
        }

        return bad_response('National Expert User Registered Unsuccessfully');

    }

    /**
     * Display the specified resource.
     *
     * @param NGO $NGO
     * @return Response
     */
    public function show(NGO $NGO)
    {
        return good_response('NGO Retrieved Successfully', $NGO->prettify());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param NGO $NGO
     * @return Response
     */
    public function update(Request $request, NGO $NGO)
    {
        $rules = [
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users',
            'password' => 'sometimes',
            'phone_number' => 'sometimes|string',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'name_of_the_organization' => 'sometimes|string',
            'countries_of_operation' => 'sometimes|string',
            'type_of_organization' => 'sometimes|string',
            'year_of_establishment' => 'sometimes|integer',
            'logo' => 'nullable|mimes:png,jpg',
            'logo_disclaimer' => 'sometimes|bool',
            'establishment_notice' => 'sometimes|mimes:pdf,doc,docx,xlsx,xls',
            'bylaws' => 'sometimes|mimes:pdf,doc,docx,xlsx,xls',
            'articles_of_association' => 'sometimes|mimes:pdf,doc,docx,xlsx,xls',
            'role_or_position_in_organization' => 'sometimes|string',
            'address' => 'sometimes|string',
            'username' => 'sometimes|string|unique:users',
            'settings' => 'sometimes|string',

        ];
        $parameters = $request->only([
            'first_name',
            'last_name',
            'email',
            'password',
            'phone_number',
            'country_id',
            'name_of_the_organization',
            'countries_of_operation',
            'type_of_organization',
            'year_of_establishment',
            'logo',
            'logo_disclaimer',
            'establishment_notice',
            'bylaws',
            'role_or_position_in_organization',
            'articles_of_association',
            'address',
            'username'
        ]);
        $validator = validation_response($rules, $parameters);
        if ($validator) {
            return $validator;
        }
        $settings_update_fail_flag = update_settings($request, $NGO);
        if ($settings_update_fail_flag) {
            return $settings_update_fail_flag;
        }
        $parameters['name'] = $parameters['first_name'] . ' ' . $parameters['last_name'];
        $parameters['password'] = Hash::make($parameters['password']);
        $update_flag = $NGO->AllinOneUpdate($parameters);

        $files = [];

        if ($request->has('logo')) {
            $files['logo'] = $request->file('logo');
        }
        if ($request->has('establishment_notice')) {
            $files['establishment_notice'] = $request->file('establishment_notice');
        }
        if ($request->has('bylaws')) {
            $files['bylaws'] = $request->file('bylaws');
        }
        if ($request->has('articles_of_association')) {
            $files['articles_of_association'] = $request->file('articles_of_association');
        }

        if ($files) {
            $saved_files = save_files($files, '/NGOs');
            if ($saved_files) {
                $update_flag = $NGO->update($saved_files);
            }
        }

        if ($update_flag) {
            return good_response('NGO Updated Successfully', $NGO->prettify());
        }
        return bad_response('Unable to Update NGO');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param NGO $NGO
     * @return Response
     */
    public function destroy(NGO $NGO)
    {
        $data = $NGO->prettify();
        if ($NGO->user->delete() && $NGO->delete()) {
            return good_response('NGO Deleted Successfully', $data);
        }
        return bad_response('NGO Deleted Unsuccessfully');
    }

    /**
     * @param NGO $NGO
     * @return Response
     */
    public function activate(NGO $NGO): Response
    {
        $NGO->user->activate();
        if ($NGO->user->isActive()) {
            return good_response('NGO User Activated', $NGO->prettify());
        }
        return bad_response('NGO User Not Activated');
    }
}
