<?php

namespace App\Lib\Log;

use App\Lib\Type\String\CGString;

enum LogType: string
{
    case JOB_RUN_SUCCESS = 'Job:run.success';
    case JOB_RUN_FAILED = 'Job:run.failed';
    case QUEUE_RUN_SUCCESS = 'Queue:run.success';
    case QUEUE_RUN_FAILED = 'Queue:run.failed';
    case SCHEDULE_RUN_SUCCESS = 'Schedule:run.success';
    case SCHEDULE_RUN_FAILED = 'Schedule:run.failed';
    case AUTH_LOGIN = 'auth:Login';
    case AUTH_LOGOUT = 'auth:Logout';
    case AUTH_LOGOUT_ALL_DEVICES = 'auth:LogoutAllDevices';
    case AUTH_KICK = 'auth:Kick';
    case AUTH_BAN = 'auth:Ban';
    case INVENTORY_ADD = 'Inventory:Add';
    case INVENTORY_REMOVE = 'Inventory:Remove';
    case INVENTORY_EDIT = 'Inventory:Edit';
    case MEMBER_ADD = 'Member:Add';
    case MEMBER_REMOVE = 'Member:Remove';
    case MEMBER_EDIT = 'Member:Edit';
    case SHOP_CONFIG_ADD = 'ShopConfig:Add';
    case SHOP_CONFIG_REMOVE = 'ShopConfig:Remove';
    case SHOP_CONFIG_EDIT = 'ShopConfig:Edit';


    public static function isVaild(string $name): bool
    {
        foreach (LogType::cases() as $case) {
            if ((new CGString($case->name))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return true;
            }
            if ((new CGString($case->value))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return true;
            }
        }
        return false;
    }

    public static function valueof(string $name): ?LogType
    {
        foreach (LogType::cases() as $case) {
            if ((new CGString($case->name))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return $case;
            }
            if ((new CGString($case->value))->toUpperCase()->toString() === (new CGString($name))->toUpperCase()->toString()) {
                return $case;
            }
        }
        return null;
    }
}
