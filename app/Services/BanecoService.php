<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BanecoService
{
    private string $baseUrl;
    private string $userName;
    private string $password;
    private string $aesKey;
    private string $accountNumber;

    public function __construct()
    {
        $this->baseUrl      = config('services.baneco.url');
        $this->userName     = config('services.baneco.username');
        $this->password     = config('services.baneco.password');
        $this->aesKey       = config('services.baneco.aes_key');
        $this->accountNumber = config('services.baneco.account');
    }

    // Encrypt password text using Baneco endpoint 
    public function encrypt(string $text): string
    {
        $url = "{$this->baseUrl}/api/authentication/encrypt"
            . "?text="   . urlencode($text)
            . "&aesKey=" . urlencode($this->aesKey);

        $response = $this->httpClient()->get($url);

        if ($response->failed()) {
            Log::error('Baneco encrypt failed', ['response' => $response->body()]);
            throw new \Exception('Error al encriptar datos con Baneco');
        }

        return trim($response->body(), " \t\n\r\0\x0B\"");
    }

    // Decrypt password text and delete " character
    public function decrypt(string $encryptedText): string
    {
        $url = "{$this->baseUrl}/api/authentication/decrypt"
            . "?text="   . urlencode($encryptedText)
            . "&aesKey=" . urlencode($this->aesKey);

        $response = $this->httpClient()->get($url);

        if ($response->failed()) {
            Log::error('Baneco decrypt failed', ['response' => $response->body()]);
            throw new \Exception('Error al desencriptar datos con Baneco');
        }

        return trim($response->body(), " \t\n\r\0\x0B\"");
    }

    // Autenticate Bearer Token JWT to use in request QR
    public function authenticate(): string
    {
        $encryptedPassword = $this->encrypt($this->password);

        $response = $this->httpClient()
            ->asJson()
            ->post("{$this->baseUrl}/api/authentication/authenticate", [
                'userName' => $this->userName,
                'password' => $encryptedPassword,
            ]);

        $data = $response->json();

        if ($response->failed() || ($data['responseCode'] ?? 1) !== 0) {
            Log::error('Baneco authentication failed', ['response' => $data]);
            throw new \Exception('Error de autenticación con Baneco: ' . ($data['message'] ?? 'desconocido'));
        }

        return $data['token'];
    }

    // Generate QR and return array [qr_id, qr_image] -> Base64
    public function generateQR(
        string $transactionId,
        float $amount,
        string $description,
        string $dueDate
    ): array {
        $token = $this->authenticate();
        $encryptedAccount = $this->encrypt($this->accountNumber);

        $response = $this->httpClient()
            ->withToken($token)
            ->asJson()
            ->post("{$this->baseUrl}/api/qrsimple/generateQR", [
                'transactionId'  => $transactionId,
                'accountCredit'  => $encryptedAccount,
                'currency'       => 'BOB',
                'amount'         => $amount,
                'description'    => $description,
                'dueDate'        => $dueDate,
                'singleUse'      => true,  // un solo pago por suscripción
                'modifyAmount'   => false, // el cliente no puede cambiar el monto
            ]);

        $data = $response->json();

        if ($response->failed() || ($data['responseCode'] ?? 1) !== 0) {
            Log::error('Baneco generateQR failed', [
                'transactionId' => $transactionId,
                'response'      => $data,
            ]);
            throw new \Exception('Error al generar QR con Baneco: ' . ($data['message'] ?? 'desconocido'));
        }

        return [
            'qrId'    => $data['qrId'],
            'qrImage' => $data['qrImage'],
        ];
    }

    // Use Http with verifing (SSL) if local or production
    private function httpClient()
    {
        $client = Http::acceptJson();

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }
}
