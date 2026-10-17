<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->get(route('health'))
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }
}
