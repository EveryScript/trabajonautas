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
    public $announce_file;
    public $pro = false;
    public $company_id;
    public $user_id;
    public $area_id;
    public $locations;
    public $profesions;

    public function edit($id)
    {
        $announcement_edit = Announcement::find($id);
        $this->announce_title = $announcement_edit->announce_title;
        $this->description = $announcement_edit->description;
        $this->expiration_time = $announcement_edit->expiration_time;
        $this->salary = $announcement_edit->salary;
        $this->announce_file = $announcement_edit->announce_file;
        $this->pro = $announcement_edit->pro;
        $this->company_id = $announcement_edit->company_id;
        $this->user_id = $announcement_edit->user_id;
        $this->area_id = $announcement_edit->area_id;
        $this->locations = $announcement_edit->locations->pluck('id');
        $this->profesions = $announcement_edit->profesions->pluck('id');
    }

    public function update($update_id)
    {
        $this->validate([
            'announce_title' => 'required|min:10',
            'description' => 'required',
            'expiration_time' => 'required|date',
            'salary' => 'required',
            'pro' => 'boolean',
            'company_id' => 'required',
            'user_id' => 'required',
            'area_id' => 'required',
            'locations' => 'required',
            'profesions' => 'required'
        ]);
        // if ($this->announce_file)
        //     $this->announce_file = $this->announce_file->store('convocatorias', 'public');
        $announcement = Announcement::find($update_id);
        $announcement->update([
            'announce_title' => $this->announce_title,
            'description' => $this->description,
            'expiration_time' => $this->expiration_time,
            'salary' => $this->salary,
            'pro' => $this->pro,
            // 'announce_file' => $this->announce_file,
            'company_id' => $this->company_id,
            'area_id' => $this->area_id,
            'user_id' => $this->user_id
        ]);
        $announcement->locations()->sync($this->locations);
        $announcement->profesions()->sync($this->profesions);
    }

    public function save()
    {
        $this->validate([
            'announce_title' => 'required|min:10',
            'description' => 'required',
            'expiration_time' => 'required|date',
            'salary' => 'required',
            'pro' => 'boolean',
            // 'announce_file' => 'required|mimes:pdf,docx,zip|max:2000',
            'company_id' => 'required',
            'user_id' => 'required',
            'area_id' => 'required',
            'locations' => 'required',
            'profesions' => 'required'
        ]);
        if ($this->announce_file)
            $this->announce_file = $this->announce_file->store('convocatorias', 'public');
        $announcement = Announcement::create($this->only(
            'announce_title',
            'description',
            'expiration_time',
            'salary',
            'pro',
            'announce_file',
            'company_id',
            'user_id',
            'area_id'
        ));
        $announcement->locations()->attach($this->locations);
        $announcement->profesions()->attach($this->profesions);
    }

    public function validationAttributes()
    {
        return [
            'announce_title' => 'titulo',
            'description' => 'descripción',
            'expiration_time' => 'expiración',
            'salary' => 'sueldo',
            'pro' => 'PRO',
            'announce_file' => 'archivo de la convocatoria',
            'company_id' => 'empresa',
            'user_id' => 'usuario',
            'area_id' => 'area profesional',
            'locations' => 'ubicaciones',
            'profesions' => 'profesiones'
        ];
    }
}
