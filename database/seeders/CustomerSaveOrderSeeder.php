<?php

namespace Database\Seeders;

use App\Models\CustomerSaveOrder;
use Illuminate\Database\Seeder;

class CustomerSaveOrderSeeder extends Seeder
{
    public function run(): void
    {
        CustomerSaveOrder::factory()->count(10)->create();
    }
}
