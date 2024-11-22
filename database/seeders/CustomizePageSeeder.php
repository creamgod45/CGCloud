<?php

namespace Database\Seeders;

use App\Models\CustomizePage;
use Illuminate\Database\Seeder;

class CustomizePageSeeder extends Seeder
{
    public function run(): void
    {
        CustomizePage::factory()->count(10)->create();
    }
}
