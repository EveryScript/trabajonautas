<?php

namespace App\Livewire\User;

use App\Mail\RenewAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ConfigClient extends Component
{
    // Form propeties
    #[Locked]
    public $client_id = null;
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
        $client = $this->client;

        if ($client) {
            $this->verified_payment = $client->lastPendingSubscription?->verified_payment;
            $this->client_actived = $client->actived;
            $this->dispatch('client-loaded');
        }
    }

    #[Computed]
    public function client()
    {
        if (!$this->client_id) return null;

        return User::select('id', 'name', 'email', 'actived', 'phone', 'register_completed', 'location_id', 'profesion_id', 'deleted_at')
            ->withTrashed()
            ->with([
                'latestPendingSubscription.type:id,name,price,duration_days',
                'account:id,user_id,account_type_id,limit_time,device_token,updated_at',
                'account.type:id,name,price',
                'location:id,location_name',
                'profesion:id,profesion_name',
            ])->find($this->client_id);
    }

    public function saveClient()
    {
        $client = $this->client;
        if (!$client) return;

        try {
            DB::transaction(function () use ($client) {
                $subscription = $client->latestPendingSubscription;

                if ($subscription) {
                    // Update/Create Account
                    $client->account()->updateOrCreate(['user_id' => $client->id], [
                        'account_type_id' => $subscription->account_type_id,
                        'limit_time' => now()->addDays($subscription->type->duration_days)
                    ]);

                    // Verify subscription
                    $subscription->update([
                        'verified_payment' => $this->verified_payment,
                        'verified_by_user_id' => auth()->id()
                    ]);

                    Mail::to($client->email)->queue(new RenewAccount($client, $subscription->type->name));
                }

                $client->update(['actived' => $this->client_actived]);

                unset($this->client); // Update computed property

                $this->dispatch('client-saved', [
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'type' => $subscription ? $subscription->type->name : $client->account?->type?->name,
                ]);
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('client-error');
        }
    }

    public function render()
    {
        return view('livewire.user.config-client');
    }
}
