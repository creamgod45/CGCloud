<?php

namespace App\Console\Commands;

use App\Lib\Type\Array\CGArray;
use App\Lib\Utils\CGFileSystem\CGBaseFile;
use App\Lib\Utils\CGFileSystem\CGBaseFileObject;
use App\Lib\Utils\CGFileSystem\CGBaseFolder;
use App\Lib\Utils\CGFileSystem\CGFileSystem;
use App\Lib\Utils\CGFileSystem\CGPathUtils;
use App\Lib\Utils\Utilsv2;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Nette\Utils\FileSystem;

class importDbCommand extends Command
{
    protected $signature = 'import:db {database : 目標資料庫名稱} {table : 目標資料表名稱} {input? : JSON資料檔案路徑，選填}';

    protected $description = '從 JSON 檔案匯入資料到指定的資料庫資料表';

    public function genArgumentWrapper(string $name, array $choices, string $questionText, string $customQuestionText, string $defaultQuestionValue, ) {
        if($this->argument($name) !== null) {
            $choices[] = $this->argument($name);
        }
        $choice = $this->choice($questionText, $choices);
        if (str_contains($choice, "custom")) {
            $finalValue = (string)$this->ask($customQuestionText, $defaultQuestionValue);
        } else {
            $finalValue = $choice;
        }
        return $finalValue;
    }

    /**
     * 執行資料匯入命令
     */
    public function handle(): void
    {
        list ($finalDatabase, $finalTable, $finalInputFile) = [
            $this->genArgumentWrapper(
                'database',
                [
                    "custom database",
                    Env::get('DB_DATABASE'),
                ],
                'Please select the target database for import',
                'Please enter the custom database name:',
                $this->argument('database')
            ),
            $this->genArgumentWrapper(
                'table',
                [
                    "custom Table",
                ],
                'Please select the target table to import data into',
                'Please enter the custom table name:',
                $this->argument('table')
            ),
            $this->genArgumentWrapper(
                'input',
                [
                    "custom input file",
                    CGPathUtils::converterPathSlash(storage_path('app/backups/db.json')),
                ],
                'Please select the JSON file to import from',
                'Please enter the path to your custom JSON file:',
                CGPathUtils::converterPathSlash($this->argument('input')) ?? CGPathUtils::converterPathSlash(storage_path('app/backups/db.json'))
            )
        ];

        $CGBaseFolder = CGFileSystem::getCGFileObject($finalInputFile);
        $isFile = $CGBaseFolder->isFile();

        $this->info("開始處理檔案: $finalInputFile");
        if($CGBaseFolder->isFile() && $CGBaseFolder instanceof CGBaseFile) {
            $value = $CGBaseFolder->readFile();
            if(Utilsv2::isJson($value)) {
                $fileJson = Json::decode($value, true);
                $array = new CGArray($fileJson);
                $first = $array->getFirstObject();
                //dump($first);
                $keys = $first->getKeys();
                $this->info("insert keys: ".implode(", ", $keys));

                // Laravel 的指令選項必須在 signature 內定義
                // 原本使用 $this->option('force') 會觸發「The 'force' option does not exist」錯誤
                // 解決方式：於 signature 中加上 {--force} 即可
                // 但因為本檔案的 signature 沒有 --force，所以要移除相關程式碼
                // 如果真的需要 force 功能，請這樣定義 signature:
                // protected $signature = 'import:db {database} {table} {input?} {--force}';

                // === 修正：簡單詢問(原本用 force 選項) ===
                $answer = $this->confirm('[最終確認] 您是否要執行此命令', false);
                if(!$answer) {
                    $this->error("已取消所有操作");
                    return;
                } else {
                    try {
                        $connection = $this->getConnection($finalDatabase);

                        if ($connection->table($finalTable)->exists()) {
                            $connection->table($finalTable)->truncate();
                            $this->warn("[警告] 已清空資料庫");
                        }
                        $connection->enableQueryLog();
                        $connection->table($finalTable)->insertOrIgnore($fileJson);
                        $this->info("已完成匯入");
                        if($isFile){
                            $virtualFile = CGFileSystem::getCGFileObject($CGBaseFolder->getDirname()."/".$CGBaseFolder->getFilename().".log.json");
                            $CGBaseFile = $virtualFile->touchAndCastToCGBaseFile();
                            $queryLog = $connection->getQueryLog();
                            $CGBaseFile->writeFile(json_encode($queryLog, JSON_PRETTY_PRINT));
                            $this->info("完成時間: ".$queryLog["0"]["time"]."ms");
                        }
                    } catch(Exception $e) {
                        $this->error("執行時發生錯誤: ".$e->getMessage());
                    }
                }
            } else {
                $this->error('檔案內容不是合法 JSON 結構');
            }
        } else {
            $this->error("找不到檔案: {$finalInputFile}");
        }
    }

    /**
     * @param array|string $finalDatabase
     *
     * @return \Illuminate\Database\Connection
     */
    private function getConnection(array|string $finalDatabase): \Illuminate\Database\Connection
    {
        $newConnectionName = 'import_temp_' . uniqid();
        config([
            "database.connections.$newConnectionName" => [
                'driver' => 'pgsql',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '5432'),
                'database' => $finalDatabase,
                'username' => env('DB_USERNAME', 'forge'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]
        ]);

        // 清除舊連線（若有）
        DB::purge($newConnectionName);

        // 之後用此 connection 執行各種操作
        $connection = DB::connection($newConnectionName);
        return $connection;
    }
}
