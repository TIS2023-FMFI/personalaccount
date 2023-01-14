<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\OperationType;
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
        $accounts = Account::all('id');
        $operationTypes = OperationType::all('id');
        $checked = fake()->boolean(30);

        return [
            'account_id' => $accounts->random()['id'],
            'title' => fake()->text(20),
            'date' => fake()->date,
            'operation_type_id' => $operationTypes->random()['id'],
            'subject' => fake()->name,
            'sum' => fake()->randomFloat(2,1,1000),
            'attachment' => fake()->unique()->filePath(),
            'checked' => $checked,
            'sap_id' => $checked? fake()->randomNumber(5) : null
        ];
    }
}
