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
        $company = Company::findOrFail($id);
        $company->delete();
    }

    public function restore($id)
    {
        $company = Company::withTrashed()->find($id);
        $company->restore();
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset page when searching
    }

    public function render()
    {
        $query = Company::withTrashed()->with(['companyType:id,company_type_name'])
            ->select(['id', 'company_name', 'description', 'company_image', 'company_type_id', 'updated_at', 'deleted_at'])
            ->orderBy('company_name', 'ASC');

        if (!empty($this->search))
            $query->where('company_name', 'LIKE', '%' . $this->search . '%');

        $companies = $query->simplePaginate(8);

        return view('livewire.company.list-company', [
            'companies' => $companies,
            'count_results' => $companies->count()
        ]);
    }
}
