<?php

namespace App\Livewire\Forms;

use App\Models\Area;
use App\Models\Profesion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AreaForm extends Form
{
    #[Validate('required|unique:areas,area_name')]
    public $area_name;

    #[Validate('required|max:200')]
    public $description;

    #[Validate('required')]
    public $user_id;

    #[Validate('required')]
    public $profesions;

    public function edit($edit_id)
    {
        $area_edit = Area::find($edit_id);
        $this->area_name = $area_edit->area_name;
        $this->description = $area_edit->description;
        $this->user_id = $area_edit->user_id;
        $this->profesions = $area_edit->profesions->pluck('id');
    }

    public function update($update_id)
    {
        $this->user_id = Auth::user()->id;
        $rules = [
            'area_name' => Rule::unique('areas', 'area_name')->ignore($update_id),
            'description' => 'required|max:200',
            'user_id' => 'required',
            'profesions' => 'required'
        ];
        $this->validate($rules);
        $area = Area::find($update_id);
        $area->update([
            'area_name' => $this->area_name,
            'description' => $this->description,
            'user_id' => $this->user_id
        ]);
        $area->profesions()->sync($this->profesions);
    }

    public function save()
    {
        $this->user_id = Auth::user()->id;
        $this->validate();
        $area = Area::create($this->only('area_name', 'description', 'user_id'));
        $area->profesions()->attach($this->profesions);
    }

    public function validationAttributes()
    {
        return [
            'area_name' => 'nombre del area',
            'description' => 'descripción',
        ];
    }
}
