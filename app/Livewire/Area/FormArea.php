<?php

namespace App\Livewire\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use App\Models\Profesion;
use Livewire\Component;

class FormArea extends Component
{
    public $id; // Edit
    public AreaForm $area;
    public $profesions;
    public $search = '';

    public function mount($id = null)
    {
        if ($id && Area::find($id)) {
            $this->id = $id;
            $this->area->edit($id);
        }
    }

    public function update()
    {
        $this->area->update($this->id);
        $this->redirectRoute('area', navigate: true);
    }

    public function save()
    {
        $this->area->save();
        $this->redirectRoute('area', navigate: true);
    }

    public function render()
    {
        $this->profesions = Profesion::orderBy('profesion_name', 'ASC')->get();
        return view('livewire.area.form-area');
    }
}
