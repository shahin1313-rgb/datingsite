<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileSearchSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_interested_in_filter_uses_existing_column(): void
    {
        $viewer = User::factory()->create();

        User::factory()->create([
            'name' => 'Sport Match',
            'interested_in' => 'sport',
        ]);

        User::factory()->create([
            'name' => 'Travel Match',
            'interested_in' => 'travel',
        ]);

        $this->actingAs($viewer)
            ->get(route('search', [
                'interested_in' => 'sport',
            ]))
            ->assertOk()
            ->assertSee('Sport Match')
            ->assertDontSee('Travel Match');
    }

    public function test_search_filters_are_validated_before_querying(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('search', [
                'city' => str_repeat('a', 101),
                'min_age' => 17,
                'max_age' => 101,
                'marital_status' => 'invalid',
                'interested_in' => 'invalid',
                'has_photo' => 'invalid',
                'is_active' => 'invalid',
                'page' => 1001,
            ]))
            ->assertSessionHasErrors([
                'city',
                'min_age',
                'max_age',
                'marital_status',
                'interested_in',
                'has_photo',
                'is_active',
                'page',
            ]);
    }

    public function test_maximum_age_cannot_be_less_than_minimum_age(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('search', [
                'min_age' => 40,
                'max_age' => 30,
            ]))
            ->assertSessionHasErrors('max_age');
    }

    public function test_search_is_limited_to_thirty_requests_per_minute(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 30) as $attempt) {
            $this->actingAs($user)
                ->get(route('search'))
                ->assertOk();
        }

        $this->actingAs($user)
            ->get(route('search'))
            ->assertStatus(429);
    }
}
