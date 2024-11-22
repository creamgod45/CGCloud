<?php

namespace App\Jobs;

use App\Lib\Utils\RouteNameField;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateHomeCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $key = 1,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function handle(): void
    {
        // 初始化 Guzzle HTTP 客戶端
        $client = new Client();

        // 發送 GET 請求到目標網址
        Log::info(route(RouteNameField::PageHome->value, ["page" => $this->key]));
        $response = $client->request('GET', route(RouteNameField::PageHome->value, ["page" => $this->key]));

        // 獲取響應的狀態碼
        $statusCode = $response->getStatusCode(); // 200 表示成功

        // 顯示響應內容
        //Log::info(response()->json(['status_code' => $statusCode]));
        return;
    }
}
