<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\SharePermissions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SharePermissionsFactory extends Factory
{
    protected $model = SharePermissions::class;

    public function definition(): array
    {
        return [
            'permission_type' => $this->faker->word(),
            'expires_at' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'member_id' => Member::factory(),
        ];
    }
}
