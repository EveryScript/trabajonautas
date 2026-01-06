<?php

namespace App\Livewire\Notices;

use App\Livewire\Forms\NoticeForm;
use App\Models\Notice;
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

    #[On('notice-saved')]
    public function render()
    {
        $notices = Notice::orderBy('updated_at', 'DESC')->simplePaginate(6);

        return view('livewire.notices.list-notices', [
            'notices' => $notices,
        ]);
    }
}
