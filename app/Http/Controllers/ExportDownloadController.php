<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportDownloadController extends Controller
{
    public function __invoke(Request $request, string $file): StreamedResponse
    {
        abort_if(str_contains($file, '..') || !preg_match('/\.(zip|xlsx)$/', $file), 404);
        $path = storage_path('app/exports/' . $file);
        abort_unless(file_exists($path), 404, 'El archivo ya no está disponible.');
        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $file);
    }
}
