<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\UserRoles
 *
 * @property int $user_id
 * @property int $role_id
 * @method static Builder|UserRoles newModelQuery()
 * @method static Builder|UserRoles newQuery()
 * @method static Builder|UserRoles query()
 * @method static Builder|UserRoles whereRoleId($value)
 * @method static Builder|UserRoles whereUserId($value)
 * @mixin Eloquent
 */
class UserRoles extends Pivot
{
    //
}
