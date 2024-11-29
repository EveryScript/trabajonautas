<?php

namespace App\Livewire\Area;

use App\Models\Area;
use Livewire\Component;

class ListArea extends Component
{
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
