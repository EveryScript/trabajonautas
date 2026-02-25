<?php

namespace App\Livewire\Company;

use App\Livewire\Forms\CompanyForm;
use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Support\Facades\Auth;
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
        $this->redirectRoute('company', navigate: true);
    }

    public function render()
    {
        return view('livewire.company.form-company', [
            'company_types' => CompanyType::all(['id', 'company_type_name'])
        ]);
    }
}
