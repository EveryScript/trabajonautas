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

        if (!$_client->account)
            return false;

        // if ($_client->getRoleNames()->first() === env('ADMIN_ROLE') || $_client->getRoleNames()->first() === env('USER_ROLE'))
        //     return true;

        if ($_client->getRoleNames()->first() === config('app.client_role') && intval($_client->account->account_type_id) === 1)
            return false;

        if ($_client->lastPendingSubscription && !$_client->lastPendingSubscription->verified_payment)
            return false;

        return true;
    }
    protected function getAuthClientWithAccount()
    {
        if (!auth()->check())
            return null;

        return User::select('id', 'name', 'profesion_id')->with('account')->find(auth()->user()->id);
    }
}
