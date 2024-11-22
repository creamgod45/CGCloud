<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

ini_set('memory_limit', '1G');
set_time_limit(3600);

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--disk=backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '備份資料庫並產生 gzip 文件';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 获取数据库配置
        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");
        $disk = $this->option('disk');

        $filename = 'backup-' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql.gz';

        // 确定备份文件的存储路径
        $storageDisk = Storage::disk($disk);
        $storagePath = $storageDisk->path($filename);

        switch ($dbConfig['driver']) {
            case 'mysql':
                $this->backupMySQL($dbConfig, $storagePath);
                break;
            // 如果需要支持其他数据库类型，可以在这里添加
            default:
                $this->error('不支援的資料庫類型：' . $dbConfig['driver']);
                return CommandAlias::FAILURE;
        }

        $this->info('資料庫備份成功，備份檔案已儲存至 ' . $storagePath);

        return CommandAlias::SUCCESS;
    }

    protected function backupMySQL($dbConfig, $storagePath): void
    {
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $charset = $dbConfig['charset'] ?? 'utf8mb4';

        // 构建 mysqldump 命令
        $command = [
            'mysqldump',
            '--user=' . $username,
            '--password=' . $password,
            '--host=' . $host,
            '--port=' . $port,
            '--default-character-set=' . $charset,
            $database,
        ];

        $result = Process::pipe(function (Pipe $pipe) use ($storagePath, $command) {
            $pipe->command(implode(' ', $command));
            $pipe->command('gzip > ' . escapeshellarg($storagePath));
        });

        if (!$result->successful()) {
            $this->error('資料庫備份失敗：' . $result->errorOutput());
            return;
        }
        $this->info('資料庫備份輸出：' . $result->output());
    }
}
