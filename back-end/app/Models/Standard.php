<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Standard
 *
 * @property int $id
 * @property string $title
 * @property string $logo
 * @property string $description
 * @property string $description_ar
 * @property int $active
 * @property string|null $title_ar
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Standard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Standard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Standard query()
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereDescriptionAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereTitleAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Question[] $questions
 * @property-read int|null $questions_count
 * @method static Builder|Standard onlyTrashed()
 * @method static Builder|Standard withTrashed()
 * @method static Builder|Standard withoutTrashed()
 * @property-read Survey_Answer $answers
 * @method static \Illuminate\Database\Eloquent\Builder|Standard whereCountryId($value)
 * @property-read int|null $answers_count
 * @property-read Collection|SurveyStandardStatus[] $standards_status
 * @property-read int|null $standards_status_count
 */
class Standard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'logo',
        'description',
        'description_ar',
        'active',
        'title_ar',
    ];


    public function frontend()
    {
        $user = Auth::user();
        $survey = $user->NGO->surveys()->orderByDesc('surveys.date_started')->first()->id;
        $data = [];
        $data['id'] = $this->id;
        $data['titleInEng'] = $this->title;
        $data['titleInArab'] = $this->title_ar;
        $status = $this->standards_status()->first();
        if (!$status) {
            $new_status = SurveyStandardStatus::make([
                'status' => 'NotStarted',
                'standard_id' => $this->id,
                'survey_id' => $survey
            ]);
            $data['isComplete'] = $new_status->status;
        } else {
            $data['isComplete'] = $status->status;
        }
        return $data;
    }

    public function getquestionsAttribute()
    {
        return $this->questions()->get();
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Survey_Answer::class, 'survey_id');
    }

    public function standards_status()
    {
        return $this->hasMany(SurveyStandardStatus::class);
    }



    public function survey_standards_status($answerd_standards)
    {
        $response=array();
        $temp_standard=array();
        $first_time=true;
        $old_standard=0;
        $nbr_of_answerd_questions=0;
        $total_standard_questions=0;
        $count=0;
        foreach($answerd_standards as $standard_question)
        {
           // return $standard_question;
            $temp_standard['isComplete']='NotStarted';
            if($standard_question['questions_answerd'] >0 ||$standard_question['documents_answerd']>0)
            {
                $temp_standard['isComplete']='Progress';
            }
            if($standard_question['id']==7)
            {
                return $standard_question;
            }
            if($standard_question['na_answers']==$standard_question['na_comments']
              && $standard_question['questions_number'] == $standard_question['questions_answerd'] && $standard_question['documents_number'] == $standard_question['documents_answerd'])
            {
                $temp_standard['isComplete']='Completed';
            }

            $temp_standard['id']=$standard_question['id'];
            $temp_standard['titleInEng']=$standard_question['title'];
            $temp_standard['titleInArab']= $standard_question['title_ar'];
            array_push($response,$temp_standard);
        }
        return $response;
    }

   /* public function survey_standards_status($answerd_standards)
    {
        $response=array();
        $temp_standard=array();
        $first_time=true;
        $old_standard=0;
        $nbr_of_answerd_questions=0;
        $total_standard_questions=0;
        $all_standards=Standard::orderBy('id')->get(['description','description_ar']);
        $count=0;
        foreach($answerd_standards as $standard_question)
        {
          if($old_standard!=$standard_question->standard_id)
           {
            
            if(!$first_time)
            {
                if($nbr_of_answerd_questions==$total_standard_questions)
                {
                    $temp_standard['isComplete']='Completed';
                }
                array_push($response,$temp_standard);
            }
            $temp_standard['id']=$standard_question->standard_id;
            $temp_standard['titleInEng']=$all_standards[$count]['description'];
            $temp_standard['titleInArab']=$all_standards[$count]['description_ar'];
            $temp_standard['isComplete']='NotStarted';
            $old_standard=$standard_question->standard_id;
            $nbr_of_answerd_questions=$total_standard_questions=0;
            $first_time=false;
            $count++;
           }
         if(!is_null($standard_question->answer_id))
         {
            $temp_standard['isComplete']='Progress';
            $nbr_of_answerd_questions++;
         }
         $total_standard_questions++;
        }
        array_push($response,$temp_standard);
        return $response;
    }*/
}
