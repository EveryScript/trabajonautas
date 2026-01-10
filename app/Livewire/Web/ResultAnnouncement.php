<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\User;
use App\Traits\AuthorizeClients;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ResultAnnouncement extends Component
{
    use AuthorizeClients;

    public $id; // Announce id component
    public $announce_saved;

    public function mount()
    {
        $announce = Announcement::find($this->id);
        if (!$announce)
            return $this->redirect('/', true);

        if ($announce->pro && !$this->isAuthClientProVerifiedAndCurrent())
            return $this->redirect('/panel', true);
    }

    #[Computed]
    public function announcement()
    {
        return Announcement::with(['company.companyType', 'announceSuggests'])->find($this->id);
    }

    public function saveAnnounce($id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        if (!$user->myAnnounces->contains($id)) {
            $user->myAnnounces()->attach($id);
        }
    }

    public function removeAnnounce($id)
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $user = User::find(auth()->user()->id);
        $user->myAnnounces()->detach($id);
    }

    public function downloadAnnounceFiles()
    {
        if (!auth()->check())
            return $this->redirectRoute('register', navigate: true);

        $zipFileName = 'archivos_convocatoria_' . $this->id . '.zip';
        return response()->streamDownload(function () {
            $zip = new \ZipArchive;
            $tmpFile = tempnam(sys_get_temp_dir(), 'zip');
            $zip->open($tmpFile, \ZipArchive::CREATE);

            foreach ($this->announcement->announceFiles as $file) {
                $filePath = $file->url;
                $fileName = basename($filePath);
                $fileContent = Storage::disk('public')->get($filePath);
                $zip->addFromString($fileName, $fileContent);
            }

            $zip->close();
            echo file_get_contents($tmpFile);
            unlink($tmpFile);
        }, $zipFileName, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
        ]);
    }

    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d/M/Y H:m:s');
    }

    public function render()
    {
        $client = $this->getAuthClientWithAccount();
        return view('livewire.web.result-announcement', [
            'announcement' => $this->announcement,
            'suggests' => $this->announcement->announceSuggests,
            'total_locations' => Location::count(),
            'client' => $client,
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent(),
        ]);
    }
}
