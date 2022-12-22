<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::firstOrCreate([ 'email' => 'a@b.c' ]);

        return [
            'user_id' => $user->id,
            'title' => fake()->text(20),
            'sap_id' => fake()->uuid()
        ];
    }
}
