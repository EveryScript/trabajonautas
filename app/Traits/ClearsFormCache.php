<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsFormCache
{
    public function clearFormCache()
    {
        Cache::forget('areas');
        Cache::forget('profesions');
        Cache::forget('companies');
    }
}
