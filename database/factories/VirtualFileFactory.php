<?php

namespace Database\Factories;

use App\Models\VirtualFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VirtualFileFactory extends Factory
{
    protected $model = VirtualFile::class;

    public function definition(): array
    {
        return [
            'filename' => $this->faker->word(),
            'path' => $this->faker->word(),
            'extension' => $this->faker->word(),
            'minetypes' => $this->faker->word(),
            'disk' => $this->faker->word(),
            'expires_at' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}