<?php

namespace Tests\Unit;

use App\Lib\Utils\ClientConfig;
use App\Lib\I18N\ELanguageCode;
use Tests\TestCase;

class ClientConfigTest extends TestCase
{
    public function test_invalid_language_defaults_to_zh_tw(): void
    {
        $config = new ClientConfig('invalid');
        $this->assertEquals(ELanguageCode::zh_TW->name, $config->getLanguage());
        $this->assertEquals(ELanguageCode::zh_TW, $config->getLanguageClass());
    }
}
