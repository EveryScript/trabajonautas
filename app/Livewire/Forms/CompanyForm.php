<?php

namespace App\Livewire\Forms;

use App\Models\Company;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CompanyForm extends Form
{
    #[Validate('required|min:2')]
    public $company_name;

    #[Validate('required')]
    public $description;

    #[Validate('required')]
    public $company_image;

    #[Validate('required')]
    public $user_id;
    
    #[Validate('required')]
    public $company_type_id = 1;

    public function edit($edit_id)
    {
        $company_edit = Company::find($edit_id);
        $this->company_name = $company_edit->company_name;
        $this->description = $company_edit->description;
        $this->company_image = $company_edit->company_image;
        $this->user_id = $company_edit->user_id;
        $this->company_type_id = $company_edit->company_type_id;
    }

    public function update($update_id)
    {
        $company = Company::find($update_id);
        $company->update([
            'company_name' => $this->company_name,
            'description' => $this->description,
            'company_image' => $this->company_image,
            'user_id' => $this->user_id,
            'company_type_id' => $this->company_type_id
        ]);
    }

    public function save()
    {
        $this->validate();
        $company_saved = Company::create(
            $this->only('company_name', 'description', 'company_image', 'user_id', 'company_type_id')
        );
    }

    public function validationAttributes()
    {
        return [
            'company_name' => 'nombre de empresa',
            'description' => 'descripción',
            'company_image' => 'logotipo',
            'company_type_id' => 'tipo de empresa'
        ];
    }
}
