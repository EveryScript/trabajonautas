<?php

namespace App\Livewire\Announcement;

use App\Livewire\Forms\AnnouncementForm;
use App\Models\Announcement;
use App\Models\Area;
use App\Models\Company;
use App\Models\Location;
use App\Models\NotificationLog;
use App\Models\Profesion;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormAnnouncement extends Component
{
    use WithFileUploads;

    public $id; // Edit
    public AnnouncementForm $announcement;

    public function mount($id = null)
    {
        if ($id && Announcement::find($id)) {
            $this->id = $id;
            $this->announcement->edit($id);
        }
    }

    public function save()
    {

        $this->announcement->user_id = Auth::id();
        $announce_saved = $this->announcement->save();
        if ($this->announcement->pro) {
            $company_name = Company::where('id', $this->announcement->company_id)->first()->company_name;
            $notifier = new FirebaseNotificationService();
            $clients = User::role(env('CLIENT_ROLE'))
                // Clients PRO-MAX and with TOKEN registered
                ->whereHas('account', fn($query) => $query
                    ->where('account_type_id', 3)->whereNotNull('device_token'))
                // Clients with same announcement location
                ->whereHas('location', fn($query) => $query
                    ->whereIn('location_id', $this->announcement->locations))
                // Clients with just one announcement profesion
                ->whereHas('profesion', fn($query)  => $query
                    ->whereIn('profesion_id', $this->announcement->profesions))
                ->get();
            $array_tokens = $clients->pluck('account.device_token')->toArray();
            if ($array_tokens !== [])
                $notifier->sendBatchTokens($array_tokens, $announce_saved->id, $company_name);
        }
        $this->redirectRoute('announcement', navigate: true);
    }

    public function update()
    {
        $this->announcement->update($this->id);
        if ($this->announcement->pro) {
            $company_name = Company::where('id', $this->announcement->company_id)->first()->company_name;
            $notifier = new FirebaseNotificationService();
            $clients = User::role(env('CLIENT_ROLE'))
                // Clients PRO-MAX and with TOKEN registered
                ->whereHas('account', fn($query) => $query
                    ->where('account_type_id', 3)->whereNotNull('device_token'))
                // Clients with same announcement location
                ->whereHas('location', fn($query) => $query
                    ->whereIn('location_id', $this->announcement->locations))
                // Clients with just one announcement profesion
                ->whereHas('profesion', fn($query)  => $query
                    ->whereIn('profesion_id', $this->announcement->profesions))
                ->get();
            $array_tokens = $clients->pluck('account.device_token')->toArray();
            if ($array_tokens !== [])
                $notifier->sendBatchTokens($array_tokens, $this->id, $company_name);
        }
        $this->redirectRoute('announcement', navigate: true);
    }

    public function sendUnnotifiedClients()
    {
        $today = Carbon::today();
        $all_clients = User::role(env('CLIENT_ROLE'))
            ->whereHas('account', fn($query) => $query
                ->where('account_type_id', 3)->whereNotNull('device_token'))
            ->get();

        $notified_clients = NotificationLog::whereDate('sent_at', $today)->get();

        $all_tokens = $all_clients->pluck('account.device_token')->toArray();
        $notified_tokens = $notified_clients->pluck('device_token')->toArray();

        $unnotified_tokens = array_diff($all_tokens, $notified_tokens);

        if (!empty($unnotified_tokens)) {
            $notifier = new FirebaseNotificationService();
            $notifier->sendUnnotifiedTokens($unnotified_tokens);
        }
    }

    public function render()
    {
        $areas = Area::all(['id', 'area_name']);
        $locations = Location::all(['id', 'location_name']);
        $companies = Company::all(['id', 'company_name']);
        $profesions = Profesion::with('area:id')->get();
        return view('livewire.announcement.form-announcement', [
            'areas' => $areas,
            'locations' => $locations,
            'companies' => $companies,
            'profesions' => $profesions,
        ]);
    }
}
