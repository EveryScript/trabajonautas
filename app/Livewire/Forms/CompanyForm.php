<?php

namespace App\Livewire\Forms;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CompanyForm extends Form
{
    public ?Company $company = null;

    public $company_name;
    public $description;
    public $company_image;
    public $user_id;
    public $company_type_id = 1;

    public function setCompany(Company $company)
    {
        $this->company = $company;

        $this->fill($company->only([
            'company_name',
            'description',
            'company_image',
            'user_id',
            'company_type_id'
        ]));
    }

    public function save()
    {
        $this->validate();

        if ($this->company_image && !is_string($this->company_image))
            $this->company_image = $this->company_image->store('empresas', 'public');

        if ($this->company) {
            $this->company->update($this->except(['company']));
        } else {
            Company::create($this->except(['company']));
        }

        $this->reset();
    }

    public function rules()
    {
        return [
            'company_name' => [
                'required',
                'min:2',
                Rule::unique('companies', 'company_name')->ignore($this->company?->id),
            ],
            'description'     => 'required',
            'user_id'         => 'required',
            'company_type_id' => 'required',
            'company_image'   => $this->company_image && is_string($this->company_image) ? 'nullable' : 'required|mimes:jpg,jpeg,png,webp|max:4000'
        ];
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
