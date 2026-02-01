<?php

namespace App\Livewire\Panel;

use App\Livewire\Forms\ClientForm;
use App\Models\AccountType;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\TbnSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FirstSteps extends Component
{
    public ClientForm $form;

    public $user_id;                            // Component parameter
    public $user;                               // Current user
    public $country_code = '+591';
    public $qr_image = '';

    public function mount()
    {
        $this->user = User::with(['account.type'])->select('id', 'name', 'phone')->find($this->user_id);
        $this->qr_image = TbnSetting::where('key', 'qr_image')->first();
    }

    #[Computed]
    public function locations()
    {
        return Location::select('id', 'location_name')->get();
    }

    #[Computed]
    public function profesions()
    {
        return Profesion::select('id', 'profesion_name')->get();
    }

    #[Computed]
    public function account_types()
    {
        return AccountType::select('id', 'name', 'price', 'duration_days')->get();
    }

    public function confirmAndSave()
    {
        try {
            $this->form->store($this->user, $this->country_code);
            return $this->redirectRoute('dashboard', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('register-failed');
        }
    }

    public function render()
    {
        return view('livewire.panel.first-steps', [
            'profesions' => $this->profesions,
            'locations' => $this->locations,
            'account_types' => $this->account_types
        ]);
    }
}
