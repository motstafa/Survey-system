<?php

use App\Models\NotApplicableComments;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use MikeMcLin\WpPassword\Facades\WpPassword;

//CONSTANTS
//ROLES
const SUPER_ADMIN_ROLE_ID = 1;
const NGO_ROLE_ID = 4;
const CFP_ROLE_ID = 5;
const NATIONAL_EXPERT_ROLE_ID = 6;
const SETTINGS = ['Default', 'Country Based'];
const DEFAULT_ANSWERS = ['Met', 'Not Met', 'Partially Met', 'Not Applicable'];

//FUNCTIONS
function bad_response($msg)
{
    return response([
        'status' => 'FAILED',
        'message' => $msg,
        'data' => []
    ]);
}

function good_response($msg, $data)
{
    return response([
        'status' => 'SUCCESS',
        'message' => $msg,
        'data' => $data
    ]);
}

function validation_response($rules, $parameters)
{
    //Validation of input
    $validator = Validator::make($parameters, $rules);
    if ($validator->fails()) {
        return bad_response($validator->messages());
    }
    return false;
}

function unauthorized_response()
{
    return response([
        'status' => 'Failed',
        'message' => 'Unauthorized',
        'data' => []
    ]);
}

function check_roles(User $user, array $allowedRoles)
{
    $res = $user->roles()->whereIn('name', $allowedRoles)->get();
    if ($res->isEmpty()) {
        return false;
    }
    return true;
}

function save_files(array $files, string $dir,)
{
    $paths = [];
    foreach ($files as $key => $file) {
        $filename = $key . "_" . Carbon::now()->timestamp . "." . $file->extension();
        $file->storeAs("Public/$dir/", $filename);
        $paths[$key] = $dir . '/' . $filename;
    }
    if ($paths) {
        return $paths;
    }
    return false;
}

function delete_files(array $files)
{
    foreach ($files as $file) {
        $public_path = public_path($file);
        echo $public_path;
        if (File::exists($public_path) && File::delete($public_path)) {
            return true;
        }
    }
    return false;
}

function roleBasedLogin(Request $request, Role $role)
{
    $rules = [
        'email' => 'required|string',
        'password' => 'required|string'
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return bad_response($validator->messages());
    }
    $user = User::whereEmail($request->input('email'))->first();
    //Role Specific
    if (!$user->roles()->find($role)) {
        return bad_response('Please Check Your Role');
    }

    //TODO allow users to Login via Email
    if ($user && $user->isActive()) {
        //checks and Fixes the WordPress password to laravel password
        if (WpPassword::check($request->input('password'), $user->password)) {
            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);
        }
        if (Hash::check($request->input('password'), $user->password)) {
            $data = [
                'user_id' => $user->id,
                'token' => $user->createToken('Login')->plainTextToken
            ];
            return good_response("User Logged In Successfully", $data);
        }
    }

    return bad_response('Invalid Email or Password');
}

function update_settings(Request $request, $model)
{
    try {
        if ($request->has('settings')) {
            $setting = $request->get('settings');
            if (in_array($setting, SETTINGS, true)) {
                $model->user->setting()->update(['questions_set' => $setting]);
            } else {
                return bad_response('Invalid Settings');
            }
        }
    } catch (Exception $exception) {
        return false;
    }
}

function get_current_user_settings()
{
    if (Auth::check()) {
        $base_user = Auth::user();
        $settings = $base_user->setting;
        if ($settings) {
            return $settings;
        }
    }
    return false;

}

function addQuestion($parameters)
{
    $question = Question::find($parameters['question_id']);
    if (!$question->standard_id === $parameters['standard_id']) {
//        return bad_response('Invalid Survey ID');
        return false;
    }

    if (!Auth::user()->NGO) {
//        return bad_response('User is not a NGO User');
        return false;
    }
    $user = Auth::user()->NGO;
    $survey = $user->surveys()->find($parameters['survey_id']);
    if (!$survey) {
//        return bad_response('Check Your Input');
        return false;
    }
    $answer = Survey_Answer::make($parameters);
    $save_flag = false;

    if ($question->is_not_applicable === 1) {
        if ($answer->answer === DEFAULT_ANSWERS[3] && !empty($parameters['comment'])) {
            $save_flag = $answer->save();
            NotApplicableComments::create([
                'answer_id' => $answer->id,
                'comment' => $parameters['comment']
            ]);
        }
    }
    return $save_flag;
}

function getCurrentNGOSurvey()
{
    //$user = Auth::user();
    $user = User::find(27);
    return $user->NGO->surveys()->orderByDesc('surveys.date_started')->first();
}

