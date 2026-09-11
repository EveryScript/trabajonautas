<?php

namespace App\Observers;

use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class LocationObserver
{
    public function created(Location $location): void
    {
        Cache::forget('total_locations_count');
        Cache::forget('locations_list');
        Cache::forget('web-locations');
    }

    public function updated(Location $location): void
    {
        //
    }

    public function deleted(Location $location): void
    {
        Cache::forget('total_locations_count');
        Cache::forget('locations_list');
        Cache::forget('web-locations');
    }

    public function restored(Location $location): void
    {
        //
    }

    public function forceDeleted(Location $location): void
    {
        //
    }
}
