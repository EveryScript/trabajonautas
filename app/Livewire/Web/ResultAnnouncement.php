<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Area;
use App\Models\CompanyType;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL as FacadesURL;
use Kudashevs\ShareButtons\ShareButtons;
use Livewire\Component;

class ResultAnnouncement extends Component
{
    public $id; // Announce id component

    public function mount($id = null)
    {
        if ($id && Announcement::find($id)) {
            $this->id = $id;
        } else {
            $this->redirect('/', true);
        }
    }

    public function saveAnnounce($id)
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            if (!$user->myAnnounces->contains($id)) {
                $user->myAnnounces()->attach($id);
            }
        } else {
            $this->redirectRoute('register', navigate: true);
        }
    }

    public function removeAnnounce($id)
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            $user->myAnnounces()->detach($id);
        } else {
            $this->redirectRoute('register', navigate: true);
        }
    }

    public function downloadFile($file)
    {
        if (Auth::check()) {
            try {
                return response()->download('storage/' . $file, 'convocatoria.pdf');
            } catch (Exception $error) {
                dump('convocatoria no disponible');
            }
        } else {
            $this->redirectRoute('register', navigate: true);
        }
    }

    public function render()
    {
        $user = Auth::check() ? User::find(Auth::user()->id) : null;
        $announcement = Announcement::with('company')->find($this->id);
        $pro_flag = true;
        // Verify PRO
        if ($announcement->pro) {
            if (!$user || $user->hasRole('FREE_CLIENT'))
                $pro_flag = false;
        }
        $suggests = Announcement::whereHas('area', fn($query) => $query->where('id', $announcement->area->id))
            ->where('id', '<>', $announcement->id)
            ->where('expiration_time', '>=', now())
            ->get();
        $company_types = CompanyType::all('id', 'company_type_name');
        $share_buttons = (app(ShareButtons::class))->page(FacadesURL::full(), 'Trabajonautas tiene una convocatoria para ti')
            ->whatsapp()
            ->telegram()
            ->render();
        return view('livewire.web.result-announcement', compact(
            'announcement',
            'suggests',
            'user',
            'share_buttons',
            'company_types',
            'pro_flag'
        ));
    }
}
