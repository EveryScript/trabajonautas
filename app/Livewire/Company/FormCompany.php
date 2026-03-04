<?php

namespace App\Livewire\Company;

use App\Livewire\Forms\CompanyForm;
use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormCompany extends Component
{
    use WithFileUploads;

    public $id; // Edit
    public CompanyForm $form;
    public $preview_image;

    public function mount(Company $company)
    {
        if ($company->exists) {
            $this->form->setCompany($company);
            $this->preview_image = $this->form->company_image;
        }
    }

    public function save()
    {
        $this->form->user_id = Auth::id();
        $this->form->save();
        Cache::forget('companies_list'); // Invalid cache for "companies_list"
        $this->redirectRoute('company', navigate: true);
    }

    #[Computed]
    public function companyTypes()
    {
        return Cache::remember('company_types_list', 86400, fn() => CompanyType::all(['id', 'company_type_name']));
    }

    public function render()
    {
        return view('livewire.company.form-company', [
            'company_types' => $this->companyTypes
        ]);
    }
}
