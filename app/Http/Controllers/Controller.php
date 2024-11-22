<?php

namespace App\Http\Controllers;

use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\I18N;
use App\Lib\Utils\CGLaravelControllerInit;
use App\Lib\Utils\ClientConfig;
use App\Lib\Utils\EncryptedCache;
use App\Lib\Utils\Utilsv2;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function baseControllerInit(Request $request, array ...$params): CGLaravelControllerInit
    {
        return (new Controller)->extracted($request, $params);
    }

    /**
     * 內部使用 CGLaravelControllerInit 類別建構器
     *
     * @param Request $request
     * @param array   $params
     *
     * @return CGLaravelControllerInit
     */
    private function extracted(Request $request, array $params): CGLaravelControllerInit
    {
        $url = $request->url();
        $path = parse_url($url, PHP_URL_PATH);
        $router = [];
        if ($path !== null) {
            $pathParts = explode('/', $path);
            $router = array_filter($pathParts);
        }
        $config = EncryptedCache::get(Session::get("ClientID") . "_ClientConfig");
        $lang = ELanguageCode::zh_TW;
        if ($config instanceof ClientConfig) {
            $lang = $config->getLanguageClass();
        }
        $i18N = new I18N($lang, limitMode: [ELanguageCode::zh_TW]);
        $fingerprint = self::fingerprint($request->session()->get('ClientID', ""));
        return new CGLaravelControllerInit($i18N, $router, $request, $params, $fingerprint);
    }

    public static function fingerprint(string $key = "")
    {
        return Utilsv2::getClientFingerprint($key);
    }

    /**
     * @param Request $request
     * @param array   $params
     *
     * @return CGLaravelControllerInit
     */
    public function baseGlobalVariable(Request $request, array $params = []): CGLaravelControllerInit
    {
        return $this->extracted($request, $params);
    }
}
