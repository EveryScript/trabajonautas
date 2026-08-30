<?php

namespace App\Console\Commands;

use App\Services\BanecoService;
use Illuminate\Console\Command;

class TestBaneco extends Command
{
    protected $signature = 'baneco:test';
    protected $description = 'Test Baneco API Connection';

    public function handle(BanecoService $service)
    {
        $this->info('Probando encriptación...');
        $encrypted = $service->encrypt('30.00');
        $this->line("  Texto encriptado: {$encrypted}");

        $this->info('Probando autenticación...');
        $token = $service->authenticate();
        $this->line("  Token obtenido: " . substr($token, 0, 40) . '...');

        $this->info('Probando generación de QR...');
        $qr = $service->generateQR(
            transactionId: 'TEST-001',
            amount: 30.00,
            description: 'Prueba suscripción PRO-MAX',
            dueDate: now()->addDay()->format('Y-m-d')
        );
        $this->line("  QR ID: {$qr['qrId']}");
        $this->line("  QR Image (primeros 40 chars): " . substr($qr['qrImage'], 0, 40) . '...');

        $this->info('✅ Todo OK. El servicio está conectado correctamente.');
    }
}
