<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

/**
 * App\Models\NGO
 *
 * @property int $id
 * @property int $user_id
 * @property string $name_of_the_organization
 * @property string $countries_of_operation
 * @property string $type_of_organization
 * @property string $year_of_establishment
 * @property string $address
 * @property string|null $logo
 * @property int $logo_disclaimer
 * @property string $articles_of_association
 * @property string $establishment_notice
 * @property string $bylaws
 * @property string $role_or_position_in_organization
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|NGO newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NGO newQuery()
 * @method static Builder|NGO onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NGO query()
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereArticlesOfAssociation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereBylaws($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereCountriesOfOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereEstablishmentNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereLogoDisclaimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereNameOfTheOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereRoleOrPositionInOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereTypeOfOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NGO whereYearOfEstablishment($value)
 * @method static Builder|NGO withTrashed()
 * @method static Builder|NGO withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|Survey[] $surveys
 * @property-read int|null $surveys_count
 */
class NGO extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ngos';
    protected $fillable = [
        'user_id',
        'name_of_the_organization',
        'countries_of_operation',
        'type_of_organization',
        'year_of_establishment',
        'logo',
        'logo_disclaimer',
        'establishment_notice',
        'bylaws',
        'role_or_position_in_organization',
        'articles_of_association',
        'address',
    ];

    public static function create($attributes = []): NGO|false
    {
        $user = User::create($attributes);
        $user->roles()->attach(NGO_ROLE_ID);
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
        $result['username'] = $user['username'];
        $result['email'] = $user['email'];
        $result['phone_number'] = $user['phone_number'];
        $result['active_since'] = $user['active_since'];
        $result['expiration'] = $user['expiration'];
        $result['name_of_the_organization'] = $this->name_of_the_organization;
        $result['countries_of_operation'] = $this->countries_of_operation;
        $result['type_of_organization'] = $this->type_of_organization;
        $result['year_of_establishment'] = $this->year_of_establishment;
        $result['logo'] = $this->logo;
        $result['logo_disclaimer'] = $this->logo_disclaimer;
        $result['establishment_notice'] = $this->establishment_notice;
        $result['bylaws'] = $this->bylaws;
        $result['role_or_position_in_organization'] = $this->role_or_position_in_organization;
        $result['articles_of_association'] = $this->articles_of_association;
        $result['address'] = $this->address;
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

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'ngo_id');
    }
}
