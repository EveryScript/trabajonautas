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
        $this->cancelForm();
    }

    public function save()
    {
        $this->profesion->save();
        $this->dispatch('profesion-saved');
    }

    public function cancelForm()
    {
        $this->profesion->profesion_name = null;
        $this->update_mode = false;
    }

    public function render()
    {
        $base_query = Profesion::orderBy('profesion_name', 'ASC');

        $filter_query = (clone $base_query)
            ->when($this->search, fn($query) => $query->where('profesion_name', 'LIKE', '%' . $this->search . '%'));

        $count_results = $filter_query->count();

        $profesions = $count_results > 0
            ? $filter_query->simplePaginate(7)
            : $base_query->simplePaginate(7);

        return view('livewire.profesion.profesions', [
            'profesions' => $profesions,
            'count_results' => $count_results,
            'search_title' => $this->search
        ]);
    }
}
