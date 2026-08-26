<?php

namespace App\Livewire\Notices;

use App\Models\TbnSetting;
use App\Support\StoragePath;
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
        $oldPath = StoragePath::normalizePublicPath($setting?->value);
        $path = $this->$property->store('ajustes', 'public');

        TbnSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $path]
        );

        if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    public function render()
    {
        $images = TbnSetting::whereIn('key', ['bg_web_image', 'thumb_web_image'])
            ->pluck('value', 'key');

        return view('livewire.notices.form-skin', [
            'bg_web_image_url' => StoragePath::existingUrl($images->get('bg_web_image')),
            'thumb_web_image_url' => StoragePath::existingUrl($images->get('thumb_web_image')),
        ]);
    }
}
