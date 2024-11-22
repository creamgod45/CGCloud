<?php

namespace App\Lib\Utils;

class Filter
{
    public static function chatContextFilter($text): array|string|null
    {
        $text = self::xssFilter($text);
        return preg_replace("~(?:[\p{M}])([\p{M}])+?~uis", "", $text);
    }

    public static function xssFilter($string)
    {
        // 將所有 HTML 標籤轉換為 HTML 實體
        $string = htmlspecialchars($string, ENT_QUOTES);

        $string = htmlentities($string, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        // 將所有 JavaScript 事件處理程序屬性移除
        $string = preg_replace('/\bon[a-z]+\s*=\s*"/', '', $string);

        // 將所有 JavaScript 偽協議移除
        $string = preg_replace('/javascript:/', '', $string);

        // 將所有 CSS 表達式移除
        $string = preg_replace('/\bstyle\s*=\s*"/', '', $string);

        // 將所有 base64 編碼的數據移除
        $string = preg_replace('/data:image\/[a-z]+;base64,/', '', $string);

        return $string;
    }

    public static function intFilter($i)
    {
        $value = intval($i);
        if ($r = filter_var($value, FILTER_VALIDATE_INT)) {
            return $r;
        }
        return null; // Or any other specific integer indicating failure
    }

    public function universalFilter(string|FilterType $method, $value)
    {
        switch ($method) {
            case FilterType::Integer:
            case 'Integer':
                $value = intval($value);
                if ($value === 0) {
                    return true;
                }
                if (filter_var($value, FILTER_VALIDATE_INT)) {
                    return true;
                }
            case FilterType::Token:
            case 'Token':
                return filter_var($value, FILTER_SANITIZE_STRING);
            case FilterType::UUID:
            case 'UUID':
                if (preg_match("/[.0-9a-f]+/i", $value)) {
                    return true;
                }

                return false;
            case FilterType::Email:
            case 'Email':
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return true;
                }

                return false;
            case FilterType::EnglishNumberSpecialChat1to20:
            case 'EnglishNumberSpecialChat1to20':
                /**
                 * 英文開頭
                 * a~z 0~9 _^#$@!%&*-
                 * 1~20 位數
                 */
                // url:https://stackoverflow.com/questions/18562664/regular-expression-for-username-with-a-z0-9-3-20
                if (preg_match("/^[a-z][a-z0-9_^#$@!%&*-]{1,20}$/i", $value)) {
                    return true;
                }

                return false;
            case FilterType::Username:
            case 'username':
                /**
                 * 英文開頭
                 * a~z 0~9 _^#$@!%&*-
                 * 20~255 位數
                 */
                // url:https://stackoverflow.com/questions/18562664/regular-expression-for-username-with-a-z0-9-3-20
                if (preg_match("/^[a-z][a-z0-9_^#$@!%&*-]{1,255}$/i", $value)) {
                    return true;
                }

                return false;
            case FilterType::Nickname:
            case 'Nickname':
                /**
                 * 大於等於 2 個字 到 20 個字
                 * 中文 英文 數字
                 */
                if (preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{2,20}+$/u', $value)) {
                    return true;
                }
                $r = [];
                $num = 0;
                $r [] = strpos($value, "來福");
                $r [] = strpos($value, "系統");
                $r [] = strpos($value, "ray");
                $r [] = strpos($value, "管理員");
                $r [] = strpos($value, "Ray");
                $r [] = strpos($value, "𝗭𝗲𝗶𝘁𝗙𝗿𝗲𝗶");
                $r [] = strpos($value, "版主");
                $r [] = strpos($value, "版主");
                foreach ($r as $item) {
                    if (is_numeric($item)) {
                        $num++;
                    }
                }
                if ($num > 0) {
                    return false;
                }
                return true;
            case FilterType::Password:
            case 'Password':
                /**
                 * a~z 0~9 _^#$@!%&*-
                 * 8~255 位數
                 */
                if (preg_match("/^[a-z0-9_^#$@!%&*-]{8,255}$/i", $value)) {
                    return true;
                }

                return false;
            case FilterType::DiscordID:
            case 'DiscordID':
                if (filter_var($value, FILTER_VALIDATE_INT)) {
                    return true;
                }

                return false;
            case FilterType::URL:
            case 'URL':
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return true;
                }

                return false;
            case FilterType::Avatar:
            case 'Avatar':
                $num = 0;
                $arr = explode("/", $value);

                if ($arr[2] === "i.imgur.com") {
                    $num++;
                } else {
                    if ($arr[2] === "cdn.discordapp.com" && $arr[3] === "avatars") {
                        $num++;
                    }
                }

                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $num++;
                }
                if ($num === 2) {
                    return true;
                }
                return false;
            case FilterType::IP:
            case 'IP':
                if (filter_var($value, FILTER_VALIDATE_IP)) {
                    return true;
                }

                return false;
            case FilterType::BooleanString:
            case 'BooleanString':
                if ($value === "true" || $value === "false") {
                    return true;
                }
                return false;
            default:
                return false;
        }
    }

}
