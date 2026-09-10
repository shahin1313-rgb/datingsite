<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class InterfaceConsistencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_supported_language_switches_are_available(): void
    {
        foreach (['fa', 'en', 'fr'] as $locale) {
            $this->from('/login')
                ->get(route('language.switch', ['lang' => $locale]))
                ->assertRedirect('/login')
                ->assertSessionHas('locale', $locale);
        }

        $this->get('/lang/de')->assertNotFound();
    }

    public function test_authenticated_navigation_uses_home_instead_of_public_landing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('home').'"', false);
    }

    public function test_search_form_exposes_every_backend_filter(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('search'))
            ->assertOk()
            ->assertSee('name="interested_in"', false)
            ->assertSee('value="married"', false)
            ->assertSee('value="widowed"', false)
            ->assertSee('value="sport"', false)
            ->assertSee('value="travel"', false)
            ->assertSee('value="books"', false)
            ->assertSee('value="party"', false);
    }

    public function test_search_validation_errors_are_visible(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('search'))
            ->followingRedirects()
            ->get(route('search', [
                'min_age' => 40,
                'max_age' => 30,
            ]))
            ->assertOk()
            ->assertSee(__('ui.age_range_error'));
    }

    public function test_displayed_age_is_calculated_from_birth_year(): void
    {
        Carbon::setTestNow('2036-06-15 12:00:00');

        try {
            $user = new User([
                'age' => 21,
                'birth_year' => 2000,
            ]);

            $this->assertSame(36, $user->age);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_legacy_age_remains_available_without_birth_year(): void
    {
        $user = new User([
            'age' => 29,
            'birth_year' => null,
        ]);

        $this->assertSame(29, $user->age);
    }

    public function test_admin_message_modal_uses_message_column(): void
    {
        $message = new Message([
            'message' => 'FULL-MESSAGE-FROM-CORRECT-COLUMN',
        ]);
        $message->id = 10;
        $message->created_at = now();
        $message->setRelation('sender', new User(['name' => 'Sender']));
        $message->setRelation('receiver', new User(['name' => 'Receiver']));

        $messages = new LengthAwarePaginator(
            collect([$message]),
            1,
            20,
            1,
            ['path' => route('admin.messages')]
        );

        $html = view('admin.messages.index', [
            'messages' => $messages,
            'errors' => new ViewErrorBag(),
        ])->render();

        $this->assertStringContainsString(
            'FULL-MESSAGE-FROM-CORRECT-COLUMN',
            $html
        );
    }

    public function test_admin_menu_only_references_real_routes(): void
    {
        $items = collect(config('adminlte.menu'));

        $this->assertFalse($this->menuContainsPlaceholder($items->all()));

        foreach ($items->pluck('route')->filter() as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Admin menu route [{$routeName}] is not registered."
            );
        }
    }

    /**
     * @param array<int, mixed> $items
     */
    private function menuContainsPlaceholder(array $items): bool
    {
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (($item['url'] ?? null) === '#') {
                return true;
            }

            if (
                isset($item['submenu']) &&
                $this->menuContainsPlaceholder($item['submenu'])
            ) {
                return true;
            }
        }

        return false;
    }
}
