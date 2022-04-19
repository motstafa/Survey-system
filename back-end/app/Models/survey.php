<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Survey
 *
 * @property int $id
 * @property int $ngo_id
 * @property string $status
 * @property string $date_started
 * @property string $expiry_date
 * @property string $date_completed
 * @property string $name_of_org
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read NGO|null $NGO
 * @property-read Collection|Survey_Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read Collection|DocumentAnswers[] $document_answers
 * @property-read int|null $document_answers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Survey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Survey newQuery()
 * @method static Builder|Survey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Survey query()
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereDateCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereDateStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereNameOfOrg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereNgoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Survey whereUpdatedAt($value)
 * @method static Builder|Survey withTrashed()
 * @method static Builder|Survey withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|SurveyStandardStatus[] $standards_status
 * @property-read int|null $standards_status_count
 */
class Survey extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ngo_id',
        'name_of_org',
        'status',
        'date_completed',
        'date_started',
        'expiry_date',
    ];


//    Relationships
    public function NGO()
    {
        return $this->belongsTo(NGO::class);
    }

    public function answers()
    {
        return $this->hasMany(Survey_Answer::class);
    }

    public function document_answers()
    {
        return $this->hasMany(DocumentAnswers::class);
    }

    public function standards_status()
    {
        return $this->hasMany(SurveyStandardStatus::class);
    }
}
