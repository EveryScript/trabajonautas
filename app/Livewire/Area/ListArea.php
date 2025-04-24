<?php

namespace App\Livewire\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use Livewire\Component;

class ListArea extends Component
{
    public AreaForm $area;

    public function save($area_name, $description)
    {
        $this->area->area_name = $area_name;
        $this->area->description = $description;
        $this->area->save();
    }

    public function delete($id)
    {
        Area::find($id)->delete();
    }

    public function render()
    {
        $areas = Area::orderBy('updated_at', 'DESC')->get();
        return view('livewire.area.list-area', compact('areas'));
    }
}
