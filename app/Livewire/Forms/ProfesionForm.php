<?php

namespace App\Livewire\Forms;

use App\Models\Profesion;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfesionForm extends Form
{
    #[Validate(['profesion_name' => 'required|min:5'], [], ['profesion_name' => 'nombre de la profesión'])]
    public $profesion_name;

    public function edit($id)
    {
        $this->profesion_name = Profesion::find($id)->profesion_name;
    }

    public function update($id)
    {
        $this->validate();
        $profesion = Profesion::find($id);
        $profesion->update([
            'profesion_name' => $this->profesion_name,
        ]);
    }

    public function delete($id)
    {
        Profesion::find($id)->delete();
    }

    public function save()
    {
        $this->validate();
        Profesion::create(['profesion_name' => $this->profesion_name]);
        $this->reset(['profesion_name']);
    }
}
