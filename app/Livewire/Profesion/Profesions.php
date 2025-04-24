<?php

namespace App\Livewire\Profesion;

use App\Livewire\Forms\ProfesionForm;
use App\Models\Profesion;
use Livewire\Component;
use Livewire\WithPagination;

class Profesions extends Component
{
    use WithPagination;

    public ProfesionForm $profesion;
    public $search, $update_mode = false;
    public $update_id, $delete_id;

    public function edit($id)
    {
        $this->update_id = $id;
        $this->profesion->edit($id);
        $this->update_mode = true;
        $this->render();
    }

    public function update()
    {
        $this->profesion->update($this->update_id);
        $this->reset(['update_mode', 'profesion.profesion_name']);
        $this->render();
    }

    public function delete($id)
    {
        $this->profesion->delete($id);
        $this->render();
        $this->search = null;
    }

    public function save()
    {
        $this->profesion->save();
        $this->dispatch('profesion-saved');
    }

    public function render()
    {
        $profesions = Profesion::orderBy('profesion_name', 'ASC')
            ->when($this->search, fn($query) => $query->where('profesion_name', 'LIKE', '%' . $this->search . '%'))
            ->simplePaginate(5);
        return view('livewire.profesion.profesions', compact('profesions'));
    }
}
