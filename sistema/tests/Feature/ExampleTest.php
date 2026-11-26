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
        // Sin iniciar sesión, el inicio redirige al login
        $this->get('/')->assertRedirect(route('login'));

        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
