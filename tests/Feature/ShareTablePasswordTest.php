<?php

namespace Tests\Feature;

use App\Http\Controllers\ShareTablePasswordController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Lib\EShareTableType;
use App\Models\Member;
use App\Models\ShareTable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShareTablePasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    #[Test]
    public function it_shows_password_modal_when_accessing_password_protected_share(): void
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST01',
            'secret' => Hash::make('correct123'),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $response = $this->withoutExceptionHandling()->get("/p/share/{$shareTable->short_code}");

        $response->assertStatus(200)
            ->assertSee('password-modal-overlay', false)
            ->assertSee('此分享資源已加密', false);
    }

    #[Test]
    public function it_returns_error_for_incorrect_password(): void
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST02',
            'secret' => Hash::make('correct123'),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson("/p/share/{$shareTable->short_code}/unlock", [
                'password' => 'wrongpassword',
            ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    #[Test]
    public function it_unlocks_session_on_correct_password(): void
    {
        $correctPassword = 'MySecretPass!';
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST03',
            'secret' => Hash::make($correctPassword),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson("/p/share/{$shareTable->short_code}/unlock", [
                'password' => $correctPassword,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // 確認 session 已寫入解鎖 key
        $sessionKey = ShareTablePasswordController::sessionKey($shareTable->short_code);
        $response->assertSessionHas($sessionKey, true);
    }

    #[Test]
    public function it_returns_404_for_non_existent_shortcode(): void
    {
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/p/share/DOESNOTEXIST/unlock', [
                'password' => 'anything',
            ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_error_for_empty_password(): void
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST04',
            'secret' => Hash::make('secret'),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson("/p/share/{$shareTable->short_code}/unlock", [
                'password' => '',
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    #[Test]
    public function it_shows_file_list_after_session_is_unlocked(): void
    {
        $correctPassword = 'unlockme';
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST05',
            'secret' => Hash::make($correctPassword),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $sessionKey = ShareTablePasswordController::sessionKey($shareTable->short_code);

        // 帶著已解鎖 session 訪問
        $response = $this->withoutExceptionHandling()
            ->withSession([$sessionKey => true])
            ->get("/p/share/{$shareTable->short_code}");

        $response->assertStatus(200)
            ->assertDontSee('password-modal-overlay', false);
    }

    #[Test]
    public function it_does_not_show_password_modal_for_unprotected_share(): void
    {
        $shareTable = ShareTable::factory()->create([
            'type' => EShareTableType::public->value,
            'short_code' => 'PWTEST06',
            'secret' => null,
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        $response = $this->withoutExceptionHandling()->get("/p/share/{$shareTable->short_code}");

        $response->assertStatus(200)
            ->assertDontSee('password-modal-overlay', false);
    }

    #[Test]
    public function it_cannot_unlock_a_private_share_table(): void
    {
        $owner = Member::factory()->create();
        $shareTable = ShareTable::factory()->create([
            'member_id' => $owner->id,
            'type' => EShareTableType::private->value,
            'short_code' => 'PWTEST07',
            'secret' => Hash::make('secret'),
            'expired_at' => Carbon::now()->addDays(7)->timestamp,
        ]);

        // unlock route 只處理 public 類型，所以應返回 404
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson("/p/share/{$shareTable->short_code}/unlock", [
                'password' => 'secret',
            ]);

        $response->assertStatus(404);
    }
}
