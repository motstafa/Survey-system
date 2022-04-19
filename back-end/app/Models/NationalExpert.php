<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

/**
 * App\Models\NationalExpert
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert newQuery()
 * @method static Builder|NationalExpert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert query()
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereUserId($value)
 * @method static Builder|NationalExpert withTrashed()
 * @method static Builder|NationalExpert withoutTrashed()
 * @mixin Eloquent
 * @property string $countries_of_expertise
 * @property string $short_bio
 * @property string|null $highest_educational_degree
 * @property string|null $field_of_highest_educational_degree
 * @property string $professional_photo
 * @property string $cv
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereCountriesOfExpertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereFieldOfHighestEducationalDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereHighestEducationalDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereProfessionalPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NationalExpert whereShortBio($value)
 */
class NationalExpert extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'national_experts';
    protected $fillable = [
        'user_id',
        'countries_of_expertise',
        'short_bio',
        'highest_educational_degree',
        'field_of_highest_educational_degree',
        'professional_photo',
        'cv',
    ];

    public static function create($attributes = []): NationalExpert|false
    {
        $user = User::create($attributes);
        $user->roles()->attach(NATIONAL_EXPERT_ROLE_ID);
        $attributes['user_id'] = $user->id;
        try {
            $audit_user = static::query()->create($attributes);
            return $audit_user;
        } catch (QueryException $exception) {
            User::destroy($user->id);
            return false;
        }
    }

    //functions
    public function prettify()
    {
        $user = $this->user()->first()->toArray();
        $result = [];
        $result['id'] = $this->id;
        $result['name'] = $user['name'];
        $result['first_name'] = $user['first_name'];
        $result['last_name'] = $user['last_name'];
        $result['email'] = $user['email'];
        $result['countries_of_expertise'] = $this->countries_of_expertise;
        $result['short_bio'] = $this->short_bio;
        $result['highest_educational_degree'] = $this->highest_educational_degree;
        $result['field_of_highest_educational_degree'] = $this->field_of_highest_educational_degree;
        $result['professional_photo'] = $this->professional_photo;
        $result['cv'] = $this->cv;
        $result['phone_number'] = $user['phone_number'];
        $result['active_since'] = $user['active_since'];
        $result['expiration'] = $user['expiration'];
        return $result;
    }

    public function AllinOneUpdate($parameters)
    {
        $user = $this->user()->first();
        if ($user->update($parameters) && $this->update($parameters)) {
            return true;
        }
        return false;
    }

    //Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
