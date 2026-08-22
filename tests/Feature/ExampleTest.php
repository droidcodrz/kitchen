<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // There is no public landing page: the root route sends guests to the
        // login screen and signed-in users to the dashboard.
        $this->get('/')->assertRedirect(route('login'));
    }
}
