<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ResultAnnouncement extends Component
{
    public $id; // Announce id component
    public $client, $pro_verified = false;

    public function mount($id = null)
    {
        if ($id && Announcement::find($id)) {
            $this->id = $id;
            if (auth()->check()) {
                if (auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')) {
                    $this->client = User::with('account.accountType')->find(auth()->user()->id);
                    $this->pro_verified = $this->client->account->verified_payment;
                    if ($this->client->account->limit_time) {
                        $limit_time = Carbon::parse($this->client->account->limit_time);
                        if ($limit_time->isBefore(Carbon::now()))
                            $this->redirect('/panel', true);
                    }
                    if (Announcement::find($id)->pro && $this->client->account->account_type_id == 1)
                        $this->redirect('/pro', true);
                }
            } elseif (Announcement::find($id)->pro) {
                $this->redirect('/pro', true);
            }
        } else {
            $this->redirect('/', true);
        }
    }

    public function saveAnnounce($id)
    {
        if (auth()->check()) {
            $user = User::find(auth()->user()->id);
            if (!$user->myAnnounces->contains($id)) {
                $user->myAnnounces()->attach($id);
            }
        } else {
            $this->redirectRoute('register', navigate: true);
        }
    }

    public function removeAnnounce($id)
    {
        if (auth()->check()) {
            $user = User::find(auth()->user()->id);
            $user->myAnnounces()->detach($id);
        } else {
            $this->redirectRoute('register', navigate: true);
        }
    }

    public function downloadAnnounceFiles()
    {
        $announcement = Announcement::with('announceFiles')->find($this->id);
        $zipFileName = 'archivos_convocatoria_' . $announcement->id . '.zip';
        return response()->streamDownload(function () use ($announcement) {
            $zip = new \ZipArchive;
            $tmpFile = tempnam(sys_get_temp_dir(), 'zip');
            $zip->open($tmpFile, \ZipArchive::CREATE);

            foreach ($announcement->announceFiles as $file) {
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

    public function render()
    {
        $user = auth()->check() ? User::find(auth()->user()->id) : null;
        $announcement = Announcement::with('company.companyType')->find($this->id);
        $suggests = Announcement::whereHas('area', fn($query) => $query->where('id', $announcement->area->id))
            ->where('id', '<>', $announcement->id)
            ->where('expiration_time', '>=', now())
            ->get();
        // $share_buttons = (app(ShareButtons::class))->page(FacadesURL::full(), 'Trabajonautas tiene una convocatoria para ti')
        //     ->whatsapp()
        //     ->telegram()
        //     ->render();
        return view('livewire.web.result-announcement', compact(
            'announcement',
            'suggests'
        ));
    }
}
