<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\Survey;
use App\Policies\RolePolicy;
use App\Policies\SurveyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
//        Survey::class => SurveyPolicy::class,
        Role::class => RolePolicy::class,
        Survey::class => SurveyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
