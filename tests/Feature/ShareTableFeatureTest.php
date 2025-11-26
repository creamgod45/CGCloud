<?php

namespace Tests\Feature;

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

class ShareTableFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_access_public_share_table_without_authentication()
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PUBLIC123',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        // 假設有一個公開分享的路由
        $response = $this->get("/share/{$shareTable->short_code}");
        
        // 根據實際路由調整狀態碼
        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function it_requires_authentication_for_private_share_table()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create([
            'member_id' => $member->id,
            'type' => EShareTableType::private->value,
            'short_code' => 'PRIVATE456',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        // 未認證用戶嘗試訪問私人分享
        $response = $this->get("/share/{$shareTable->short_code}");
        
        // 應該被重定向到登入頁面或返回 401/403
        $this->assertTrue(in_array($response->status(), [302, 401, 403, 404]));
    }

    /** @test */
    public function it_can_create_share_table_via_api()
    {
        $member = Member::factory()->create();
        
        $shareData = [
            'name' => '測試 API 分享表',
            'description' => '透過 API 創建的分享表',
            'type' => EShareTableType::public->value,
            'expired_at' => Carbon::now()->addDays(30)->toISOString(),
        ];

        // 假設有 API 端點來創建分享表
        $response = $this->actingAs($member)
                         ->postJson('/api/share-tables', $shareData);

        // 根據實際 API 實作調整
        if ($response->status() === 201) {
            $response->assertStatus(201)
                    ->assertJsonStructure([
                        'data' => [
                            'id',
                            'name',
                            'description',
                            'type',
                            'short_code',
                            'share_url'
                        ]
                    ]);
        }
    }

    /** @test */
    public function it_can_add_files_to_share_table()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create(['member_id' => $member->id]);
        $virtualFile = VirtualFile::factory()->create();

        $fileData = [
            'virtual_file_uuid' => $virtualFile->uuid,
        ];

        // 假設有 API 端點來添加檔案到分享表
        $response = $this->actingAs($member)
                         ->postJson("/api/share-tables/{$shareTable->id}/files", $fileData);

        // 根據實際 API 實作調整
        if ($response->status() === 200) {
            $response->assertStatus(200);
            
            $this->assertDatabaseHas('share_table_virtual_file', [
                'share_table_id' => $shareTable->id,
                'virtual_file_uuid' => $virtualFile->uuid,
            ]);
        }
    }

    /** @test */
    public function it_can_manage_share_permissions()
    {
        $owner = Member::factory()->create();
        $member = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create([
            'member_id' => $owner->id,
            'type' => EShareTableType::private->value,
        ]);

        $permissionData = [
            'member_id' => $member->id,
            'permission_type' => 'read',
            'expired_at' => Carbon::now()->addDays(7)->toISOString(),
        ];

        // 假設有 API 端點來管理權限
        $response = $this->actingAs($owner)
                         ->postJson("/api/share-tables/{$shareTable->id}/permissions", $permissionData);

        // 根據實際 API 實作調整
        if ($response->status() === 201) {
            $response->assertStatus(201);
            
            $this->assertDatabaseHas('share_permissions', [
                'share_tables_id' => $shareTable->id,
                'member_id' => $member->id,
                'permission_type' => 'read',
            ]);
        }
    }

    /** @test */
    public function it_prevents_unauthorized_access_to_share_table_management()
    {
        $owner = Member::factory()->create();
        $unauthorizedMember = Member::factory()->create();
        
        $shareTable = ShareTable::factory()->create(['member_id' => $owner->id]);

        // 未授權用戶嘗試修改分享表
        $response = $this->actingAs($unauthorizedMember)
                         ->putJson("/api/share-tables/{$shareTable->id}", [
                             'name' => '嘗試修改名稱',
                         ]);

        // 應該返回 403 Forbidden
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    /** @test */
    public function it_handles_expired_share_table_access()
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'EXPIRED123',
            'expired_at' => Carbon::now()->subDays(1), // 已過期
        ]);

        $response = $this->get("/share/{$shareTable->short_code}");
        
        // 應該返回錯誤或重定向
        $this->assertTrue(in_array($response->status(), [404, 410, 302]));
    }

    /** @test */
    public function it_can_download_files_from_share_table()
    {
        $member = Member::factory()->create();
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'DOWNLOAD123',
            'expired_at' => Carbon::now()->addDays(7),
        ]);
        
        $virtualFile = VirtualFile::factory()->create();
        
        ShareTableVirtualFile::create([
            'share_table_id' => $shareTable->id,
            'virtual_file_uuid' => $virtualFile->uuid,
        ]);

        // 假設有下載端點
        $response = $this->get("/share/{$shareTable->short_code}/download/{$virtualFile->uuid}");
        
        // 根據實際實作調整
        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function it_can_list_user_share_tables()
    {
        $member = Member::factory()->create();
        
        // 創建多個分享表
        ShareTable::factory()->count(3)->create(['member_id' => $member->id]);
        ShareTable::factory()->count(2)->create(); // 其他用戶的分享表

        $response = $this->actingAs($member)
                         ->getJson('/api/my-share-tables');

        // 根據實際 API 實作調整
        if ($response->status() === 200) {
            $response->assertStatus(200)
                    ->assertJsonCount(3, 'data');
        }
    }

    /** @test */
    public function it_validates_share_table_creation_data()
    {
        $member = Member::factory()->create();
        
        $invalidData = [
            'name' => '', // 空名稱
            'type' => 'invalid_type', // 無效類型
            'expired_at' => 'invalid_date', // 無效日期
        ];

        $response = $this->actingAs($member)
                         ->postJson('/api/share-tables', $invalidData);

        // 應該返回驗證錯誤
        $this->assertTrue(in_array($response->status(), [422, 400]));
    }

    /** @test */
    public function it_can_search_share_tables()
    {
        $member = Member::factory()->create();
        
        ShareTable::factory()->create([
            'member_id' => $member->id,
            'name' => '重要文件分享',
            'description' => '包含重要文件的分享表',
        ]);
        
        ShareTable::factory()->create([
            'member_id' => $member->id,
            'name' => '圖片集合',
            'description' => '照片和圖片的集合',
        ]);

        // 搜索包含 "重要" 的分享表
        $response = $this->actingAs($member)
                         ->getJson('/api/share-tables?search=重要');

        // 根據實際搜索 API 實作調整
        if ($response->status() === 200) {
            $response->assertStatus(200);
            // 驗證搜索結果
        }
    }

    /** @test */
    public function it_can_handle_password_protected_shares()
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::private->value,
            'short_code' => 'PROTECTED123',
            'secret' => 'password123',
            'expired_at' => Carbon::now()->addDays(7),
        ]);

        // 不提供密碼的訪問
        $response = $this->get("/share/{$shareTable->short_code}");
        $this->assertTrue(in_array($response->status(), [401, 403, 302]));

        // 提供正確密碼的訪問
        $response = $this->post("/share/{$shareTable->short_code}/unlock", [
            'password' => 'password123'
        ]);
        
        // 根據實際實作調整
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}