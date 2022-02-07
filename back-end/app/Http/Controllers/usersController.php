<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class usersController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
        $this->middleware('jwt.xauth', ['except' => ['login', 'register', 'refresh']]);
        $this->middleware('jwt.xrefresh', ['only' => ['refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['userName','password']);
        $user=User::where('userName',$request['userName'])
        ->first();
        if (! $access_token = auth()->claims(['xtype' => 'auth'])->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
	

        return $this->respondWithToken($access_token,$user->id);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();


        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Register new user
     *
     * @param  string $name, $email, $password, password_confirmation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
    	
        $validator = Validator::make($request->all(), [
                'First_Name' => 'required|string|max:255',
                'Last_Name' => 'required|string|max:255',
                'userName' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
            
        if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'success' => false,
                    'error' =>
                    $validator->errors()->toArray()
                ], 400);
            }
            
        $user = User::firstOrCreate([
                'First_Name' => $request->input('First_Name'),
                'Last_Name' => $request->input('Last_Name'),
                'userName' => $request->input('userName'),
                'password' => Hash::make($request->input('password')),
            ]);
        return response()->json([
            'message' => 'User created.',
                'user' => $user
            ],200);	
        }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        
        $access_token = auth()->claims(['xtype' => 'auth'])->refresh(true,true);
        auth()->setToken($access_token); 
    
        return $this->respondWithToken($access_token);
         
        
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($access_token,$id)
    {
        return response()->json([
            'access_token' => $access_token,
            'token_type' => 'bearer',
            'access_expires_in' => auth()->factory()->getTTL() * 60,
            'refresh_token' => auth()
		->claims([
			'xtype' => 'refresh',
			'xpair' => auth()->payload()->get('jti')
			])
			->setTTL(auth()->factory()->getTTL() * 3)
			->tokenById(auth()->user()->id), 
		'refresh_expires_in' => auth()->factory()->getTTL() * 60,
        'id'=>$id
        ]);
    }
	
}
