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
    public $preview_image;
    public CompanyForm $company;
    public $company_types;

    public function mount($id = null)
    {
        if ($id && Company::find($id)) {
            $this->id = $id;
            $this->company->edit($id);
            $this->preview_image = $this->company->company_image;
            $this->company->company_image = null;
        }
    }

    public function update()
    {
        $this->company->update($this->id);
        $this->redirectRoute('company', navigate: true);
    }

    public function save()
    {
        $this->company->user_id = Auth::id();
        $this->company->save();
        $this->redirectRoute('company', navigate: true);
    }

    public function render()
    {
        $this->company_types = CompanyType::all(['id', 'company_type_name']);
        return view('livewire.company.form-company');
    }
}
