<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_home_page_is_rendered_in_polish_when_session_locale_is_polish(): void
    {
        $this->withSession(['locale' => 'pl'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('lang="pl"', false)
            ->assertSee('Monitorowanie walut')
            ->assertSee('Zaloguj się');
    }

    public function test_users_can_change_locale_with_the_language_switcher_route(): void
    {
        $response = $this
            ->withoutMiddleware(ValidateCsrfToken::class)
            ->from(route('home'))
            ->post(route('locale.update', 'pl'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('locale', 'pl');
    }
}
