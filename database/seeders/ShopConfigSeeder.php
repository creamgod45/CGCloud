<?php

namespace Database\Seeders;

use App\Models\ShopConfig;
use Illuminate\Database\Seeder;

class ShopConfigSeeder extends Seeder
{
    public function run(): void
    {
        ShopConfig::updateOrInsert(
            ['name' => 'ShopName'],
            ['value' => 'CGCloud 雲端檔案平台'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopDescription'],
            ['value' => '分享檔案與內容'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopMainColor'],
            ['value' => 'rgb(251, 191, 36)'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopMainTextColor'],
            ['value' => 'black'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopSecondaryColor'],
            ['value' => 'rgb(245, 158, 11)'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopSecondaryTextColor'],
            ['value' => 'black'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopMenuColor'],
            ['value' => 'rgb(245, 158, 11)'],
        );
        ShopConfig::updateOrInsert(
            ['name' => 'ShopImage'],
            ['value' => asset('favicon.ico')],
        );
    }
}
