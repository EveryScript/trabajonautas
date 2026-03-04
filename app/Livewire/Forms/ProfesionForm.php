<?php

namespace App\Livewire\Forms;

use App\Models\Profesion;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfesionForm extends Form
{
    public ?Profesion $profesion = null;

    public $profesion_name;
    public $area_id;

    public function setProfesion(Profesion $profesion)
    {
        $this->profesion = $profesion;
        
        $this->fill($profesion->only([
            'profesion_name',
            'area_id'
        ]));
    }

    public function save()
    {
        $this->validate();

        if ($this->profesion)
            $this->profesion->update($this->except(['profesion']));
        else
            Profesion::create($this->except(['profesion']));
        $this->reset();
    }

    public function rules()
    {
        return [
            'profesion_name' => [
                'required',
                'min:5',
                Rule::unique('profesions', 'profesion_name')->ignore($this->profesion?->id),
            ],
            'area_id' => 'required|exists:areas,id',
        ];
    }

    public function validationAttributes()
    {
        return [
            'profesion_name' => 'nombre de la profesion',
            'area_id' => 'area'
        ];
    }
}
