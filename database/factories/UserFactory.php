<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * رمز مشترک کاربران آزمایشی.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),

            'email' => fake()
                ->unique()
                ->safeEmail(),

            'email_verified_at' => now(),

            'password' =>
                static::$password ??=
                    Hash::make('password'),

            'remember_token' =>
                Str::random(10),

            /*
             * این دو ستون در جدول users اجباری هستند.
             */
            'gender' => fake()->randomElement([
                'male',
                'female',
                'other',
            ]),

            'city' => fake()->city(),

            /*
             * مقادیر پیش‌فرض موردنیاز تست‌های مدیریت.
             */
            'role' => 'user',
            'banned' => false,
        ];
    }

    public function unverified(): static
    {
        return $this->state(
            fn (array $attributes): array => [
                'email_verified_at' => null,
            ]
        );
    }
}