<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class UpdateExpiredAccounts extends Command
{
    // Command Name    
    protected $signature = 'trabajonautas:update-expired-accounts';
    protected $description = 'Update account if limit_time is expired';

    public function handle()
    {
        $affected = Account::where('limit_time', '<', now())
            ->where('account_type_id', '!=', 1)
            ->update(['account_type_id' => 1]);

        $this->info("Proceso completado. Cuentas actualizadas: {$affected}");
    }
}
