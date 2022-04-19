<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * App\Models\Survey_Answer
 *
 * @property int $id
 * @property int $survey_id
 * @property int $standard_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $question_id
 * @property string $answer
 * @property-read Collection|Question[] $questions
 * @property-read int|null $questions_count
 * @property-read Collection|Standard[] $standard
 * @property-read int|null $standard_count
 * @property-read Survey $survey
 * @method static Builder|Survey_Answer newModelQuery()
 * @method static Builder|Survey_Answer newQuery()
 * @method static Builder|Survey_Answer query()
 * @method static Builder|Survey_Answer whereAnswer($value)
 * @method static Builder|Survey_Answer whereCreatedAt($value)
 * @method static Builder|Survey_Answer whereId($value)
 * @method static Builder|Survey_Answer whereQuestionId($value)
 * @method static Builder|Survey_Answer whereStandardId($value)
 * @method static Builder|Survey_Answer whereSurveyId($value)
 * @method static Builder|Survey_Answer whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read NotApplicableComments|null $notApplicableComments
 * @property-read mixed $comment
 */
class Survey_Answer extends Model
{
    use HasFactory;

    protected $table = 'survey_answers';
    protected $fillable = [
        'survey_id',
        'standard_id',
        'question_id',
        'answer',
    ];

    public function getcommentAttribute()
    {
        if ($this->notApplicableComments) {
            return $this->notApplicableComments->comment;
        }
        return false;
    }

    public static function bulkCreate(Request $request)
    {
        $submitted_answers = [];
        $answers = $request->json('data');
        //Check Roles of Auth User
        $user = Auth::user();
        if (!check_roles($user, ['NGO'])) {
            return bad_response('You are not an NGO');
        }
        $rules = [
            'survey_id' => 'required|integer|exists:surveys,id',
            'standard_id' => 'required|integer|exists:standards,id',
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'required|string',
            'comment' => 'sometimes|string|nullable',
        ];
        //Check if the answer is valid one
        foreach ($answers as $answer) {
            //parameters validation
            $validator = validation_response($rules, $answer);
            if ($validator) {
                continue;
            }

            if (!in_array($answer['answer'], DEFAULT_ANSWERS, true)) {
                continue;
            }
            //check if the question matches the standard
            $question = Question::find($answer['question_id']);
            if ($question->standard_id !== (int)$answer['standard_id']) {
                continue;
            }

            $survey = $user->NGO->surveys()->find($answer['survey_id']);
            if (!$survey) {
                continue;
            }
            //checking Not Applicable Criteria
            $question_answer = Survey_Answer::make($answer);
            $save_flag = false;
            if ($question->is_not_applicable === 1) {
                if ($question_answer->answer === DEFAULT_ANSWERS[3] && !empty($answer['comment'])) {
                    $save_flag = $question_answer->save();
                    NotApplicableComments::create([
                        'answer_id' => $question_answer->id,
                        'comment' => $answer['comment']
                    ]);
                }
            }
            if ($question_answer) {
                if (!$save_flag) {
                    $question_answer->save();
                }
                $submitted_answers[] = $answer['question_id'];
            }
        }
        if (!empty($submitted_answers)) {
            return good_response('Answers Submitted Successfully', $submitted_answers);
        }
        return bad_response('No Answers Are Submitted');
    }

    public static function validateAnswer($survey_id, $standard_id, $question_id, $answer)
    {
        $survey = getCurrentNGOSurvey();
        if ($survey->id === $survey_id) {
            //see if the standard contains hte question
            $std = Standard::find($standard_id);
            //check if std exists
            if ($std) {
                $question = Question::find($question_id);
                //check if question exists
                if ($question) {
                    if ($question->standard_id === $standard_id) {
                        //check the actual answer
                        if (in_array($answer, DEFAULT_ANSWERS, true)) {
                            if ($answer === DEFAULT_ANSWERS[3]) {
                                if ($question->is_not_applicable === 1) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function generateNotApplicableComment($comment)
    {
        if (empty($comment)) {
            return false;
        }
        $question = Question::find($this->question_id);
        if (($question->is_not_applicable === 1) && $this->answer === DEFAULT_ANSWERS[3]) {
            return NotApplicableComments::make(
                ['text' => $comment,
                    'answer_id' => $this->id]
            );
        }
        return false;
    }

    //Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function questions()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function notApplicableComments()
    {
        return $this->hasOne(NotApplicableComments::class, 'answer_id');
    }

    public function calculate_scores($survey_answers)
    {

        $questions =new Question;
        $total_questions_number  = $questions->total_questions_number_per_standard();
        
        $met =0;
        $not_met=0;
        $partially_met=0;
        $not_applicable=0;
        $standard_questions=1;
        $old_standard=0;
        $response=array();
        $temp_standard=array();
        $temp_standard['standard']=array();
        $first_time=true;
        $count=0;
        foreach($survey_answers as $answer)
        { 
         
          if($old_standard!=$answer->standard_id)
            {
                $count++;
               $score=((0.5*$partially_met+$met)/$standard_questions)*100;
               $score=round($score,1);
               $temp_standard['score']="$score/100";
               $temp_standard['status']='true';
               if($score<50)
                   {
                     $temp_standard['status']='false';
                   }   
               if(!$first_time)
               {
                   array_push($response,$temp_standard);
               }
               $standard_questions = $total_questions_number[$count-1]['total_number'];
               $temp_standard['id']=$answer->standard_id;
               $temp_standard['imgUrl']=$answer->logo;
               $temp_standard['standard']['title']=$answer->title;
               $temp_standard['standard']['description']=$answer->description;
              $first_time=false;
              $met=$not_met=$partially_met=$not_applicable=0; 
              $old_standard=$answer->standard_id;
            }
          switch ($answer->answer)
            {
             case 'Met':
              $met++;
              break;
             case 'Not Met':
              $not_met++;
              break;
             case 'Partially Met':
                $partially_met++;
                break;
             case 'Not Applicable':
                $standard_questions--;
                break;    
            }
        }
        $score=((0.5*$partially_met+$met)/$standard_questions)*100;
        $score=round($score,1);
        $temp_standard['score']="$score/100";
        if($score<50)
        {
          $temp_standard['status']='false';
        }  
        array_push($response,$temp_standard);
        return $response;
    }

    public function get_scores_details_per_standards($survey_answers)
    {

        $questions =new Question;
        $total_questions_number  = $questions->total_questions_number_per_standard();
        
        $met =0;
        $not_met=0;
        $partially_met=0;
        $not_applicable=0;
        $standard_questions=1;
        $old_standard=0;
        $response=array();
        $temp_standard=array();
        $first_time=true;
        $score=0;
        $count=0;
        foreach($survey_answers as $answer)
        { 
          if($old_standard!=$answer->standard_id)
            {
               $count++;
               $score=((0.5*$partially_met+$met)/$standard_questions)*100;
               $score=round($score, 1);
               if(!$first_time)
               {
                  $temp_standard['Standard']=' ';
                  $temp_standard['Question']=' ';
                  $temp_standard['Document']=' ';
                  $temp_standard['Answer']=' ';   
                  $temp_standard['Score']=$score/100;
                  $temp_standard['Comment']=' '; 
                  array_push($response,$temp_standard);
               }
              $first_time=false;
              $standard_questions = $total_questions_number[$count-1]['total_number'];
              $temp_standard['Standard']=$answer->standard_title;
              $temp_standard['Question']=' ';
              $temp_standard['Document']=' ';
              $temp_standard['Answer']=' ';
              $temp_standard['Score']=' ';
              $temp_standard['Comment']=' ';
              array_push($response,$temp_standard);
            $met=$not_met=$partially_met=0;     
            $old_standard=$answer->standard_id;
            }
             
             $temp_standard['Standard']=' ';    
             $temp_standard['Question']=$answer->question_title;
             $temp_standard['Document']=$answer->name;
             $temp_standard['Answer']=$answer->answer;
             $temp_standard['Score']=' ';
             $temp_standard['Comment']=' ';
          switch ($answer->answer)
            {
             case 'Met':
              $met++;
              break;
             case 'Not Met':
              $not_met++;
              break;
             case 'Partially Met':
                $partially_met++;
                break;
             case 'Not Applicable':
                $standard_questions--;

                $temp_standard['Comment']=$answer->text;
                break;    
            }
            array_push($response,$temp_standard);
        }
        $temp_standard['Standard']=' ';
        $temp_standard['Question']=' ';
        $temp_standard['Document']=' ';
        $temp_standard['Answer']=' ';   
        $temp_standard['Score']=$score/100;
        $temp_standard['Comment']=' ';
        array_push($response,$temp_standard); 
        return  $response;
    }

    public function PDF_Data($survey_answers)
    {
        $questions =new Question;
        $total_questions_number  = $questions->total_questions_number_per_standard();
        
        $met =0;
        $not_met=0;
        $partially_met=0;
        $not_applicable=0;
        $standard_questions=1;
        $old_standard=0;
        $response=array();
        $temp_standard=array();
        $first_time=true;
        $count=0;
        foreach($survey_answers as $answer)
        { 
           
          if($old_standard!=$answer->standard_id)
            {
               $count++;
               $score=((0.5*$partially_met+$met)/$standard_questions)*100;
               
               $temp_standard['Met']=$met;
               $temp_standard['Not_Met']=$not_met;
               $temp_standard['Partially_Met']=$partially_met;
               $temp_standard['Not_Applicable']=$not_applicable;
               $score=round($score,1);
               $temp_standard['score']="$score";
               if($score>=70){
               $temp_standard['capacity_building']='';
                            } 
               if(!$first_time)
               {
                   array_push($response,$temp_standard);
               }

              $first_time=false;
              $standard_questions = $total_questions_number[$count-1]['total_number'];
              $old_standard=$answer->standard_id;
              $temp_standard['standard']=$answer->standard_title;
              $temp_standard['Improvment']='';
              $met=$not_met=$partially_met=$not_applicable=0;              
              $temp_standard['Met_questions']='';
              $temp_standard['NotMet_questions']='';
              $temp_standard['Partially_Met_questions']='';
              $temp_standard['Not_Applicable_questions']='';
              $temp_standard['capacity_building']=$answer->capacity_building;
            }
          switch ($answer->answer)
            {
             case 'Met':
              $met++;
              $temp_standard['Met_questions']=$temp_standard['Met_questions']."$met. $answer->question_title\n";
              $temp_standard['Improvment']=$temp_standard['Improvment']."> $answer->improvement\n";  
              break;
             case 'Not Met':
              $not_met++;
              $temp_standard['NotMet_questions']=$temp_standard['NotMet_questions']."$not_met. $answer->question_title\n";
              $temp_standard['Improvment']=$temp_standard['Improvment']."> $answer->improvement\n"; 
              break;
             case 'Partially Met':
                $partially_met++;
                $temp_standard['Partially_Met_questions']=$temp_standard['Partially_Met_questions']."$partially_met. $answer->question_title\n";
                $temp_standard['Improvment']=$temp_standard['Improvment']."> $answer->improvement\n"; 
                break;
             case 'Not Applicable':
                $standard_questions--;
                $not_applicable++;
                $temp_standard['Not_Applicable_questions']=$temp_standard['Not_Applicable_questions']."$not_applicable. $answer->question_title\n";
                break;    
            }
        }
          $score=((0.5*$partially_met+$met)/$standard_questions)*100;
          $temp_standard['Met']=$met;
          $temp_standard['Not_Met']=$not_met;
          $temp_standard['Partially_Met']=$partially_met;
          $temp_standard['Not_Applicable']=$not_applicable;
          $score=round($score,1);
          $temp_standard['score']="$score";
          if($score>=70)
            {
            $temp_standard['capacity_building']='';
            } 
          array_push($response,$temp_standard);
        return $response;
    }

    
    public function standards_scores($survey_answers)
    {
    
        $questions =new Question;
        $total_questions_number  = $questions->total_questions_number_per_standard();
        
        $met =0;
        $not_met=0;
        $partially_met=0;
        $not_applicable=0;
        $standard_questions=1;
        $old_standard=0;
        $response=array();
        $temp_standard=array();
        $temp_standard['standard']=array();
        $first_time=true;
        $count=0;// counting standard number
        foreach($survey_answers as $answer)
        { 
          
          if($old_standard!=$answer->standard_id)
            {
               $count++;
               $score=((0.5*$partially_met+$met)/$standard_questions);
               $score=round($score,2);
               $temp_standard['totaleScore']=100*$score."/100";

               if($score>=70)
                   {
                    $temp_standard['recommendedCapacityBuilding']=array();
                   }   
               if(!$first_time)
               {
                   array_push($response,$temp_standard);
               }
              $first_time=false;
              $old_standard=$answer->standard_id;
              $standard_questions = $total_questions_number[$count-1]['total_number'];
              $temp_standard['id']=$answer->standard_id;
              $temp_standard['title']="Standard $count - ".$answer->standard_title;
              $temp_standard['met']=array();
              $temp_standard['partiallyMet']=array();
              $temp_standard['notMet']=array();
              $temp_standard['notApplicable']=array();
              
              $temp_standard['met']['questions']=array();
              $temp_standard['partiallyMet']['questions']=array();
              $temp_standard['notMet']['questions']=array();
              $temp_standard['notApplicable']['questions']=array();
              $temp_standard['recommendedPerformanceImprovement']=array();
              $temp_standard['recommendedCapacityBuilding']=array();
              $temp_standard['met']['result']=$temp_standard['notMet']['result']=
              $temp_standard['partiallyMet']['result']=$temp_standard['notApplicable']['result']=0;  
              $capacity=$answer->capacity_building;
              $capacity=trim($capacity, ">");
              array_push($temp_standard['recommendedCapacityBuilding'],explode("\n", $capacity));

              $met=$not_applicable=$not_met=$partially_met=0;
            }
          switch ($answer->answer)
            {
             case 'Met':
              $met++;
              array_push($temp_standard['met']['questions'],$answer->question_title);
              $temp_standard['met']['result']=$met;  
              break;

             case 'Not Met':
              $not_met++;
              array_push($temp_standard['notMet']['questions'],$answer->question_title);
              $temp_standard['notMet']['result']=$not_met;
              array_push($temp_standard['recommendedPerformanceImprovement'],$answer->improvement);
              break;

             case 'Partially Met':
                $partially_met++;
                array_push($temp_standard['partiallyMet']['questions'],$answer->question_title);
                $temp_standard['partiallyMet']['result']=$partially_met;
                array_push($temp_standard['recommendedPerformanceImprovement'],$answer->improvement);
                break;

             case 'Not Applicable':
                $standard_questions--;
                $not_applicable++;
                array_push($temp_standard['notApplicable']['questions'],$answer->question_title);
                $temp_standard['notApplicable']['result']=$not_applicable;
                break;    
            }
        }

        $score=((0.5*$partially_met+$met)/$standard_questions);
        $score=round($score,2);
        $temp_standard['totaleScore']=100*$score."/100";
        if($score>=70)
        {
         $temp_standard['recommendedCapacityBuilding']=array();
        } 
        array_push($response,$temp_standard);
        return $response;
    }
}
