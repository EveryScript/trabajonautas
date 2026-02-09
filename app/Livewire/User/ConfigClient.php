<?php

namespace App\Livewire\User;

use App\Mail\RenewAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class ConfigClient extends Component
{
    // Parameter    
    public $view_client = null;
    // Form propeties
    public $client_id;
    public $verified_payment;
    public $client_actived;

    public function mount()
    {
        Carbon::setlocale('es');
    }

    #[On('load-client')]
    public function loadClient($id)
    {
        $this->client_id = $id;
        $this->hydrateClient();
        $this->dispatch('client-loaded');
    }

    public function hydrateClient()
    {
        $this->view_client = User::select('id', 'name', 'email', 'actived', 'phone', 'register_completed', 'location_id', 'profesion_id', 'deleted_at')
            ->withTrashed()
            ->with([
                'latestPendingSubscription.type:id,name,price',
                'account:id,user_id,account_type_id,limit_time,device_token,updated_at',
                'account.type:id,name,price',
                'location:id,location_name',
                'profesion:id,profesion_name',
            ])->find($this->client_id);

        if ($this->view_client) {
            $this->verified_payment = $this->view_client->latestPendingSubscription?->verified_payment;
            $this->client_actived = $this->view_client->actived;
        }
    }

    public function saveClient()
    {
        try {
            DB::transaction(function () {
                if ($this->view_client->latestPendingSubscription) {
                    $ps = $this->view_client->latestPendingSubscription;
                    // Create or update current account
                    $this->view_client->account()->updateOrCreate(['user_id' => $this->view_client->id], [
                        'account_type_id' => $ps->account_type_id,
                        'limit_time' => now()->addDays($ps->type->duration_days)
                    ]);
                    // Verify subscription
                    $ps->update([
                        'user_id' => $this->view_client->id,
                        'account_type_id' => $ps->account_type_id,
                        'price' => $ps->type->price,
                        'verified_payment' => $this->verified_payment,
                        'verified_by_user_id' => auth()->user()->id
                    ]);

                    // Send email "Account approved"
                    Mail::to($this->view_client->email)->queue(new RenewAccount($this->view_client, $ps->type->name));

                    // Alert
                    $this->dispatch('client-saved', [
                        'name' => $this->view_client->name,
                        'phone' => $this->view_client->phone,
                        'type' => $ps->type->name,
                    ]);
                } else {
                    // Alert
                    $this->dispatch('client-saved', [
                        'name' => $this->view_client->name,
                        'phone' => $this->view_client->phone,
                        'type' => $this->view_client->account->type->name,
                    ]);
                }
                // Client active
                $this->view_client->update([
                    'actived' => $this->client_actived
                ]);
                // Refresh client data
                $this->hydrateClient();
            });
        } catch (\Exception $e) {
            $this->dispatch('client-error');
        }
    }

    public function render()
    {
        return view('livewire.user.config-client');
    }
}
