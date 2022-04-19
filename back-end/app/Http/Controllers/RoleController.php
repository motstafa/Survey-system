<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class RoleController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Role::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
//        $is_authorized=$this->check_authorize('viewAny',Role::class);
//        if ($is_authorized){
//            return good_response('test',Role::all());
//        }
       $data= Role::all();
       if ($data->isEmpty()){
           return bad_response('No Roles');
       }
       return good_response('Roles List Retrieved',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules=[
            'name'=>'required|string|unique:roles',
            'description'=>'required|string',
        ];
        $parameters = $request->only(['name','description']);
        $validator= Validator::make($parameters,$rules);
        if ($validator->fails()){
            return bad_response($validator->messages());
        }
        $role=Role::create($parameters);
        if ($role){
            return good_response('Role Created Successfully',$role);
        }
        return bad_response('Role is not Created');
    }

    /**
     * Display the specified resource.
     *
     * @param Role $role
     * @return Response
     */
    public function show(Role $role)
    {
        return good_response('Role retrieved Successfully',[
            'role'=>$role,
            'users'=>$role->users()->select(['users.id','users.name'])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Role $role
     * @return Response
     */
    public function update(Request $request, Role $role)
    {
        $rules=[
            'name'=>'string',
            'description'=>'string',
        ];
        $parameters = $request->only(['name','description']);
        $validator= Validator::make($parameters,$rules);
        if ($validator->fails()){
            return bad_response($validator->messages());
        }
        if ($role->update($parameters)){
            return good_response('Role Updates Successfully',$role);
        }
        return bad_response('Role is not Updates');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return Response
     */
    public function destroy(Role $role)
    {
        if ($role->delete()){
            return good_response('Role Deleted Successfully',$role);
        }
        return bad_response('Role Cannot be deleted');
    }
}
