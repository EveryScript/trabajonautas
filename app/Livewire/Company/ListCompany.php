<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class ListCompany extends Component
{
    use WithPagination;

    public $search;

    public function edit($id)
    {
        return $this->redirect("/new-company?edit_id=" . $id, true);
    }

    public function delete($id)
    {
        Company::find($id)->delete();
    }

    public function render()
    {
        $base_query = Company::orderBy('company_name', 'ASC');

        $filter_query = (clone $base_query)
            ->when($this->search, fn($query)  => $query->where('company_name', 'LIKE', '%' . $this->search . '%'));

        $count_results = $filter_query->count();

        $companies = $count_results > 0
            ? $filter_query->simplePaginate(7)
            : $base_query->simplePaginate(7);
            
        return view('livewire.company.list-company', [
            'companies' => $companies,
            'count_results' => $count_results,
            'search' => $this->search
        ]);
    }
}
