<?php

namespace App\Livewire\Forms;

use App\Models\Company;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CompanyForm extends Form
{
    public $company_name;
    public $description;
    public $company_image;
    public $user_id;
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
        $rules = [
            'company_name' => 'required|min:2|unique:companies,company_name',
            'description' => 'required',
            'user_id' => 'required',
            'company_type_id' => 'required'
        ];
        if ($this->company_image)
            $rules['company_image'] = 'required|image|mimes:jpg,jpeg,png|max:4000';
        $this->validate($rules);

        $update_data = [
            'company_name' => $this->company_name,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'company_type_id' => $this->company_type_id
        ];
        if ($this->company_image) {
            $this->company_image = $this->company_image->store('empresas', 'public');
            $update_data['company_image'] = $this->company_image;
        }
        $company->update($update_data);
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|min:2|unique:companies,company_name',
            'description' => 'required',
            'company_image' => 'required|image|mimes:jpg,jpeg,png|max:4000',
            'user_id' => 'required',
            'company_type_id' => 'required'
        ]);
        $this->company_image = $this->company_image->store('empresas', 'public');
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
