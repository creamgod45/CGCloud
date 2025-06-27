<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Member;
use App\Models\ShareTable;
use App\Models\VirtualFile;
use App\Models\ShareTableVirtualFile;
use App\Models\DashVideos;

class MigrationForeignKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_dash_video_and_share_table_virtual_file_relationship(): void
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create(['member_id' => $member->id]);
        $virtualFile = VirtualFile::factory()->create();

        $stvf = ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile->uuid,
        ]);

        $dashVideo = DashVideos::create([
            'virtual_file_uuid' => $virtualFile->uuid,
            'share_table_virtual_file_id' => $stvf->id,
            'member_id' => $member->id,
        ]);

        $stvf->update(['dash_videos_id' => $dashVideo->id]);

        $this->assertEquals($dashVideo->id, $stvf->dashVideos->id);
        $this->assertEquals($stvf->id, $dashVideo->shareTableVirtualFile->id);
    }
}
