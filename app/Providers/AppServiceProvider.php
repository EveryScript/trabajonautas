<?php

namespace App\Providers;

use App\Models\AccountType;
use App\Models\GradeProfile;
use App\Models\Location;
use App\Models\Profesion;
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
        View::composer('profile.update-profile-information-form', function ($view) {
            $view->with([
                'profesions' => Profesion::select('id', 'profesion_name')->get(),
                'locations' => Location::select('id', 'location_name')->get(),
                'grade_profiles' => GradeProfile::select('id', 'profile_name')->get()
            ]);
        });
    }
}
