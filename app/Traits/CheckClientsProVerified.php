<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;

trait CheckClientsProVerified
{
    protected $_client;
    protected $_pro;

    protected function isClientProVerified(): bool
    {
        if (auth()->check()) {
            $this->_client = User::with('account.accountType')->find(auth()->user()->id);
            // Is a client with account and has CLIENT role?
            if ($this->_client && $this->isClientRole()) {
                $this->_pro = $this->_client->account->verified_payment;
                return $this->_pro;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function isClientRole()
    {
        if (auth()->check()) {
            return auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE');
        } else {
            return false;
        }
    }
}
