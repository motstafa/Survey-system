<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use MikeMcLin\WpPassword\Facades\WpPassword;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
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

    public function logout(Request $request)
    {

        if (!$request->user()) {
            return bad_response('No User Logged In');
        }
        $result = $request->user()->currentAccessToken()->delete();
        if ($result) {
            return good_response('User Logged Out Successfully', Auth::user());
        }
        return bad_response('Unable to Log out User');
    }

    public function login_ngo(Request $request)
    {
        $role = Role::find(4);
        return roleBasedLogin($request, $role);
    }

    public function login_cfp(Request $request)
    {
        $role = Role::find(5);
        return roleBasedLogin($request, $role);
    }

    public function login_national_expert(Request $request)
    {
        $role = Role::find(6);
        return roleBasedLogin($request, $role);
    }


}
