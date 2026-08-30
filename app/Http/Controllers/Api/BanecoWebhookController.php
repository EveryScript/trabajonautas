<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Subscription;
use App\Models\TbnSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BanecoWebhookController extends Controller
{
    public function __construct()
    {
        request()->headers->set('ngrok-skip-browser-warning', 'true'); // Disable Warning Screen from ngrok
    }

    public function handlePayment(Request $request)
    {
        Log::info('Baneco webhook recibido', $request->all());
        $qrId = $request->input('payment.qrId');
        // Coins to PRO-MAX client
        $tbn_coins = TbnSetting::where('key', 'tbn_coins')->first();
        $coins = (int) $tbn_coins->value;

        if (!$qrId) {
            Log::warning('Baneco webhook: qrId no encontrado en el payload');
            return response()->json(['responseCode' => 1, 'message' => 'qrId requerido']);
        }

        // Looking for pending subscription by qr_id
        $subscription = Subscription::where('qr_id', $qrId)
            ->where('verified_payment', false)
            ->first();

        if (!$subscription) {
            Log::warning('Baneco webhook: suscripción no encontrada o ya procesada', [
                'qr_id' => $qrId
            ]);
            return response()->json(['responseCode' => 0, 'message' => '']);
        }

        // Activate subscription
        try {
            DB::transaction(function () use ($subscription, $request, $coins) {
                // Confirm subcription
                $subscription->update([
                    'verified_payment' => true,
                    'verified_by_user_id' => null, // null = AUTO activated
                    'qr_image' => null,
                ]);

                // Update or create account
                $duration_days = AccountType::where('id', $subscription->account_type_id)->value('duration_days');
                $subscription->user->account()->updateOrCreate(['user_id' => $subscription->user_id], [
                    'account_type_id' => $subscription->account_type_id,
                    'limit_time' => now()->addDays($duration_days)
                ]);
                Log::info('Debug coins', [
                    'account_type_id' => $subscription->account_type_id,
                    'coins'           => $coins,
                    'user_id'         => $subscription->user_id,
                ]);

                // Set Coins (PRO-MAX)
                if ($subscription->account_type_id == 3)
                    User::where('id', $subscription->user_id)->update(['coins' => $coins]);

                // Success information
                Log::info('Suscripción activada automáticamente via QR', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'qr_id' => $subscription->qr_id,
                    'amount' => $request->input('payment.amount'),
                ]);
            });
        } catch (\Exception $e) {
            // Try again if error
            Log::error('Error al activar suscripción via webhook', [
                'qr_id' => $qrId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['responseCode' => 1, 'message' => 'Error interno']);
        }

        // Return Baneco format
        return response()->json(['responseCode' => 0, 'message' => '']);
    }
}
