<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SurveyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
        $allowed_roles = ['super admin', 'NGO'];
        return check_roles($user, $allowed_roles);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Survey $survey
     * @return Response|bool
     */
    public function view(User $user, Survey $survey)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];

        //Super admin
        if (check_roles($user, ['super admin'])) {
            return true;
        }

        //if role allowed
        if (!check_roles($user, $allowedRoles)) {
            return false;
        }

        //if survey is for the user
        if (check_roles($user, ['self assessment'])) {
            $ngo = $user->NGO()->first();
            if ($ngo) {
                if ($ngo->id === $survey->ngo_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];

        if (check_roles($user, $allowedRoles)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Survey $survey
     * @return Response|bool
     */
    public function update(User $user, Survey $survey)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];
        return check_roles($user, $allowedRoles);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Survey $survey
     * @return Response|bool
     */
    public function delete(User $user, Survey $survey)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];
        return check_roles($user, $allowedRoles);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Survey $survey
     * @return Response|bool
     */
    public function restore(User $user, Survey $survey)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];
        return check_roles($user, $allowedRoles);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Survey $survey
     * @return Response|bool
     */
    public function forceDelete(User $user, Survey $survey)
    {
        return true;
        $allowedRoles = ['super admin', 'NGO'];
        return check_roles($user, $allowedRoles);
    }
}
