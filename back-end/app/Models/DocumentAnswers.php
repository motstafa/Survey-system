<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\DocumentAnswers
 *
 * @property int $id
 * @property int $survey_id
 * @property int $document_id
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Document $document
 * @property-read Survey $survey
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers newQuery()
 * @method static Builder|DocumentAnswers onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentAnswers whereUpdatedAt($value)
 * @method static Builder|DocumentAnswers withTrashed()
 * @method static Builder|DocumentAnswers withoutTrashed()
 * @mixin Eloquent
 */
class DocumentAnswers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'survey_id',
        'document_id',
        'path',
    ];

    protected $table = 'document_answers';

    protected $with = ['document'];

    //Relationships
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
