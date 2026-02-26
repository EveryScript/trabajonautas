<?php

namespace App\Livewire\Report;

use App\Models\TbnSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ConfigQr extends Component
{
    use WithFileUploads;

    public $qr_new_pro, $qr_new_promax;

    public function save()
    {
        $this->validate([
            'qr_new_pro'    => 'nullable|image|max:2048',
            'qr_new_promax' => 'nullable|image|max:2048',
        ]);

        $fields = [
            'qr_new_pro'    => 'qr_pro',
            'qr_new_promax' => 'qr_promax',
        ];

        foreach ($fields as $property => $key) {
            if ($this->$property) {
                $this->processAndSaveImage($property, $key);
            }
        }
        $this->reset(['qr_new_pro', 'qr_new_promax']);
        $this->dispatch('close-modal');
    }

    private function processAndSaveImage($property, $key)
    {
        $setting = TbnSetting::where('key', $key)->first();

        if ($setting && $setting->value)
            Storage::disk('public')->delete($setting->value);
        $path = $this->$property->store('ajustes', 'public');

        TbnSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $path]
        );
    }
    
    public function render()
    {
        return view('livewire.report.config-qr', [
            'qr_pro' => TbnSetting::where('key', 'qr_pro')->first(),
            'qr_promax' => TbnSetting::where('key', 'qr_promax')->first()
        ]);
    }
}
