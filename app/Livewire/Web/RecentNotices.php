<?php

namespace App\Livewire\Web;

use App\Models\Notice;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RecentNotices extends Component
{
    public function render()
    {
        $notices = Cache::remember('web-recent-notices', 900, function () {
            return Notice::select('id', 'title', 'description', 'link', 'image', 'updated_at')
                ->orderBy('updated_at', 'DESC')
                ->limit(10)
                ->get();
        });

        return view('livewire.web.recent-notices', [
            'notices' => $notices
        ]);
    }
}
