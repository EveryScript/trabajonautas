<?php

namespace App\Livewire\Web;

use App\Models\Notice;
use Livewire\Component;

class RecentNotices extends Component
{
    public function render()
    {
        $notices = Notice::orderBy('updated_at', 'DESC')->get();
        return view('livewire.web.recent-notices', [
            'notices' => $notices
        ]);
    }
}
