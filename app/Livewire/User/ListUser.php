<?php

namespace App\Livewire\User;

use App\Models\ProAccount;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class ListUser extends Component
{
    use WithPagination;

    public $search;
    public $filterClients = true;

    public function edit($id)
    {
        return $this->redirect("/create-user?id=" . $id, true);
    }


    public function clearCacheData()
    {
        try {
            Artisan::call('optimize:clear');
            Cache::forget('companies_list');
            Cache::forget('profesions_list');
            Cache::forget('locations_list');
            Cache::forget('areas_list');
            session()->flash('message', '¡Sistema optimizado y cache limpiado con éxito!');
        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un error al limpiar el cache.');
        }
    }

    public function render()
    {
        define('USER', env('USER_ROLE'));
        define('ADMIN', env('ADMIN_ROLE'));

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [USER, ADMIN]);
        })->get();

        return view('livewire.user.list-user', compact('users'));
    }
}
