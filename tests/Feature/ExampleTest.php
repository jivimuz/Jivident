<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every request (even to "/") goes through the global license gate first,
     * so a guest without an activated license is always redirected to the
     * license screen rather than ever seeing a 200 here.
     */
    public function test_guest_without_license_is_redirected_to_license_screen(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('license'));
    }
}
