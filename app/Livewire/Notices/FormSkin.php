<?php

namespace App\Livewire\Notices;

use App\Models\TbnSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormSkin extends Component
{
    use WithFileUploads;

    public $bg_new_image, $thumb_new_image;

    public function save()
    {
        $this->validate([
            'bg_new_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5000',
            'thumb_new_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5000'
        ]);

        $fields = [
            'bg_new_image' => 'bg_web_image',
            'thumb_new_image' => 'thumb_web_image'
        ];

        foreach ($fields as $property => $key) {
            if ($this->$property)
                $this->processAndSaveImage($property, $key);
        }

        $this->reset(['bg_new_image', 'thumb_new_image']);
        $this->dispatch('skin-saved');
    }

    private function processAndSaveImage($property, $key)
    {
        $setting = TbnSetting::where('key', $key)->first();

        if ($setting && $setting->value)
            Storage::disk('public')->delete($setting->value);
        $path = $this->$property->store('img', 'public');

        TbnSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $path]
        );
    }

    public function render()
    {
        return view('livewire.notices.form-skin', [
            'bg_web_image' => TbnSetting::where('key', 'bg_web_image')->first(),
            'thumb_web_image' => TbnSetting::where('key', 'thumb_web_image')->first()
        ]);
    }
}
