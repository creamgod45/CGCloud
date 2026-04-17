<?php

namespace Database\Factories;

use App\Lib\EShareTableType;
use App\Models\Member;
use App\Models\ShareTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ShareTableFactory extends Factory
{
    protected $model = ShareTable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'type' => EShareTableType::public->value,
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
            'short_code' => Str::random(10),
            'secret' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'member_id' => Member::factory(),
        ];
    }

    public function private(): static
    {
        return $this->state(['type' => EShareTableType::private->value]);
    }

    public function withPassword(string $password): static
    {
        return $this->state(['secret' => \Illuminate\Support\Facades\Hash::make($password)]);
    }
}
