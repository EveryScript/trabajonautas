<?php

namespace App\Observers;

use App\Models\Area;
use App\Traits\ClearsFormCache;

class AreaObserver
{
    use ClearsFormCache;

    public function created(Area $area) : void
    {
        $this->clearFormCache();
    }

    public function updated(Area $area)
    {
        $this->clearFormCache();
    }

    public function deleted(Area $area)
    {
        $this->clearFormCache();
    }

    public function restored(Area $area): void
    {
        //
    }

    public function forceDeleted(Area $area): void
    {
        //
    }
}
