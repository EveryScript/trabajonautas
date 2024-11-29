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
        $companies = Company::orderBy('company_name', 'ASC')
            ->when($this->search, fn($query)  => $query->where('company_name', 'LIKE', '%' . $this->search . '%'))
            ->simplePaginate(7);
        return view('livewire.company.list-company', compact('companies'));
    }
}
