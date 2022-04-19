<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SurveyStandardStatus
 *
 * @method static Builder|SurveyStandardStatus newModelQuery()
 * @method static Builder|SurveyStandardStatus newQuery()
 * @method static Builder|SurveyStandardStatus query()
 * @mixin Eloquent
 * @property int $id
 * @property int $survey_id
 * @property int $standard_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SurveyStandardStatus whereCreatedAt($value)
 * @method static Builder|SurveyStandardStatus whereId($value)
 * @method static Builder|SurveyStandardStatus whereStandardId($value)
 * @method static Builder|SurveyStandardStatus whereStatus($value)
 * @method static Builder|SurveyStandardStatus whereSurveyId($value)
 * @method static Builder|SurveyStandardStatus whereUpdatedAt($value)
 */
class SurveyStandardStatus extends Model
{
    use HasFactory;

    protected $table = 'survey_standards_status';

    protected $fillable = [
        'survey_id',
        'standard_id',
        'status',
    ];

    //Relationships
    public function survey()
    {
        $this->belongsTo(Survey::class, 'survey_id');
    }

    public function standard()
    {
        $this->belongsTo(Standard::class, 'standard_id');
    }
}
