<?php

namespace Database\Factories;

use Database\Seeders\AccountSeeder;
use Database\Seeders\OperationTypeSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialOperation>
 */
class FinancialOperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $checked = fake()->boolean(30);

        return [
        'account_id' => mt_rand(1,AccountSeeder::$accountsCount),
        'title' => fake()->text(20),
        'date' => fake()->date(),
        'operation_type_id' => mt_rand(1,OperationTypeSeeder::$operationTypesCount),
        'subject' => fake()->name,
        'sum' => fake()->randomFloat(2,1,1000),
        'attachment' => fake()->unique()->filePath(),
        'checked' => $checked,
        'sap_id' => $checked? fake()->randomNumber(5) : null
        ];
    }
}
