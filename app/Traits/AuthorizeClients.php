<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;

trait AuthorizeClients
{
    protected function isAuthClientProVerifiedAndCurrent(): bool
    {
        $user = $this->getAuthClientWithAccount();

        if (!$user)
            return false;

        if ($user->hasAnyRole([config('app.user_role'), config('app.admin_role')]))
            return true;

        if (!$user->account)
            return false;

        if ($user->hasRole(config('app.client_role')) && (int)$user->account->account_type_id === 1)
            return false;

        if ($user->lastPendingSubscription && !$user->lastPendingSubscription->verified_payment)
            return false;

        return true;

        // if (!auth()->check())
        //     return false;

        // $_client = $this->getAuthClientWithAccount();

        // if (!$_client->account)
        //     return false;

        // if ($_client->getRoleNames()->first() === config('app.client_role') && intval($_client->account->account_type_id) === 1)
        //     return false;

        // if ($_client->lastPendingSubscription && !$_client->lastPendingSubscription->verified_payment)
        //     return false;

        // return true;
    }
    protected function getAuthClientWithAccount()
    {
        if (!auth()->check())
            return null;

        return User::select('id', 'name', 'profesion_id')->with('account')->find(auth()->user()->id);
    }
}
