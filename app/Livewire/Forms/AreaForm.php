<?php

namespace App\Livewire\Forms;

use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AreaForm extends Form
{
    #[Validate('required')]
    public $area_name;

    #[Validate(['area_profesions' => 'required|array', 'area_profesions.*' => 'exists:profesions,id'])]
    public $area_profesions = [];

    #[Validate('required|max:100')]
    public $description;

    #[Validate('required')]
    public $user_id;

    public function edit($edit_id)
    {
        $area_edit = Area::find($edit_id);
        $this->area_name = $area_edit->area_name;
        $this->description = $area_edit->description;
        $this->area_profesions = $area_edit->profesions->pluck('id');
        $this->user_id = $area_edit->user_id;
    }

    public function update($update_id)
    {
        $this->user_id = Auth::user()->id;
        $this->validate();
        $area = Area::find($update_id);
        $area->update([
            'area_name' => $this->area_name,
            'description' => $this->description,
            'user_id' => $this->user_id
        ]);
        $area->profesions()->sync($this->area_profesions);
    }

    public function save()
    {
        $this->user_id = Auth::user()->id;
        $this->validate();
        $area = Area::create($this->only('area_name', 'description', 'user_id'));
        $area->profesions()->attach($this->area_profesions);
    }

    public function validationAttributes()
    {
        return [
            'area_name' => 'nombre del area',
            'description' => 'descripción',
            'area_profesions' => 'profesiones relacionadas'
        ];
    }
}
