<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $mobile_phone_number
 * @property string $title
 * @property string|null $deleted_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobilePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string|null $active_since
 * @property string|null $expiration
 * @property-read NationalExpert|null $AuditUser
 * @property-read NgoUser|null $SelfAssessmentUser
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActiveSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExpiration($value)
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property int $country_id
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @property-read Country|null $CountryRelation
 * @property-read Country $country
 * @property string $username
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @property-read CFP|null $CFP
 * @property-read NGO|null $NGO
 * @property-read NationalExpert|null $NationalExpert
 * @property-read Setting|null $setting
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active_since',
        'expiration',
        'phone_number',
        'last_name',
        'first_name',
        'username',
        'country_id',
    ];
    protected $appends = ['roles', 'country'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'country_id',
        'password',
        'remember_token',
        'pivot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //functions
    public function activate($years = 1): bool
    {
        if (!$this->setting()->exists()) {
            $setting_attr = [
                'user_id' => $this->id,
                'questions_set' => 'Default',
            ];
            Setting::create($setting_attr);
        }
        if ($this->update([
            'active_since' => Carbon::now(),
            'expiration' => Carbon::now()->addYears($years)
        ])) {
            return true;
        }
        return false;
    }

    public function isActive(): bool
    {
        if ($this->active_since === null || $this->expiration === null) {
            return false;
        }
        if (Carbon::now()->isAfter($this->expiration)) {
            return false;
        }
        return true;
    }

    public function getRolesAttribute()
    {
        $roles = $this->roles()->select('name')->get();
        $data = [];
        foreach ($roles as $role) {
            $data[] = $role['name'];
        }
        return $data;
    }

    public function getCountryAttribute()
    {
        return $this->country()->get()->first()->name;
    }

    //Relationships
    public function NationalExpert()
    {
        return $this->hasOne(NationalExpert::class);
    }

    public function CFP()
    {
        return $this->hasOne(CFP::class);
    }

    public function NGO()
    {
        return $this->hasOne(NGO::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }
}
