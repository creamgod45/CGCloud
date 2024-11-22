<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ini_set('memory_limit', '16G');
        Inventory::factory()->count(1000000)->create();

    }
}
