<?php

namespace App\Livewire\User;

use App\Livewire\Forms\ClientForm;
use App\Models\AccountType;
use App\Models\GradeProfile;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FormClient extends Component
{
    public $id; // Edit    
    public ClientForm $form;

    public function mount($client)
    {
        if ($client)
            $this->form->setClient(User::withTrashed()->findOrFail($client));
        else
            return $this->redirectRoute('client', navigate: true);
    }

    public function update()
    {
        $this->form->account_price = $this->accountTypes->find($this->form->account_type_id)->price;
        $this->form->update();
        return $this->redirectRoute('client', navigate: true);
    }

    public function delete()
    {
        $this->form->delete();
        return $this->redirectRoute('client', navigate: true);
    }

    public function restore()
    {
        $this->form->restore();
        return $this->redirectRoute('client', navigate: true);
    }

    public function forceDelete()
    {
        $this->form->forceDelete();
        return $this->redirectRoute('client', navigate: true);
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations', 86400, fn() => Location::all(['id', 'location_name']));
    }

    #[Computed]
    public function gradeProfiles()
    {
        return Cache::remember('grades', 86400, fn() => GradeProfile::all(['id', 'profile_name']));
    }

    #[Computed]
    public function accountTypes()
    {
        return Cache::remember('account-types', 86400, fn() => AccountType::all(['id', 'name', 'price']));
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('profesions', 86400, function () {
            return Profesion::with('areas')->get()->map(function ($p) {
                return [
                    'id' => (int) $p->id,
                    'profesion_name' => $p->profesion_name,
                    'area_ids' => $p->areas->pluck('id')->map(fn($id) => (int)$id)->toArray()
                ];
            })->toArray();
        });
    }

    public function render()
    {
        return view('livewire.user.form-client', [
            'locations' => $this->locations,
            'profesions' => $this->profesions,
            'grade_profiles' => $this->gradeProfiles,
            'account_types' => $this->accountTypes,
        ]);
    }
}
