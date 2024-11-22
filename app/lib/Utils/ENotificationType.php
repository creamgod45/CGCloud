<?php

namespace App\Lib\Utils;

use App\Lib\Type\String\CGString;

enum ENotificationType
{
    case info;
    case warning;
    case error;
    case success;

    public static function isVaild(string $name): bool
    {
        foreach (ENotificationType::cases() as $case) {
            if ((new CGString($case->name))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return true;
            }
        }
        return false;
    }

    public static function valueof(string $name): ?ENotificationType
    {
        foreach (ENotificationType::cases() as $case) {
            if ((new CGString($case->name))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return $case;
            }
        }
        return null;
    }
}
