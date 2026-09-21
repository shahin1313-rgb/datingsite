<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider localizedFailedLoginMessages
     */
    public function test_failed_login_message_uses_the_selected_locale(
        string $locale,
        string $expectedMessage
    ): void {
        $response = $this
            ->withSession(['locale' => $locale])
            ->from(route('login'))
            ->post(route('login'), [
                'email' => 'missing@example.com',
                'password' => 'incorrect-password',
            ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => $expectedMessage,
            ]);
    }

    public static function localizedFailedLoginMessages(): array
    {
        return [
            'Persian' => [
                'fa',
                'ایمیل یا رمز عبور واردشده صحیح نیست.',
            ],
            'English' => [
                'en',
                'The email address or password is incorrect.',
            ],
            'French' => [
                'fr',
                'L’adresse e-mail ou le mot de passe est incorrect.',
            ],
        ];
    }
}
