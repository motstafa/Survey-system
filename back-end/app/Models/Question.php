<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use DB;
/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $standard_id
 * @property string $title
 * @property string|null $improvement
 * @property string|null $info
 * @property string $title_ar
 * @property string $improvement_ar
 * @property string $info_ar
 * @property int|null $required
 * @property int $active
 * @property string $notpi
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereImprovement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereImprovementAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereInfoAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereNotpi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereStandardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTitleAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Standard $standard
 * @method static Builder|Question onlyTrashed()
 * @method static Builder|Question withTrashed()
 * @method static Builder|Question withoutTrashed()
 * @property-read Document $document
 * @property-read int|null $document_count
 * @property string $criteria
 * @property int $is_not_applicable
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereIsNotApplicable($value)
 * @property-read Survey_Answer|null $answers
 * @property-read int|null $answers_count
 */
class Question extends Model
//TODO Added Default and is Applicable Functions
{
    use HasFactory;
    use SoftDeletes;
    public $number = null;

    protected $fillable = [
        'title',
        'improvement',
        'info',
        'title_ar',
        'improvement_ar',
        'info_ar',
        'required',
        'active',
        'notpi',
    ];
    protected $appends = ['Document'];

    public function getDocumentAttribute()
    {
        $doc = $this->document()->get();
        if ($doc->isNotEmpty()) {
            return $doc;
        }
        return false;
    }


    //Relationships
    public function standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    public function answers()
    {
        return $this->hasMany(Survey_Answer::class);
    }

    public function document()
    {
        return $this->hasMany(Document::class);
    }

    public function total_questions_number_per_standard()
    {
        return Question::select('standard_id',DB::raw("count(*) as total_number"))->groupBy('standard_id')
        ->get();
    }
}
