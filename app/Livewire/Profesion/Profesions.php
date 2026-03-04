<?php

namespace App\Livewire\Profesion;

use App\Livewire\Forms\ProfesionForm;
use App\Models\Area;
use App\Models\Profesion;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Profesions extends Component
{
    use WithPagination;

    public $search;
    public $count_results;

    public function editProfesion($id)
    {
        $this->dispatch('load-profesion', id: $id)->to(FormProfesion::class);
    }

    public function delete($id)
    {
        $profesion = Profesion::findOrFail($id);
        $profesion->delete();
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset page when searching
    }

    #[On('profesion-saved')]
    public function refreshList() {
        $this->resetPage(); // Reset page when searching
    }

    public function render()
    {
        $query = Profesion::with('area:id,area_name')
            ->select(['id', 'profesion_name', 'area_id', 'updated_at'])
            ->orderBy('updated_at', 'DESC');

        if (!empty($this->search))
            $query->where('profesion_name', 'LIKE', '%' . $this->search . '%');

        $profesions = $query->simplePaginate(8);

        return view('livewire.profesion.profesions', [
            'profesions' => $profesions,
            'count_results' => $this->count_results,
        ]);
    }
}
