<?php

namespace Database\Seeders;

use App\Models\OperationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RealOperationTypeSeeder extends Seeder
{
    /**
     * The supported operation types.
     * @var array
     */
    private array $types = [
        [ 'name' => 'Služba na faktúru', 'expense' => false, 'lending' => false ],
        [ 'name' => 'Grant', 'expense' => false, 'lending' => false ],
        [ 'name' => 'Pôžička', 'expense' => false, 'lending' => true ],
        [ 'name' => 'Splatenie pôžičky', 'expense' => false, 'lending' => true ],
        [ 'name' => 'Iný', 'expense' => false, 'lending' => false ],

        [ 'name' => 'Nákup na faktúru', 'expense' => true, 'lending' => false ],
        [ 'name' => 'Nákup cez Marquet', 'expense' => true, 'lending' => false ],
        [ 'name' => 'Drobný nákup', 'expense' => true, 'lending' => false ],
        [ 'name' => 'Pracovná cesta', 'expense' => true, 'lending' => false ],
        [ 'name' => 'Pôžička', 'expense' => true, 'lending' => true ],
        [ 'name' => 'Splatenie pôžičky', 'expense' => true, 'lending' => true ],
        [ 'name' => 'Iný', 'expense' => true, 'lending' => false ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OperationType::create($this->types);
    }
}
