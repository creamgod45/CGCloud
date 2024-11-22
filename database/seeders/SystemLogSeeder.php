<?php

namespace Database\Seeders;

use App\Models\SystemLog;
use Illuminate\Database\Seeder;

class SystemLogSeeder extends Seeder
{
    public function run(): void
    {
        SystemLog::factory()->count(100)->create();
    }
}
