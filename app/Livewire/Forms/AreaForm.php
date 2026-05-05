<?php

namespace App\Livewire\Forms;

use App\Models\Area;
use App\Models\Profesion;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AreaForm extends Form
{

    public ?Area $area = null;

    public $area_name;
    public $description;
    public $user_id;
    public $profesions;

    public function setArea(Area $area)
    {
        $this->area = $area;
        $this->fill($area->only([
            'area_name',
            'description',
            'user_id',
        ]));
        $this->profesions = $this->area->profesions->pluck('id')->toArray();
    }

    public function save()
    {
        $this->validate();

        if ($this->area) {
            $this->area->update($this->only(['area_name', 'description', 'user_id']));
            $this->area->profesions()->sync($this->profesions);
        } else {
            $new_area = Area::create($this->only(['area_name', 'description', 'user_id']));
            $new_area->profesions()->sync($this->profesions);
        }
    }

    public function rules()
    {
        return [
            'area_name' => [
                'required',
                'min:5',
                Rule::unique('areas', 'area_name')->ignore($this->area?->id)
            ],
            'description' => 'required|max:200',
            'user_id' => 'required|exists:users,id',
            'profesions' => 'required'
        ];
    }

    public function validationAttributes()
    {
        return [
            'area_name' => 'nombre del area',
            'description' => 'descripción',
            'profesions' => 'profesiones'
        ];
    }
}
