<?php

namespace App\Livewire\Forms;

use App\Models\Announcement;
use Livewire\Form;

class AnnouncementForm extends Form
{
    public $announce_title;
    public $description;
    public $expiration_time;
    public $salary;
    public $announce_files;
    public $pro = false;
    public $company_id;
    public $user_id;
    public $area_id;
    public $locations;
    public $profesions;
    public $announce_urls;

    public function edit($id)
    {
        $announcement_edit = Announcement::find($id);
        $this->announce_title = $announcement_edit->announce_title;
        $this->description = $announcement_edit->description;
        $this->expiration_time = $announcement_edit->expiration_time;
        $this->salary = $announcement_edit->salary;
        $this->pro = $announcement_edit->pro;
        $this->company_id = $announcement_edit->company_id;
        $this->user_id = $announcement_edit->user_id;
        $this->area_id = $announcement_edit->area_id;
        $this->locations = $announcement_edit->locations->pluck('id');
        $this->profesions = $announcement_edit->profesions->pluck('id');
        $this->announce_urls = $announcement_edit->announceFiles->pluck('url');
    }

    public function update($update_id)
    {
        $this->validate([
            'announce_title' => 'required|min:10',
            'description' => 'required',
            'expiration_time' => 'required|date',
            'salary' => 'required',
            'pro' => 'boolean',
            'announce_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:2000',
            'company_id' => 'required',
            'user_id' => 'required',
            'area_id' => 'required',
            'locations' => 'required',
            'profesions' => 'required'
        ]);
        $announcement = Announcement::find($update_id);
        $announcement->update([
            'announce_title' => $this->announce_title,
            'description' => $this->description,
            'expiration_time' => $this->expiration_time,
            'salary' => $this->salary,
            'pro' => $this->pro,
            'company_id' => $this->company_id,
            'area_id' => $this->area_id,
            'user_id' => $this->user_id
        ]);
        $announcement->locations()->sync($this->locations);
        $announcement->profesions()->sync($this->profesions);
        if ($this->announce_files && count($this->announce_files)) {
            $announcement->announceFiles()->delete();
            $announce_files_data = [];
            foreach ($this->announce_files as $index => $file) {
                $file_url = $file->storeAs(path: 'convocatorias', options: 'public', name: $index . '-' . $file->getClientOriginalName());
                $announce_files_data[] = [
                    'announcement_id' => $announcement->id,
                    'url' => $file_url
                ];
            }
            $announcement->announceFiles()->createMany($announce_files_data);
        }
    }

    public function save()
    {
        $this->validate([
            'announce_title' => 'required|min:10',
            'description' => 'required',
            'expiration_time' => 'required|date',
            'salary' => 'required',
            'pro' => 'boolean',
            'announce_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:2000',
            'company_id' => 'required',
            'user_id' => 'required',
            'area_id' => 'required',
            'locations' => 'required',
            'profesions' => 'required'
        ]);
        $announcement = Announcement::create($this->only(
            'announce_title',
            'description',
            'expiration_time',
            'salary',
            'pro',
            'company_id',
            'user_id',
            'area_id'
        ));
        $announcement->locations()->attach($this->locations);
        $announcement->profesions()->attach($this->profesions);
        $announce_files_data = [];
        if ($this->announce_files && count($this->announce_files)) {
            foreach ($this->announce_files as $index => $file) {
                $file_url = $file->storeAs(path: 'convocatorias', options: 'public', name: $index . '-' . $file->hashName());
                $announce_files_data[] = [
                    'announcement_id' => $announcement->id,
                    'url' => $file_url
                ];
            }
            $announcement->announceFiles()->createMany($announce_files_data);
        }
        return $announcement;
    }

    public function messages()
    {
        return [
            'announce_files.*.max' => 'Los archivos de la convocatoria no deben ser mayores a 2MB',
            'announce_files.*.mimes' => 'Los archivos de la convocatoria deben ser documentos o imagenes'
        ];
    }

    public function validationAttributes()
    {
        return [
            'announce_title' => 'titulo',
            'description' => 'descripción',
            'expiration_time' => 'expiración',
            'salary' => 'sueldo',
            'pro' => 'PRO',
            'company_id' => 'empresa',
            'user_id' => 'usuario',
            'area_id' => 'area profesional',
            'locations' => 'ubicaciones',
            'profesions' => 'profesiones'
        ];
    }
}
