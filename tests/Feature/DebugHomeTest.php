<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebugHomeTest extends TestCase
{
    public function test_debug_home_route(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/');
    }
}
