<?php

namespace Database\Factories;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class SystemLogFactory extends Factory
{
    protected $model = SystemLog::class;

    public function definition(): array
    {
        $random = Arr::random([
            'Job:run.success',
            'Job:run.failed',
            'Queue:run.success',
            'Queue:run.failed',
            'Schedule:run.success',
            'Schedule:run.failed',
            'auth:Login',
            'auth:Logout',
            'auth:LogoutAllDevices',
            'auth:Kick',
            'auth:Ban',
            'Inventory:Add',
            'Inventory:Remove',
            'Inventory:Edit',
            'Member:Add',
            'Member:Remove',
            'Member:Edit',
            'ShopConfig:Add',
            'ShopConfig:Remove',
            'ShopConfig:Edit',
        ]);
        return [
            'type' => $random,
            'title' => $random . " 操作",
            'description' => "",
        ];
    }
}
