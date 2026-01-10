<?php

namespace App\Livewire\Forms;

use App\Models\Profesion;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfesionForm extends Form
{
    #[Validate(
        ['profesion_name' => 'required|min:5|unique:profesions,profesion_name'],
        [],
        ['profesion_name' => 'nombre de la profesión']
    )]
    public $profesion_name;
    public $area_id;

    public function edit($id)
    {
        $edit_profesion = Profesion::find($id);
        $this->profesion_name = $edit_profesion->profesion_name;
        $this->area_id = $edit_profesion->area_id;
    }

    public function update($id)
    {
        $this->validate([
            'profesion_name' => 'required|min:5|unique:profesions,profesion_name',
            'area_id' => 'required|exists:areas,id',
        ]);
        $profesion = Profesion::find($id);
        $profesion->update([
            'profesion_name' => $this->profesion_name,
            'area_id' => $this->area_id
        ]);
        $this->reset(['profesion_name', 'area_id']);
    }

    public function delete($id)
    {
        Profesion::find($id)->delete();
    }

    public function save()
    {
        $this->validate([
            'profesion_name' => 'required|min:5|unique:profesions,profesion_name',
            'area_id' => 'required|exists:areas,id',
        ]);
        Profesion::create([
            'profesion_name' => $this->profesion_name,
            'area_id' => $this->area_id
        ]);
        $this->reset(['profesion_name', 'area_id']);
    }

    public function validationAttributes()
    {
        return [
            'profesion_name' => 'nombre de la profesion',
            'area_id' => 'area'
        ];
    }
}
