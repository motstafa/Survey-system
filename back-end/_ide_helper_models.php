<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

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
     * @method static Builder|CFP newModelQuery()
     * @method static Builder|CFP newQuery()
     * @method static Builder|CFP onlyTrashed()
     * @method static Builder|CFP query()
     * @method static Builder|CFP whereCountriesOfExpertise($value)
     * @method static Builder|CFP whereCreatedAt($value)
     * @method static Builder|CFP whereCv($value)
     * @method static Builder|CFP whereDeletedAt($value)
     * @method static Builder|CFP whereFieldOfHighestEducationalDegree($value)
     * @method static Builder|CFP whereHighestEducationalDegree($value)
     * @method static Builder|CFP whereId($value)
     * @method static Builder|CFP whereProfessionalPhoto($value)
     * @method static Builder|CFP whereShortBio($value)
     * @method static Builder|CFP whereUpdatedAt($value)
     * @method static Builder|CFP whereUserId($value)
     * @method static Builder|CFP withTrashed()
     * @method static Builder|CFP withoutTrashed()
     * @mixin Eloquent
     */
    class CFP extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Country
     *
     * @property int $id
     * @property string $name
     * @property Carbon|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read Collection|Standard[] $standards
     * @property-read int|null $standards_count
     * @method static Builder|Country newModelQuery()
     * @method static Builder|Country newQuery()
     * @method static Builder|Country onlyTrashed()
     * @method static Builder|Country query()
     * @method static Builder|Country whereCreatedAt($value)
     * @method static Builder|Country whereDeletedAt($value)
     * @method static Builder|Country whereId($value)
     * @method static Builder|Country whereName($value)
     * @method static Builder|Country whereUpdatedAt($value)
     * @method static Builder|Country withTrashed()
     * @method static Builder|Country withoutTrashed()
     * @mixin Eloquent
     * @property-read User $user
     */
    class Country extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Document
     *
     * @property int $id
     * @property int $question_id
     * @property string $name
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Carbon|null $deleted_at
     * @property-read Collection|DocumentAnswers[] $answers
     * @property-read int|null $answers_count
     * @property-read Question|null $question
     * @method static Builder|Document newModelQuery()
     * @method static Builder|Document newQuery()
     * @method static Builder|Document onlyTrashed()
     * @method static Builder|Document query()
     * @method static Builder|Document whereCreatedAt($value)
     * @method static Builder|Document whereDeletedAt($value)
     * @method static Builder|Document whereId($value)
     * @method static Builder|Document whereName($value)
     * @method static Builder|Document whereQuestionId($value)
     * @method static Builder|Document whereUpdatedAt($value)
     * @method static Builder|Document withTrashed()
     * @method static Builder|Document withoutTrashed()
     * @mixin Eloquent
     */
    class Document extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

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
     * @method static Builder|DocumentAnswers newModelQuery()
     * @method static Builder|DocumentAnswers newQuery()
     * @method static Builder|DocumentAnswers onlyTrashed()
     * @method static Builder|DocumentAnswers query()
     * @method static Builder|DocumentAnswers whereCreatedAt($value)
     * @method static Builder|DocumentAnswers whereDeletedAt($value)
     * @method static Builder|DocumentAnswers whereDocumentId($value)
     * @method static Builder|DocumentAnswers whereId($value)
     * @method static Builder|DocumentAnswers wherePath($value)
     * @method static Builder|DocumentAnswers whereSurveyId($value)
     * @method static Builder|DocumentAnswers whereUpdatedAt($value)
     * @method static Builder|DocumentAnswers withTrashed()
     * @method static Builder|DocumentAnswers withoutTrashed()
     * @mixin Eloquent
     */
    class DocumentAnswers extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;

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
     * @method static Builder|NGO newModelQuery()
     * @method static Builder|NGO newQuery()
     * @method static Builder|NGO onlyTrashed()
     * @method static Builder|NGO query()
     * @method static Builder|NGO whereAddress($value)
     * @method static Builder|NGO whereArticlesOfAssociation($value)
     * @method static Builder|NGO whereBylaws($value)
     * @method static Builder|NGO whereCountriesOfOperation($value)
     * @method static Builder|NGO whereCreatedAt($value)
     * @method static Builder|NGO whereDeletedAt($value)
     * @method static Builder|NGO whereEstablishmentNotice($value)
     * @method static Builder|NGO whereId($value)
     * @method static Builder|NGO whereLogo($value)
     * @method static Builder|NGO whereLogoDisclaimer($value)
     * @method static Builder|NGO whereNameOfTheOrganization($value)
     * @method static Builder|NGO whereRoleOrPositionInOrganization($value)
     * @method static Builder|NGO whereTypeOfOrganization($value)
     * @method static Builder|NGO whereUpdatedAt($value)
     * @method static Builder|NGO whereUserId($value)
     * @method static Builder|NGO whereYearOfEstablishment($value)
     * @method static Builder|NGO withTrashed()
     * @method static Builder|NGO withoutTrashed()
     * @mixin Eloquent
     * @property-read Collection|Survey[] $surveys
     * @property-read int|null $surveys_count
     */
    class NGO extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\NationalExpert
     *
     * @property int $id
     * @property int $user_id
     * @property Carbon|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read User $user
     * @method static Builder|NationalExpert newModelQuery()
     * @method static Builder|NationalExpert newQuery()
     * @method static Builder|NationalExpert onlyTrashed()
     * @method static Builder|NationalExpert query()
     * @method static Builder|NationalExpert whereCreatedAt($value)
     * @method static Builder|NationalExpert whereDeletedAt($value)
     * @method static Builder|NationalExpert whereId($value)
     * @method static Builder|NationalExpert whereUpdatedAt($value)
     * @method static Builder|NationalExpert whereUserId($value)
     * @method static Builder|NationalExpert withTrashed()
     * @method static Builder|NationalExpert withoutTrashed()
     * @mixin Eloquent
     * @property string $countries_of_expertise
     * @property string $short_bio
     * @property string|null $highest_educational_degree
     * @property string|null $field_of_highest_educational_degree
     * @property string $professional_photo
     * @property string $cv
     * @method static Builder|NationalExpert whereCountriesOfExpertise($value)
     * @method static Builder|NationalExpert whereCv($value)
     * @method static Builder|NationalExpert whereFieldOfHighestEducationalDegree($value)
     * @method static Builder|NationalExpert whereHighestEducationalDegree($value)
     * @method static Builder|NationalExpert whereProfessionalPhoto($value)
     * @method static Builder|NationalExpert whereShortBio($value)
     */
    class NationalExpert extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Question
     *
     * @property int $id
     * @property int $standard_id
     * @property string $title
     * @property string|null $improvement
     * @property string|null $info
     * @property string $title_ar
     * @property string $improvement_ar
     * @property string $info_ar
     * @property int|null $required
     * @property int $active
     * @property string $notpi
     * @property string|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static Builder|Question newModelQuery()
     * @method static Builder|Question newQuery()
     * @method static Builder|Question query()
     * @method static Builder|Question whereActive($value)
     * @method static Builder|Question whereCreatedAt($value)
     * @method static Builder|Question whereDeletedAt($value)
     * @method static Builder|Question whereId($value)
     * @method static Builder|Question whereImprovement($value)
     * @method static Builder|Question whereImprovementAr($value)
     * @method static Builder|Question whereInfo($value)
     * @method static Builder|Question whereInfoAr($value)
     * @method static Builder|Question whereNotpi($value)
     * @method static Builder|Question whereRequired($value)
     * @method static Builder|Question whereStandardId($value)
     * @method static Builder|Question whereTitle($value)
     * @method static Builder|Question whereTitleAr($value)
     * @method static Builder|Question whereUpdatedAt($value)
     * @mixin Eloquent
     * @property-read Standard $standard
     * @method static Builder|Question onlyTrashed()
     * @method static Builder|Question withTrashed()
     * @method static Builder|Question withoutTrashed()
     * @property-read Document $document
     * @property-read int|null $document_count
     * @property-read mixed $number
     */
    class Question extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Role
     *
     * @property int $id
     * @property string $name
     * @property string $description
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Carbon|null $deleted_at
     * @property-read Collection|User[] $users
     * @property-read int|null $users_count
     * @method static Builder|Role newModelQuery()
     * @method static Builder|Role newQuery()
     * @method static Builder|Role onlyTrashed()
     * @method static Builder|Role query()
     * @method static Builder|Role whereCreatedAt($value)
     * @method static Builder|Role whereDeletedAt($value)
     * @method static Builder|Role whereDescription($value)
     * @method static Builder|Role whereId($value)
     * @method static Builder|Role whereName($value)
     * @method static Builder|Role whereUpdatedAt($value)
     * @method static Builder|Role withTrashed()
     * @method static Builder|Role withoutTrashed()
     * @mixin Eloquent
     */
    class Role extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
     * App\Models\Setting
     *
     * @property int $id
     * @property int $user_id
     * @property string $questions_set
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read User $user
     * @method static Builder|Setting newModelQuery()
     * @method static Builder|Setting newQuery()
     * @method static Builder|Setting query()
     * @method static Builder|Setting whereCreatedAt($value)
     * @method static Builder|Setting whereId($value)
     * @method static Builder|Setting whereQuestionsSet($value)
     * @method static Builder|Setting whereUpdatedAt($value)
     * @method static Builder|Setting whereUserId($value)
     */
    class Setting extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Standard
     *
     * @property int $id
     * @property string $title
     * @property string $logo
     * @property string $description
     * @property string $description_ar
     * @property int $active
     * @property string|null $title_ar
     * @property string|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static Builder|Standard newModelQuery()
     * @method static Builder|Standard newQuery()
     * @method static Builder|Standard query()
     * @method static Builder|Standard whereActive($value)
     * @method static Builder|Standard whereCreatedAt($value)
     * @method static Builder|Standard whereDeletedAt($value)
     * @method static Builder|Standard whereDescription($value)
     * @method static Builder|Standard whereDescriptionAr($value)
     * @method static Builder|Standard whereId($value)
     * @method static Builder|Standard whereLogo($value)
     * @method static Builder|Standard whereTitle($value)
     * @method static Builder|Standard whereTitleAr($value)
     * @method static Builder|Standard whereUpdatedAt($value)
     * @mixin Eloquent
     * @property-read Collection|Question[] $questions
     * @property-read int|null $questions_count
     * @method static Builder|Standard onlyTrashed()
     * @method static Builder|Standard withTrashed()
     * @method static Builder|Standard withoutTrashed()
     * @property-read Survey_Answer $answers
     * @property int $country_id
     * @property-read Country $country
     * @method static Builder|Standard whereCountryId($value)
     */
    class Standard extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * App\Models\Survey
     *
     * @property int $id
     * @property int $user_id
     * @property string $status
     * @property string $date_started
     * @property string $expiry_date
     * @property string $date_completed
     * @property string $name_of_org
     * @property Carbon|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read NgoUser $self_assessment_user
     * @method static Builder|Survey newModelQuery()
     * @method static Builder|Survey newQuery()
     * @method static Builder|Survey onlyTrashed()
     * @method static Builder|Survey query()
     * @method static Builder|Survey whereCreatedAt($value)
     * @method static Builder|Survey whereDateCompleted($value)
     * @method static Builder|Survey whereDateStarted($value)
     * @method static Builder|Survey whereDeletedAt($value)
     * @method static Builder|Survey whereExpiryDate($value)
     * @method static Builder|Survey whereId($value)
     * @method static Builder|Survey whereNameOfOrg($value)
     * @method static Builder|Survey whereStatus($value)
     * @method static Builder|Survey whereUpdatedAt($value)
     * @method static Builder|Survey whereUserId($value)
     * @method static Builder|Survey withTrashed()
     * @method static Builder|Survey withoutTrashed()
     * @mixin Eloquent
     * @property-read Survey_Answer $answers
     * @property-read int|null $self_assessment_user_count
     * @property int $self_assessment_user_id
     * @property-read int|null $answers_count
     * @method static Builder|Survey whereSelfAssessmentUserId($value)
     * @property-read Collection|DocumentAnswers[] $document_answers
     * @property-read int|null $document_answers_count
     * @property-read NGO|null $NGO
     * @property int $ngo_id
     * @method static Builder|Survey whereNgoId($value)
     */
    class Survey extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;

    /**
     * App\Models\Survey_Answer
     *
     * @property-read Collection|Standard[] $standard
     * @property-read int|null $standard_count
     * @property-read Collection|Survey[] $survey
     * @property-read int|null $survey_count
     * @method static Builder|Survey_Answer newModelQuery()
     * @method static Builder|Survey_Answer newQuery()
     * @method static Builder|Survey_Answer query()
     * @mixin Eloquent
     * @property int $id
     * @property int $survey_id
     * @property int $standard_id
     * @property string $answers
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static Builder|Survey_Answer whereAnswers($value)
     * @method static Builder|Survey_Answer whereCreatedAt($value)
     * @method static Builder|Survey_Answer whereId($value)
     * @method static Builder|Survey_Answer whereStandardId($value)
     * @method static Builder|Survey_Answer whereSurveyId($value)
     * @method static Builder|Survey_Answer whereUpdatedAt($value)
     */
    class Survey_Answer extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

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
     * @method static Builder|User newModelQuery()
     * @method static Builder|User newQuery()
     * @method static Builder|User query()
     * @method static Builder|User whereCreatedAt($value)
     * @method static Builder|User whereDeletedAt($value)
     * @method static Builder|User whereEmail($value)
     * @method static Builder|User whereEmailVerifiedAt($value)
     * @method static Builder|User whereId($value)
     * @method static Builder|User whereMobilePhoneNumber($value)
     * @method static Builder|User whereName($value)
     * @method static Builder|User wherePassword($value)
     * @method static Builder|User whereRememberToken($value)
     * @method static Builder|User whereTitle($value)
     * @method static Builder|User whereUpdatedAt($value)
     * @mixin Eloquent
     * @property string|null $active_since
     * @property string|null $expiration
     * @property-read NationalExpert|null $AuditUser
     * @property-read NgoUser|null $SelfAssessmentUser
     * @method static Builder|User whereActiveSince($value)
     * @method static Builder|User whereExpiration($value)
     * @method static Builder|User onlyTrashed()
     * @method static Builder|User withTrashed()
     * @method static Builder|User withoutTrashed()
     * @property-read Collection|Role[] $roles
     * @property-read int|null $roles_count
     * @property string $first_name
     * @property string $last_name
     * @property string $phone_number
     * @property int $country_id
     * @method static Builder|User whereCountryId($value)
     * @method static Builder|User whereFirstName($value)
     * @method static Builder|User whereLastName($value)
     * @method static Builder|User wherePhoneNumber($value)
     * @property-read Country|null $CountryRelation
     * @property-read Country $country
     * @property string $username
     * @method static Builder|User whereUsername($value)
     * @property-read CFP|null $CFP
     * @property-read NGO|null $NGO
     * @property-read NationalExpert|null $NationalExpert
     * @property-read Setting|null $setting
     */
    class User extends Eloquent
    {
    }
}

namespace App\Models {

    use Eloquent;

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
    class UserRoles extends Eloquent
    {
    }
}

