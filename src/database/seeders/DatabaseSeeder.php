<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FinancialOperation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'email' => 'a@b.c',
        ]);

        $this->call([
            AccountSeeder::class,
            OperationTypeSeeder::class,
            FinancialOperationSeeder::class,
        ]);
    }
}
