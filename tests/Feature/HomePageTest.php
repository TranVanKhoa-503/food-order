<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_a_successful_response_with_an_empty_database(): void
    {
        $this->get('/')
            ->assertOk();
    }
}
