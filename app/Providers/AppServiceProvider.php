<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Log;
use PhpParser\Node\Expr\Cast\Double;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot any application services.
     *
     * This method performs the following:
     * - Registers a custom `Collection` macro named `expectKeys` for filtering
     *   collection items based on expected keys and values.
     * - Registers an event listener for queue jobs that logs successful job processing.
     * - Registers an event listener for queue jobs that logs failed job processing.
     *
     * @throws InvalidArgumentException if the count of keys and values in the `expectKeys` macro do not match.
     */
    public function boot(): void
    {
        /**
         * 在集合中查找與指定鍵值對匹配的所有元素。
         *
         * 此方法會遍歷集合中的每個元素，檢查是否符合指定的鍵值對條件。
         * 如果元素包含所有指定的鍵值對，則將該元素添加到結果集合中。
         * 鍵和值的比較使用字串強制轉換 (string) 以確保類型一致性（例如 1 和 '1' 被視為相等）。
         *
         * @param string|array $keys  要檢查的鍵名或鍵名陣列
         * @param string|array $values 對應的值或值陣列，順序必須與 $keys 一致
         *
         * @throws InvalidArgumentException 當 $keys 和 $values 的數量不相同時拋出
         * @return Collection 包含所有匹配元素的新集合
         *
         * @example
         * // 單一鍵值比較，返回所有 name 為 "test" 的元素
         * collect([["name" => "test", "price" => 1], ["name" => "test2", "price" => 2]])->expectKeys('name', 'test');
         *
         * @example
         * // 多鍵值比較，返回所有 name 為 "test" 且 price 為 1 的元素
         * collect([["name" => "test", "price" => 1], ["name" => "test2", "price" => 2]])->expectKeys(['name', 'price'], ['test', '1']);
         */
        Collection::macro('expectKeys', function (string|array $keys, string|array $values): Collection {
            // 將單一鍵值轉換為陣列形式，方便統一處理
            $keys = is_array($keys) ? $keys : [$keys];
            $values = is_array($values) ? $values : [$values];

            // 確保 $keys 和 $values 數量相同
            if (count($keys) !== count($values)) {
                throw new InvalidArgumentException('$keys 和 $values 的數量必須相同');
            }

            // 創建一個新的集合用於保存匹配的元素
            $matches = new Collection();

            // 遍歷集合，尋找符合所有鍵值對的元素
            foreach ($this->items as $item) {
                $matched = true;

                // 檢查每個鍵值對是否匹配
                for ($i = 0; $i < count($keys); $i++) {
                    $key = $keys[$i];
                    $value = $values[$i];

                    // 檢查是否存在該鍵並且值相等
                    // 這裡使用 (string) 轉換來處理數字比較問題
                    if (!isset($item[$key]) || (string)$item[$key] !== (string)$value) {
                        $matched = false;
                        break;
                    }
                }

                // 如果所有鍵值對都匹配，則將元素添加到結果集合中
                if ($matched) {
                    $matches->push($item);
                }
            }

            // 返回包含所有匹配元素的集合
            return $matches;
        });
        //
        // 註冊任務處理成功的監聽器
        Queue::after(function (JobProcessed $event) {
            // 記錄成功的任務
            Log::info('任務處理成功', [
                'connectionName' => $event->connectionName,
                'job' => $event->job->getName(),
                'jobId' => $event->job->getJobId(),
                'payload' => $event->job->payload(),
            ]);
        });

        // 註冊任務失敗的監聽器
        Queue::failing(function (JobFailed $event) {
            // 記錄失敗的任務
            Log::error('任務處理失敗', [
                'connectionName' => $event->connectionName,
                'job' => $event->job->getName(),
                'jobId' => $event->job->getJobId(),
                'exception' => $event->exception->getMessage(),
                'payload' => $event->job->payload(),
            ]);
        });
    }
}
