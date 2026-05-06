<?php

namespace App\Observers;

use App\Models\Profesion;
use App\Traits\ClearsFormCache;

class ProfesionObserver
{
    use ClearsFormCache;

    public function created(Profesion $profesion): void
    {
        $this->clearFormCache();
    }

    public function updated(Profesion $profesion): void
    {
        $this->clearFormCache();
    }

    public function deleted(Profesion $profesion): void
    {
        $this->clearFormCache();
    }

    public function restored(Profesion $profesion): void
    {
        //
    }

    public function forceDeleted(Profesion $profesion): void
    {
        //
    }
}
