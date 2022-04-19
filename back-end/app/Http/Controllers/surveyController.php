<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\survey;


class surveyController extends Controller
{
        /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify');
        $this->middleware('jwt.xauth');
    }

    public function store(Request $request){
      try{
        $validation= $request->validate([
        'id_user'=>'required|numeric',
        'questionsAnswerd'=>'required|numeric'
        ]);
       $date=date('Y-m-d'); 
       $newSurvey= survey::firstOrNew(
       [ 'id_user' => $request['id_user'] ],
       [ 'date' => $date ]
       );
       $newSurvey->videoGames=$request->input('videoGames');
       $newSurvey->TV=$request->input('TV');
       $newSurvey->Sport=$request->input('Sport');
       $newSurvey->questionsAnswerd=$request->input('questionsAnswerd');
             if( $newSurvey->save())
                {
                   return response()->json(["status"=>"data added successfully"],200);
                }
      }
      catch(\Exception $e)
      {
          return response()->json(["status"=>$e],400);
      }
    }


    public function getSurvey($id_user)
    {
      try{
      $date=date('Y-m-d');
      $userSurvey=survey::where(["id_user"=>$id_user],["date"=>$date])->firstOrFail();
      return json_encode($userSurvey); 
      }
      catch(ModelNotFoundException $e)
      {
        return response()->json(["status"=>"no records found"],400);
      }
    }
}
