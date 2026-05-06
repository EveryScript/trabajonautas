<?php

namespace App\Observers;

use App\Models\Company;
use App\Traits\ClearsFormCache;

class CompanyObserver
{
    use ClearsFormCache;
    
    public function created(Company $company): void
    {
        $this->clearFormCache();
    }

    public function updated(Company $company): void
    {
        $this->clearFormCache();
    }

    public function deleted(Company $company): void
    {
        $this->clearFormCache();
    }

    public function restored(Company $company): void
    {
        //
    }

    public function forceDeleted(Company $company): void
    {
        //
    }
}
