<?php

namespace App\Livewire\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class FormArea extends Component
{
    public AreaForm $form;

    #[On('load-area')]
    public function loadArea($id)
    {
        $area = Area::findOrFail(intval($id));
        $this->form->setArea($area);
        $this->dispatch('area-edit');
    }

    public function save()
    {
        $this->form->user_id = Auth::id();
        $this->form->save();
        Cache::forget('areas_list'); // Invalid cache for "areas_list"
        $this->dispatch('area-saved');
    }

    public function render()
    {
        return view('livewire.area.form-area');
    }
}
