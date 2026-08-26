<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileEmailChangeSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_email_cannot_be_changed_without_current_password(): void
    {
        Notification::fake();

        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => 'changed@example.test',
                'city' => $user->city,
                'bio' => $user->bio,
            ]);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertSame(
            'original@example.test',
            $user->email
        );

        $this->assertNotNull($user->email_verified_at);

        Notification::assertNothingSent();
    }

    public function test_email_cannot_be_changed_with_wrong_password(): void
    {
        Notification::fake();

        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => 'changed@example.test',
                'city' => $user->city,
                'bio' => $user->bio,
                'current_password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors(
            'current_password'
        );

        $this->assertSame(
            'original@example.test',
            $user->fresh()->email
        );

        Notification::assertNothingSent();
    }

    public function test_email_change_with_current_password_requires_reverification(): void
    {
        Notification::fake();

        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => 'changed@example.test',
                'city' => $user->city,
                'bio' => $user->bio,
                'current_password' => 'correct-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('verification.notice'));

        $user->refresh();

        $this->assertSame(
            'changed@example.test',
            $user->email
        );

        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_other_profile_changes_do_not_require_password(): void
    {
        Notification::fake();

        $user = $this->makeUser();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'city' => $user->city,
                'bio' => $user->bio,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertSame(
            'Updated Name',
            $user->fresh()->name
        );

        Notification::assertNothingSent();
    }

    public function test_user_supports_laravel_email_verification(): void
    {
        $this->assertInstanceOf(
            MustVerifyEmail::class,
            new User()
        );
    }

    private function makeUser(): User
    {
        return User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.test',
            'password' => Hash::make(
                'correct-password'
            ),
            'gender' => 'male',
            'city' => 'Tehran',
            'bio' => 'Original bio',
            'email_verified_at' => now(),
        ]);
    }
}
