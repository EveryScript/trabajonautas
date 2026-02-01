<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    // Through relation
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            Account::class,
            'account_type_id',
            'id',
            'id',
            'user_id'
        );
    }
}
