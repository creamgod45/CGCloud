<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\Notification;
use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use App\Lib\Utils\EncryptedCache;
use App\Lib\Utils\RouteNameField;
use App\Lib\Utils\Utilsv2;
use App\Models\Member;
use Config;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\Factory;
use React\Socket\Connector as ReactConnector;
use Session;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public I18N $i18N;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->i18N = new I18N(ELanguageCode::zh_TW, limitMode: [ELanguageCode::zh_TW]);
    }

    public string $cache_Notification_value = "";

    /**
     * A basic test example.
     */
    public function test_the_application_PageHome_returns_a_successful_response(): void
    {
        $response = $this->get(route(RouteNameField::PageHome->value));
        $response->assertStatus(200);
    }

    public function test_the_application_PageDesignComponents_returns_a_successful_response(): void
    {
        $response = $this->get(route(RouteNameField::PageDesignComponents->value));
        $response->assertStatus(200);
    }

    public function test_the_application_broadcast_returns_a_successful_response(): void
    {
        $response = $this->postJson(route(RouteNameField::APIBroadcast->value), ["description" => 'test', "title" => 'test', "type" => 'info', "second" => '10000',]);
        $response->assertStatus(200)->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'raw']);
        });
    }

    public function test_the_application_forget_password_a_successful_response(): void
    {
        $response = $this->get(route(RouteNameField::PageForgetPassword->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(200);
    }

    public function test_the_application_ForgetPassword_redirect_to_Home_Route(): void
    {
        $response = $this->get(route(RouteNameField::PagePasswordReset->value));
        $response->assertStatus(302)->assertRedirectToRoute(RouteNameField::PageHome->value)->assertSessionHas('invaild', true);
    }

    public function test_the_application_PageEmailVerification_redirect_to_Route()
    {
        $response = $this->get("/email/verify/1/1");
        $response->assertStatus(302)->assertRedirectToRoute(RouteNameField::PageHome->value);
    }

    public function test_the_application_PageMembers_redirect_to_Route(): void
    {
        $response = $this->get(route(RouteNameField::PageMembers->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(302)->assertRedirectToRoute(RouteNameField::PageLogin->value);
    }

    public function test_the_application_APIEncodeJson_compare()
    {
        $value = "lzstring compress data to encode data post to lzstring.json URL deocde data to check";
        $compress = Utilsv2::encodeContext($value)['compress'];
        $response = $this->postJson(route(RouteNameField::APIEncodeJson->value), ['a' => $compress]);

        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(200)->assertJson(['message' => $this->i18N->getLanguage(ELanguageText::DataReceivedSuccessfully), 'raw' => Utilsv2::decodeContext($compress)]);
    }

    public function test_the_application_language()
    {
        $response = $this->postJson(route(RouteNameField::APILanguage->value), ['lang' => ELanguageCode::zh_CN->name]);
        //$response->dumpSession();
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(200)->assertJson(['message' => $this->i18N->getLanguage(ELanguageText::DataReceivedSuccessfully), 'lang' => ELanguageCode::zh_CN->name]);
        $response = $this->postJson(route(RouteNameField::APILanguage->value), ['lang' => "error"]);
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(400)->assertJson(['message' => 'Error']);
        $response = $this->postJson(route(RouteNameField::APILanguage->value));
        //$response->dumpHeaders();
        $response->assertStatus(200)->assertJson(function (AssertableJson $json) {
            //$json->dump();
            $json->hasAll(['message', 'lang']);
        });
    }

    public function test_the_application_member_middleware_testing()
    {
        // user tester
        $user = Member::factory()->create();

        $response = $this->actingAs($user, 'web')->get(route(RouteNameField::PageEmailReSendMailVerification->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(302)->assertSessionHas('mail', true)->assertRedirectToRoute(RouteNameField::PageHome->value);

        $response = $this->actingAs($user, 'web')->get(route(RouteNameField::PageLogout->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(200)->assertViewIs('logout');

        // 已經驗證過信箱

        $user->markEmailAsVerified();
        $user->forceFill([
            'administrator' => 'true'
        ])->save();

        $response = $this->actingAs($user, 'web')->get(route(RouteNameField::PageMembers->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(200);

        $response = $this->actingAs($user, 'web')->get(route(RouteNameField::PageEmailReSendMailVerification->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(302)->assertSessionHas('mail_result', 1)->assertRedirectToRoute(RouteNameField::PageHome->value);

        $user->delete();
    }

    public function test_the_application_resendemail()
    {
        $response = $this->get(route(RouteNameField::PageEmailReSendMailVerification->value));
        //$response->dumpHeaders();
        //$response->dump();
        $response->assertStatus(302)->assertRedirectToRoute(RouteNameField::PageLogin->value);
    }

    public function test_the_Pusher_Broadcast_RX_TX()
    {
        $this->markTestSkipped('Requires network access to Pusher');
    }

}
