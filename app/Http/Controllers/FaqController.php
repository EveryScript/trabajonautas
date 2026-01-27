<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class FaqController extends Controller
{
    public function show()
    {
        $faqFile = Jetstream::localizedMarkdownPath('faq.md');
        return view('faq', [
            'faq' => Str::markdown(file_get_contents($faqFile)),
        ]);
    }
}
