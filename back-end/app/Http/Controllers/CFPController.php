<?php

namespace App\Http\Controllers;

use App\Models\CFP;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class CFPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $results = [];
        try {
            $users = CFP::all();
            foreach ($users as $user) {
                $results[] = $user->prettify();
            }
        } catch (Exception $exception) {
            return bad_response('Contact-Admin');
        }
        if ($results) {
            return good_response('List of CFP Users Retrieved', $results);
        }
        return bad_response('List of CFP Cannot Be Retrieved');
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
            'countries_of_expertise' => 'required',
            'short_bio' => 'required|string',
            'highest_educational_degree' => 'nullable|string',
            'field_of_highest_educational_degree' => 'nullable|string',
            'professional_photo' => 'required|mimes:jpg,png',
            'cv' => 'required|mimes:pdf,doc,docx',
            'username' => 'required|string|unique:users',
        ];
        $parameters = $request->only([
            'first_name',
            'last_name',
            'username',
            'email',
            'password',
            'phone_number',
            'country_id',
            'countries_of_expertise',
            'short_bio',
            'highest_educational_degree',
            'field_of_highest_educational_degree',
            'professional_photo',
            'cv',
        ]);

        $validator = validation_response($rules, $parameters);
        if ($validator) {
            return $validator;
        }
        $parameters['name'] = $parameters['first_name'] . ' ' . $parameters['last_name'];
        $parameters['password'] = Hash::make($parameters['password']);
        $CFP = CFP::create($parameters);
        $files = [
            'professional_photo' => $request->file('professional_photo'),
            'cv' => $request->file('cv'),
        ];
        $saved_files = save_files($files, '/CFP');
        if ($saved_files) {
            $CFP->update($saved_files);
        } else {
            $CFP->user()->forceDelete();
        }
        if ($CFP) {
            return good_response('CFP User Registered Successfully', $CFP->prettify());
        }
        return bad_response('CFP User Registered Unsuccessfully');
    }

    /**
     * Display the specified resource.
     *
     * @param CFP $CFP
     * @return Response
     */
    public function show(CFP $CFP)
    {
        return good_response('CFP User Found', $CFP->prettify());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param CFP $CFP
     * @return Response
     */
    public function update(Request $request, CFP $CFP)
    {
        $rules = [
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users',
            'password' => 'sometimes',
            'phone_number' => 'sometimes|string',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'countries_of_expertise' => 'sometimes',
            'short_bio' => 'sometimes|string',
            'highest_educational_degree' => 'nullable|string',
            'field_of_highest_educational_degree' => 'nullable|string',
            'professional_photo' => 'sometimes|mimes:jpg,png',
            'cv' => 'sometimes|mimes:pdf,doc,docx',
            'username' => 'sometimes|string|unique:users',
            'settings' => 'sometimes|string',
        ];
        $parameters = $request->only([
            'first_name',
            'last_name',
            'username',
            'email',
            'password',
            'phone_number',
            'country_id',
            'countries_of_expertise',
            'short_bio',
            'highest_educational_degree',
            'field_of_highest_educational_degree',
            'professional_photo',
            'cv',
        ]);
        $validator = validation_response($rules, $parameters);
        if ($validator) {
            return $validator;
        }
        $settings_update_fail_flag = update_settings($request, $CFP);
        if ($settings_update_fail_flag) {
            return $settings_update_fail_flag;
        }
        if ($request->has(['first_name', 'last_name'])) {
            $parameters['name'] = $parameters['first_name'] . ' ' . $parameters['last_name'];
        }
        if ($request->has('password')) {
            $parameters['password'] = Hash::make($parameters['password']);
        }
        $update_flag = $CFP->AllinOneUpdate($parameters);

        if ($request->has('professional_photo')) {
            $files = [
                'professional_photo' => $request->file('professional_photo'),
            ];

            $saved_files = save_files($files, '/CFP');
            if ($saved_files) {
                $update_flag = $CFP->update($saved_files);
            }
        }

        if ($request->has('cv')) {
            $files = [
                'cv' => $request->file('cv'),
            ];
            $saved_files = save_files($files, '/CFP');
            if ($saved_files) {
                $update_flag = $CFP->update($saved_files);
            }
        }
        if ($update_flag) {
            return good_response('CFP User Updated Successfully', $CFP->prettify());
        }
        return bad_response('Unable to Update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CFP $CFP
     * @return Response
     */
    public function destroy(CFP $CFP)
    {
        $data = $CFP->prettify();
        if ($CFP->user->delete() && $CFP->delete()) {
            return good_response('CFP User Deleted Successfully', $data);
        }
        return bad_response('CFP User Deleted Unsuccessfully');

    }

    /**
     * @param CFP $CFP
     * @return Response
     */
    public function activate(CFP $CFP): Response
    {
        $CFP->user->activate();
        if ($CFP->user->isActive()) {
            return good_response('CFP User Activated', $CFP->prettify());
        }
        return bad_response('CFP User Not Activated');
    }
}
