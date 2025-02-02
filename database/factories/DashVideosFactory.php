<?php

namespace Database\Factories;

use App\Models\DashVideos;
use App\Models\Member;
use App\Models\ShareTable;
use App\Models\VirtualFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DashVideosFactory extends Factory
{
    protected $model = DashVideos::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'format' => $this->faker->word(),
            'audioCodec' => $this->faker->word(),
            'videoCodec' => $this->faker->word(),
            'width' => $this->faker->randomNumber(),
            'height' => $this->faker->randomNumber(),
            'framerate' => $this->faker->word(),
            'bitrate' => $this->faker->word(),
            'duration' => $this->faker->randomNumber(),
            'channels' => $this->faker->word(),
            'sampleRate' => $this->faker->word(),
            'videoFrames' => $this->faker->word(),
            'metadata' => $this->faker->word(),
            'videoStream' => $this->faker->word(),
            'audioStream' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'virtual_file_uuid' => VirtualFile::factory(),
            'thumb_virtual_file_uuid' => VirtualFile::factory(),
            'share_table_id' => ShareTable::factory(),
            'member_id' => Member::factory(),
        ];
    }
}
