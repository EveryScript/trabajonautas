<?php

namespace App\Livewire\Notices;

use App\Livewire\Forms\NoticeForm;
use App\Models\Notice;
use App\Models\TbnSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListNotices extends Component
{
    use WithFileUploads;
    use WithPagination;

    public NoticeForm $notice;
    public $bg_new_image, $thumb_new_image;

    public function save()
    {
        $this->notice->user_id = Auth::id();
        $this->notice->save();
        $this->dispatch('notice-saved');
        $this->notice->reset();
    }

    public function delete($id)
    {
        $this->notice->delete($id);
    }

    public function saveBgImage()
    {
        $this->validate([
            'bg_new_image' => 'required|image|max:2048'
        ]);
        $image_path = $this->bg_new_image->store('img', 'public');
        $tbn_setting = TbnSetting::firstOrNew(['key' => 'bg_web_image']);
        $tbn_setting->value = $image_path;
        $tbn_setting->save();
        $this->reset('bg_new_image');
        $this->dispatch('image-saved');
    }

    public function saveThumbImage()
    {
        $this->validate([
            'thumb_new_image' => 'required|image|max:2048'
        ]);
        $image_path = $this->thumb_new_image->store('img', 'public');
        $tbn_setting = TbnSetting::firstOrNew(['key' => 'thumb_web_image']);
        $tbn_setting->value = $image_path;
        $tbn_setting->save();
        $this->reset('thumb_new_image');
        $this->dispatch('image-saved');
    }

    #[On('notice-saved')]
    public function render()
    {
        $notices = Notice::orderBy('updated_at', 'DESC')->simplePaginate(6);
        $bg_web_image = TbnSetting::where('key', 'bg_web_image')->first();
        $thumb_web_image = TbnSetting::where('key', 'thumb_web_image')->first();

        return view('livewire.notices.list-notices', [
            'notices' => $notices,
            'bg_web_image' => $bg_web_image,
            'thumb_web_image' => $thumb_web_image
        ]);
    }
}
