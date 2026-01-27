<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;

trait AuthorizeClients
{
    // Authorize clients to seee PRO Announcements
    protected function isAuthClientProVerifiedAndCurrent(): bool
    {
        if (!auth()->check())
            return false;

        $_client = $this->getAuthClientWithAccount();

        if ($_client->getRoleNames()->first() === env('ADMIN_ROLE') || $_client->getRoleNames()->first() === env('USER_ROLE'))
            return true;

        if ($_client->getRoleNames()->first() === env('CLIENT_ROLE') && intval($_client->account->account_type_id) === 1)
            return false;

        if (!$_client->account->verified_payment)
            return false;

        return true;
    }
    protected function getAuthClientWithAccount()
    {
        if (!auth()->check())
            return null;

        return User::select('id', 'name', 'account_id', 'profesion_id')->with('account.accountType')->find(auth()->user()->id);
    }
}
