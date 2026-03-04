<?php

namespace App\Livewire\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use Livewire\Attributes\On;
use Livewire\Component;

class ListArea extends Component
{
    public function editArea($id)
    {
        $this->dispatch('load-area', id: $id)->to(FormArea::class);
    }

    public function delete($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
    }

    #[On('area-saved')]
    public function refreshList() {}

    public function render()
    {
        $areas = Area::select(['id', 'area_name', 'description', 'user_id'])->orderBy('updated_at', 'DESC')->get();
        return view('livewire.area.list-area', [
            'areas' => $areas
        ]);
    }
}
