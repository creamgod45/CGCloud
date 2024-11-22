<?php

namespace Database\Seeders;

use App\Models\InventoryType;
use Illuminate\Database\Seeder;

class InventoryTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultTypes = [
            "Notebook" => "筆電",
            "PC" => "桌機",
            "GPU" => "顯示卡",
            "RAM" => "記憶體",
            "AllInOne" => "一體機",
            "MB" => "主機板",
            "SSD" => "固態硬碟",
            "HHD" => "機械硬碟",
            "Phone" => "手機",
            "PS" => "電源供應器",
            "CPU" => "處理器",
            "FAN" => "風扇",
            "COOLER" => "冷卻器",
            "CASE" => "機殼",
            "Keyboard" => "鍵盤",
            "Mouse" => "滑鼠",
            "Screen" => "螢幕",
            "Speaker" => "喇叭",
            "Mic" => "麥克風",
        ];
        foreach ($defaultTypes as $key => $defaultType) {
            InventoryType::updateOrInsert(
                ['namespace' => $key],
                ['namespace' => $key, 'name' => $defaultType, 'description' => ""],
            );
        }
    }
}
