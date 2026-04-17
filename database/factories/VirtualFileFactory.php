<?php

namespace Database\Factories;

use App\Models\VirtualFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VirtualFileFactory extends Factory
{
    protected $model = VirtualFile::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'members_id' => null,
            'type' => 'temporary',
            'filename' => $this->faker->word().'.jpg',
            'path' => $this->faker->word(),
            'extension' => $this->faker->fileExtension(),
            'minetypes' => 'image/jpeg',
            'disk' => 'local',
            'size' => $this->faker->numberBetween(1024, 1024 * 1024),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
