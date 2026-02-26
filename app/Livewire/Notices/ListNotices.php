<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListNotices extends Component
{
    use WithPagination;


    public function delete($id)
    {
        $notice = Notice::find($id);
        if ($notice) {
            $notice->delete();
            if (Storage::disk('public')->exists($notice->image) && $notice->image != 'noticias/default.webp')
                Storage::disk('public')->delete($notice->image);
        }
    }

    #[On('notice-saved')]
    public function render()
    {
        $notices = Notice::orderBy('updated_at', 'DESC')->simplePaginate(6);
        

        return view('livewire.notices.list-notices', [
            'notices' => $notices
        ]);
    }
}
