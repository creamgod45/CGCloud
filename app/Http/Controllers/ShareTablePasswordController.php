<?php

namespace App\Http\Controllers;

use App\Lib\EShareTableType;
use App\Lib\Utils\RouteNameField;
use App\Models\ShareTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShareTablePasswordController extends Controller
{
    /**
     * 驗證密碼保護的 ShareTable，通過後寫入 Session。
     */
    public function unlock(Request $request): \Illuminate\Http\JsonResponse
    {
        $shortcode = $request->route('shortcode', '');

        $shareTable = ShareTable::where('short_code', '=', $shortcode)
            ->where('type', '=', EShareTableType::public->value)
            ->whereNotNull('secret')
            ->first();

        if ($shareTable === null) {
            return response()->json(['success' => false, 'message' => '分享資源不存在'], 404);
        }

        $password = $request->input('password', '');

        if (empty($password)) {
            return response()->json(['success' => false, 'message' => '請輸入密碼'], 422);
        }

        if (Hash::check($password, $shareTable->secret)) {
            $sessionKey = $this->sessionKey($shortcode);
            $request->session()->put($sessionKey, true);

            return response()->json([
                'success' => true,
                'message' => '密碼正確',
                'redirect' => route(RouteNameField::PageShareableShareTableItem->value, ['shortcode' => $shortcode]),
            ]);
        }

        return response()->json(['success' => false, 'message' => '密碼錯誤，請重新輸入'], 401);
    }

    /**
     * 取得此 shortcode 的 session key。
     */
    public static function sessionKey(string $shortcode): string
    {
        return 'share_unlocked_'.$shortcode;
    }
}
