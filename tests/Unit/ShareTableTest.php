<?php

namespace Tests\Unit;

use App\Lib\EShareTableType;
use App\Models\Member;
use App\Models\SharePermissions;
use App\Models\ShareTable;
use App\Models\ShareTableVirtualFile;
use App\Models\VirtualFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShareTableTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 設定測試環境
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_create_a_share_table()
    {
        $member = Member::factory()->create();
        
        $shareTable = ShareTable::create([
            'member_id' => $member->id,
            'name' => '測試分享表',
            'description' => '這是一個測試分享表',
            'type' => EShareTableType::public->value,
            'expired_at' => Carbon::now()->addDays(7),
            'short_code' => 'TEST123',
            'secret' => null,
        ]);

        $this->assertInstanceOf(ShareTable::class, $shareTable);
        $this->assertEquals('測試分享表', $shareTable->name);
        $this->assertEquals(EShareTableType::public->value, $shareTable->type);
        $this->assertEquals($member->id, $shareTable->member_id);
    }

    /** @test */
    public function it_can_create_private_share_table_with_secret()
    {
        $member = Member::factory()->create();
        
        $shareTable = ShareTable::create([
            'member_id' => $member->id,
            'name' => '私人分享表',
            'description' => '需要密碼的分享表',
            'type' => EShareTableType::private->value,
            'expired_at' => Carbon::now()->addDays(30),
            'short_code' => 'PRIV456',
            'secret' => 'password123',
        ]);

        $this->assertEquals(EShareTableType::private->value, $shareTable->type);
        $this->assertEquals('password123', $shareTable->secret);
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create(['member_id' => $member->id]);

        $this->assertInstanceOf(Member::class, $shareTable->member);
        $this->assertEquals($member->id, $shareTable->member->id);
    }

    /** @test */
    public function it_can_check_if_member_is_owner()
    {
        $owner = Member::factory()->create();
        $otherMember = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create(['member_id' => $owner->id]);

        $this->assertTrue($shareTable->isOwner($owner));
        $this->assertFalse($shareTable->isOwner($otherMember));
    }

    /** @test */
    public function it_can_add_permissions_to_members()
    {
        $owner = Member::factory()->create();
        $member = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create([
            'member_id' => $owner->id,
            'type' => EShareTableType::private->value
        ]);

        $permission = SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member->id,
            'permission_type' => 'read',
            'expired_at' => Carbon::now()->addDays(7),
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addDays(7),
        ]);

        $this->assertTrue($shareTable->isPermissionMember($member));
        $this->assertFalse($shareTable->isPermissionMember($owner)); // 擁有者不在權限表中
    }

    /** @test */
    public function it_can_get_relation_members()
    {
        $owner = Member::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create(['member_id' => $owner->id]);

        // 添加權限成員
        SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member1->id,
            'permission_type' => 'read',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member2->id,
            'permission_type' => 'write',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        $relationMembers = $shareTable->relationMember();
        
        $this->assertCount(2, $relationMembers);
        $this->assertTrue($relationMembers->contains('id', $member1->id));
        $this->assertTrue($relationMembers->contains('id', $member2->id));
    }

    /** @test */
    public function it_can_attach_virtual_files()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create(['member_id' => $member->id]);
        $virtualFile = VirtualFile::factory()->create();

        $shareTableVirtualFile = ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile->uuid,
            'dash_videos_id' => null,
        ]);

        $this->assertInstanceOf(ShareTableVirtualFile::class, $shareTableVirtualFile);
        $this->assertEquals($shareTable->id, $shareTableVirtualFile->share_table_id);
        $this->assertEquals($virtualFile->uuid, $shareTableVirtualFile->virtual_file_uuid);
    }

    /** @test */
    public function it_can_get_all_virtual_files()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create(['member_id' => $member->id]);
        
        $virtualFile1 = VirtualFile::factory()->create();
        $virtualFile2 = VirtualFile::factory()->create();

        // 關聯虛擬檔案
        ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile1->uuid,
        ]);

        ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile2->uuid,
        ]);

        $allVirtualFiles = $shareTable->getAllVirtualFiles();
        
        $this->assertCount(2, $allVirtualFiles);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $allVirtualFiles);
    }

    /** @test */
    public function it_generates_correct_share_url()
    {
        $shareTable = ShareTable::factory()->create(['short_code' => 'ABC123']);
        
        $shareUrl = $shareTable->shareURL();
        
        $this->assertStringContainsString('ABC123', $shareUrl);
        $this->assertStringContainsString(config('app.url'), $shareUrl);
    }

    /** @test */
    public function it_can_check_viewable_permissions_for_public_share()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value
        ]);

        $this->assertTrue($shareTable->viewable($member));
    }

    /** @test */
    public function it_can_check_viewable_permissions_for_private_share()
    {
        $owner = Member::factory()->create();
        $member = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create([
            'member_id' => $owner->id,
            'type' => EShareTableType::private->value
        ]);

        // 添加權限
        SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member->id,
            'permission_type' => 'read',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        $this->assertTrue($shareTable->viewable($member));
    }

    /** @test */
    public function it_handles_expired_share_tables()
    {
        $shareTable = ShareTable::factory()->create([
            'expired_at' => Carbon::now()->subDays(1) // 已過期
        ]);

        $this->assertTrue($shareTable->expired_at->isPast());
    }

    /** @test */
    public function it_can_handle_share_permissions_expiration()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create();

        $expiredPermission = SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member->id,
            'permission_type' => 'read',
            'expired_at' => Carbon::now()->subDays(1), // 已過期
        ]);

        $validPermission = SharePermissions::create([
            'share_tables_id' => $shareTable->id,
            'member_id' => $member->id,
            'permission_type' => 'write',
            'expired_at' => Carbon::now()->addDays(7), // 有效
        ]);

        $this->assertTrue($expiredPermission->expired_at->isPast());
        $this->assertTrue($validPermission->expired_at->isFuture());
    }

    /** @test */
    public function it_can_check_dash_video_availability()
    {
        $shareTable = ShareTable::factory()->create();
        $virtualFile = VirtualFile::factory()->create();

        $shareTableVirtualFile = ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile->uuid,
            'dash_videos_id' => null,
        ]);

        $this->assertFalse($shareTableVirtualFile->isCreateDashVideo());
        $this->assertFalse($shareTableVirtualFile->isAvailableDashVideo());
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // 嘗試創建沒有必要欄位的分享表
        ShareTable::create([
            'name' => '測試分享表',
            // 缺少 member_id, type 等必要欄位
        ]);
    }

    /** @test */
    public function it_can_filter_share_tables_by_type()
    {
        $member = Member::factory()->create();
        
        $publicShare = ShareTable::factory()->create([
            'member_id' => $member->id,
            'type' => EShareTableType::public->value
        ]);
        
        $privateShare = ShareTable::factory()->create([
            'member_id' => $member->id,
            'type' => EShareTableType::private->value
        ]);

        $publicShares = ShareTable::where('type', EShareTableType::public->value)->get();
        $privateShares = ShareTable::where('type', EShareTableType::private->value)->get();

        $this->assertTrue($publicShares->contains('id', $publicShare->id));
        $this->assertTrue($privateShares->contains('id', $privateShare->id));
        $this->assertFalse($publicShares->contains('id', $privateShare->id));
        $this->assertFalse($privateShares->contains('id', $publicShare->id));
    }
}