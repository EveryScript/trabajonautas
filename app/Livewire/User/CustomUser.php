<?php

namespace App\Livewire\User;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CustomUser extends Component
{
    public $user;
    public $user_account;

    public function mount($id)
    {
        Carbon::setlocale('es');
        if ($id && User::find($id)) {
            $this->user = User::find($id);
            $this->user_account = $this->user->getRoleNames()[0];
        }
    }

    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d/M/Y H:m:s');
    }

    public function save()
    {
        $this->user->syncRoles([$this->user_account]);
        $this->redirectRoute('user', navigate: true);
    }

    public function render()
    {
        define('FREE', env('FREE_CLIENT_ROLE'));
        define('PRO', env('PRO_CLIENT_ROLE'));
        define('USER', env('USER_ROLE'));
        define('ADMIN', env('ADMIN_ROLE'));
        $user_roles = Role::all("name");
        return view('livewire.user.custom-user', compact('user_roles'));
    }
}
