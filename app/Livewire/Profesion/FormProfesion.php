<?php

namespace App\Livewire\Profesion;

use App\Livewire\Forms\ProfesionForm;
use App\Models\Area;
use App\Models\Profesion;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class FormProfesion extends Component
{
    public ProfesionForm $form;

    #[On('load-profesion')]
    public function loadProfesion($id)
    {
        $profesion = Profesion::findOrFail(intval($id));
        $this->form->setProfesion($profesion);
        $this->dispatch('profesion-edit');
    }

    public function save()
    {
        $this->form->user_id = auth()->user()->id;
        $this->form->save();
        $this->dispatch('profesion-saved');
    }

    public function render()
    {
        return view('livewire.profesion.form-profesion');
    }
}
