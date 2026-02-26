<?php

namespace App\Livewire\Notices;

use App\Livewire\Forms\NoticeForm;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormNotices extends Component
{
    use WithFileUploads;

    public NoticeForm $form;
    public $preview_image;

    public function mount(Notice $notice)
    {
        if ($notice->exists) {
            $this->form->setNotice($notice);
            $this->preview_image = $this->form->image;
        }
    }

    public function save()
    {
        $this->form->user_id = Auth::id();
        $this->form->save();
        $this->dispatch('notice-saved');
    }

    public function render()
    {
        return view('livewire.notices.form-notices');
    }
}
