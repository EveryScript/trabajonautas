<?php

namespace App\Providers;

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Company;
use App\Models\GradeProfile;
use App\Models\Location;
use App\Models\Profesion;
use App\Observers\AreaObserver;
use App\Observers\CompanyObserver;
use App\Observers\ProfesionObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Area::observe(AreaObserver::class);
        Profesion::observe(ProfesionObserver::class);
        Company::observe(CompanyObserver::class);

        View::composer('profile.update-profile-information-form', function ($view) {
            $view->with([
                'profesions' => Profesion::select('id', 'profesion_name')->get(),
                'locations' => Location::select('id', 'location_name')->get(),
                'grade_profiles' => GradeProfile::select('id', 'profile_name')->get()
            ]);
        });
    }
}
