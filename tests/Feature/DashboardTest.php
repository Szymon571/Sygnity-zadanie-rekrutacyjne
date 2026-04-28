<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                        ['currency' => 'euro', 'code' => 'EUR', 'mid' => 4.5678],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'forint wegierski', 'code' => 'HUF', 'mid' => 0.0112],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-10', 'cena' => 412.15],
                ['data' => '2026-04-11', 'cena' => 413.20],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk()
            ->assertSee('USD')
            ->assertSee('EUR')
            ->assertSee('412,15')
            ->assertSee('No watched currencies yet.')
            ->assertSee('Check rate');
    }

    public function test_latest_gold_price_card_uses_the_previous_quote_for_change(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-24', 'cena' => 549.14],
                ['data' => '2026-04-27', 'cena' => 547.28],
                ['data' => '2026-04-28', 'cena' => 546.61],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('546,61')
            ->assertSee('Compared with previous quote: -0,67 PLN');
    }

    public function test_users_can_search_for_a_currency_rate_from_dashboard(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-10', 'cena' => 412.15],
            ], 200),
            'api.nbp.pl/api/exchangerates/rates/a/usd/2026-04-10/' => Http::response([
                'table' => 'A',
                'currency' => 'dolar amerykanski',
                'code' => 'USD',
                'rates' => [
                    [
                        'effectiveDate' => '2026-04-10',
                        'mid' => 4.1357,
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard', [
            'currency' => 'USD',
            'date' => '2026-04-10',
        ]));

        $response->assertOk()
            ->assertSee('4,1357 PLN')
            ->assertSee('USD - dolar amerykanski');
    }

    public function test_users_can_search_for_a_currency_rate_from_the_previous_published_day(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-10', 'cena' => 412.15],
            ], 200),
            'api.nbp.pl/api/exchangerates/rates/a/usd/2026-04-11/' => Http::response([], 404),
            'api.nbp.pl/api/exchangerates/rates/a/usd/2026-04-10/' => Http::response([
                'table' => 'A',
                'currency' => 'dolar amerykanski',
                'code' => 'USD',
                'rates' => [
                    [
                        'effectiveDate' => '2026-04-10',
                        'mid' => 4.1357,
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard', [
            'currency' => 'USD',
            'date' => '2026-04-11',
        ]));

        $response->assertOk()
            ->assertSee('4,1357 PLN')
            ->assertSee('Date 2026-04-10.')
            ->assertSee('USD - dolar amerykanski');
    }

    public function test_dashboard_renders_fixed_toast_notification_from_session(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-10', 'cena' => 412.15],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this
            ->withSession([
                'status' => 'Currency added to the watchlist.',
                'status_variant' => 'added',
            ])
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Currency added to the watchlist.')
            ->assertSee('fixed inset-x-4 bottom-4', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Close notification');
    }

    public function test_users_can_add_and_list_watched_currency_from_dashboard(): void
    {
        Http::fake([
            'api.nbp.pl/api/exchangerates/tables/a/*' => Http::response([
                [
                    'rates' => [
                        ['currency' => 'dolar amerykanski', 'code' => 'USD', 'mid' => 4.1234],
                    ],
                ],
            ], 200),
            'api.nbp.pl/api/exchangerates/tables/b/*' => Http::response([
                [
                    'rates' => [],
                ],
            ], 200),
            'api.nbp.pl/api/cenyzlota/*' => Http::response([
                ['data' => '2026-04-10', 'cena' => 412.15],
            ], 200),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('watched-currencies.store'), [
            'currency_code' => 'USD',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('watched_currencies', [
            'user_id' => $user->id,
            'currency_code' => 'USD',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('USD')
            ->assertSee('dolar amerykanski')
            ->assertSee('4,1234 PLN');
    }

    public function test_users_can_remove_their_watched_currency(): void
    {
        $user = User::factory()->create();
        $watchedCurrency = $user->watchedCurrencies()->create([
            'currency_code' => 'USD',
        ]);

        $this->actingAs($user);

        $this->delete(route('watched-currencies.destroy', $watchedCurrency))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('watched_currencies', [
            'id' => $watchedCurrency->id,
        ]);
    }

    public function test_users_cannot_remove_another_users_watched_currency(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $watchedCurrency = $otherUser->watchedCurrencies()->create([
            'currency_code' => 'USD',
        ]);

        $this->actingAs($user);

        $this->delete(route('watched-currencies.destroy', $watchedCurrency))
            ->assertNotFound();

        $this->assertDatabaseHas('watched_currencies', [
            'id' => $watchedCurrency->id,
        ]);
    }
}
