<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\ShareTable;
use App\Models\VirtualFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ShareTableFactory extends Factory
{
    protected $model = ShareTable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'type' => $this->faker->word(),
            'expired_at' => $this->faker->randomNumber(),
            'short_code' => $this->faker->word(),
            'secret' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'filepond_id' => VirtualFile::factory(),
            'member_id' => Member::factory(),
        ];
    }
}
