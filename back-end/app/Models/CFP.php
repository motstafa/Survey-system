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
 * App\Models\CFP
 *
 * @property int $id
 * @property int $user_id
 * @property string $countries_of_expertise
 * @property string $short_bio
 * @property string|null $highest_educational_degree
 * @property string|null $field_of_highest_educational_degree
 * @property string $professional_photo
 * @property string $cv
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CFP newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CFP newQuery()
 * @method static Builder|CFP onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CFP query()
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereCountriesOfExpertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereFieldOfHighestEducationalDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereHighestEducationalDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereProfessionalPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereShortBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CFP whereUserId($value)
 * @method static Builder|CFP withTrashed()
 * @method static Builder|CFP withoutTrashed()
 * @mixin Eloquent
 */
class CFP extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cfps';
    protected $fillable = [
        'user_id',
        'countries_of_expertise',
        'short_bio',
        'highest_educational_degree',
        'field_of_highest_educational_degree',
        'professional_photo',
        'cv',
    ];

    public static function create($attributes = []): CFP|false
    {
        $user = User::create($attributes);
        $user->roles()->attach(CFP_ROLE_ID);
        $attributes['user_id'] = $user->id;
        try {
            $CFP = static::query()->create($attributes);
            return $CFP;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relationships

    public function AllinOneUpdate($parameters)
    {
        $user = $this->user()->first();
        if ($user->update($parameters) && $this->update($parameters)) {
            return true;
        }
        return false;
    }

}
