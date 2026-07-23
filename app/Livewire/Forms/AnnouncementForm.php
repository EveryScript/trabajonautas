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
    public $announce_files = [];
    public $pro = false;
    public $scheduled_at;
    public $notification_sent = false;
    public $company_id;
    public $user_id;
    public $locations;
    public $profesions;
    public $current_files;

    public function edit($id)
    {
        $announcement_edit = Announcement::findOrFail($id);
        $this->announce_title = $announcement_edit->announce_title;
        $this->description = $announcement_edit->description;
        $this->expiration_time = $announcement_edit->expiration_time;
        $this->salary = $announcement_edit->salary;
        $this->pro = $announcement_edit->pro;
        $this->scheduled_at = $announcement_edit->scheduled_at;
        $this->company_id = $announcement_edit->company_id;
        $this->user_id = $announcement_edit->user_id;
        $this->locations = $announcement_edit->locations->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->profesions = $announcement_edit->profesions->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->current_files = $announcement_edit->announceFiles;
    }

    public function update($update_id)
    {
        $this->normalizeRelationSelections();

        $this->validate([
            'announce_title' => 'required|min:10|max:1200',
            'description' => 'required',
            'expiration_time' => 'required|date|after:now',
            'salary' => 'required|numeric|min:0',
            'pro' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now|before:expiration_time',
            'announce_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,xlsx,xlsm,xls,csv|max:30000',
            'company_id' => 'required|integer|exists:companies,id',
            'locations' => 'required|array|min:1',
            'locations.*' => 'integer|distinct|exists:locations,id',
            'profesions' => 'required|array|min:1',
            'profesions.*' => 'integer|distinct|exists:profesions,id',
        ]);
        $announcement = Announcement::findOrFail($update_id);
        $announcement->update([
            'announce_title' => $this->announce_title,
            'description' => $this->description,
            'expiration_time' => $this->expiration_time,
            'salary' => str_replace('.', '', $this->salary),
            'pro' => $this->pro,
            'scheduled_at' => $this->pro && $this->scheduled_at ? $this->scheduled_at : null,
            'company_id' => $this->company_id,
        ]);
        $announcement->locations()->sync($this->locations);
        $announcement->profesions()->sync($this->profesions);
        // Delete current files and update
        if ($this->announce_files) {
            $announce_files_data = [];
            foreach ($this->announce_files as $index => $file) {
                $original_name = $file->getClientOriginalName();
                $file_url = $file->storeAs(path: 'convocatorias', options: 'public', name: $index . '-' . $file->getClientOriginalName());
                $announce_files_data[] = [
                    'announcement_id' => $announcement->id,
                    'url' => $file_url,
                    'original_name' => $original_name
                ];
            }
            $announcement->announceFiles()->createMany($announce_files_data);
        }
    }

    public function save()
    {
        $this->salary = str_replace('.', '', $this->salary);
        $this->normalizeRelationSelections();

        $this->validate([
            'announce_title' => 'required|min:10|max:1200',
            'description' => 'required',
            'expiration_time' => 'required|date|after:now',
            'salary' => 'required|numeric|min:0',
            'pro' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now|before:expiration_time',
            'announce_files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx,xlsx,xlsm,xls,csv|max:30000',
            'company_id' => 'required|integer|exists:companies,id',
            'user_id' => 'required|integer|exists:users,id',
            'locations' => 'required|array|min:1',
            'locations.*' => 'integer|distinct|exists:locations,id',
            'profesions' => 'required|array|min:1',
            'profesions.*' => 'integer|distinct|exists:profesions,id',
        ]);
        $announcement = Announcement::create($this->only(
            'announce_title',
            'description',
            'expiration_time',
            'salary',
            'pro',
            'scheduled_at',
            'company_id',
            'user_id',
        ));
        $announcement->locations()->sync($this->locations);
        $announcement->profesions()->sync($this->profesions);

        $announce_files_data = [];
        if ($this->announce_files) {
            foreach ($this->announce_files as $index => $file) {
                $original_name = $file->getClientOriginalName();
                $file_url = $file->storeAs(path: 'convocatorias', options: 'public', name: $index . '-' . $file->hashName());
                $announce_files_data[] = [
                    'announcement_id' => $announcement->id,
                    'url' => $file_url,
                    'original_name' => $original_name
                ];
            }
            $announcement->announceFiles()->createMany($announce_files_data);
        }
        return $announcement;
    }

    private function normalizeRelationSelections(): void
    {
        $this->locations = $this->normalizeIds($this->locations);
        $this->profesions = $this->normalizeIds($this->profesions);
    }

    private function normalizeIds(mixed $values): array
    {
        return collect($values ?? [])
            ->filter(fn (mixed $value): bool => is_int($value)
                || (is_string($value) && ctype_digit(trim($value))))
            ->map(fn (mixed $value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function messages()
    {
        return [
            'announce_files.*.max' => 'Los archivos de la convocatoria no deben ser mayores a 30MB',
            'announce_files.*.mimes' => 'Los archivos de la convocatoria deben ser documentos o imagenes',
            'expiration_time.after' => 'La fecha de expiración debe ser superior al momento actual',
            'scheduled_at.after' => 'La fecha de programación debe ser superior al momento actual',
            'scheduled_at.before' => 'La fecha de programación debe ser antes de la fecha de expiración'
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
            'scheduled_at' => 'fecha de programación',
            'company_id' => 'empresa',
            'user_id' => 'usuario',
            'announce_files' => 'archivos de la convocatoria',
            'locations' => 'ubicaciones',
            'profesions' => 'profesiones'
        ];
    }
}
