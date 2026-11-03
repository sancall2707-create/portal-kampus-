<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'user_type' => 'staff',
            'nip' => fake()->unique()->numerify('PL######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'staff_category' => 'guru',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Pengguna umum (eksternal) yang login dengan Google.
     */
    public function external(): static
    {
        return $this->state(fn () => [
            'user_type' => 'external',
            'nip' => null,
            'password' => null,
            'staff_category' => null,
            'google_id' => (string) fake()->unique()->randomNumber(9),
            'organization_name' => fake()->company(),
        ]);
    }
}
