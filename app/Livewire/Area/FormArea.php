<?php

namespace App\Livewire\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use App\Models\Profesion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
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
        $this->dispatch('area-edit', profesion_ids: $this->form->profesions);
    }

    public function save()
    {
        $this->form->user_id = Auth::id();
        $this->form->save();
        $this->dispatch('area-saved');
    }

    public function closeModal()
    {
        $this->form->reset();
        $this->dispatch('close-form');
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('profesions', 86400, function () {
            return Profesion::with('areas')->get()->map(function ($p) {
                return [
                    'id' => (int) $p->id,
                    'profesion_name' => $p->profesion_name,
                    'area_ids' => $p->areas->pluck('id')->map(fn($id) => (int)$id)->toArray()
                ];
            })->toArray();
        });
    }

    public function render()
    {
        return view('livewire.area.form-area', [
            'profesions' => $this->profesions
        ]);
    }
}
