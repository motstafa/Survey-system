<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\NotApplicableComments
 *
 * @property int $id
 * @property int $answer_id
 * @property string $comment
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Survey_Answer $answer
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments newQuery()
 * @method static Builder|NotApplicableComments onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotApplicableComments whereUpdatedAt($value)
 * @method static Builder|NotApplicableComments withTrashed()
 * @method static Builder|NotApplicableComments withoutTrashed()
 * @mixin Eloquent
 */
class NotApplicableComments extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'not_applicable_comments';
    protected $fillable = [
        'answer_id',
        'text',
    ];

    //Relationships
    public function answer()
    {
        return $this->belongsTo(Survey_Answer::class);
    }

}
