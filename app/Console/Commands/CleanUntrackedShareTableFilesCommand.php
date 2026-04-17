<?php

namespace App\Console\Commands;

use App\Models\DashVideos;
use App\Models\VirtualFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanUntrackedShareTableFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share-table:clean-untracked
                            {--dry-run : 僅列出將被刪除的項目，不實際刪除}
                            {--force : 不需要確認直接執行}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刪除 storage/sharetable/ 中 virtual_files 未追蹤的孤立檔案，並清除過期的 allFiles.json 快取';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('【模擬模式】僅列出將被刪除的項目，不實際刪除。');
        }

        if (! $isDryRun && ! $this->option('force')) {
            if (! $this->confirm('此操作將永久刪除未被 virtual_files 追蹤的孤立檔案，確定要繼續嗎?')) {
                $this->info('已取消。');

                return 0;
            }
        }

        $deletedFiles = 0;
        $deletedBytes = 0;
        $deletedCacheFiles = 0;
        $deletedDashDirs = 0;

        // ── 1. 掃描 local disk 的 ShareTable/ 資料夾 ──────────────────────
        $this->info('');
        $this->info('【步驟 1/2】掃描 local disk ShareTable/ 資料夾中未追蹤的孤立檔案...');

        $localDisk = Storage::disk('local');
        $localRoot = $localDisk->path('');

        // 取得 ShareTable/ 下所有檔案（遞迴）
        $allPhysicalFiles = $localDisk->allFiles('ShareTable');

        // 取得 virtual_files 中所有 disk='local' 且以 ShareTable 開頭的 path
        $trackedPaths = VirtualFile::query()
            ->where('disk', 'local')
            ->where('path', 'like', 'ShareTable%')
            ->pluck('path')
            ->flip() // 轉為 hash map 加速查找
            ->all();

        foreach ($allPhysicalFiles as $relativePath) {
            // 跳過 allFiles.json 快取檔（稍後獨立處理）
            if (str_ends_with($relativePath, 'allFiles.json')) {
                continue;
            }

            if (! array_key_exists($relativePath, $trackedPaths)) {
                $absolutePath = $localRoot.$relativePath;
                $fileSize = file_exists($absolutePath) ? filesize($absolutePath) : 0;

                $this->line("  [孤立] {$relativePath} (".number_format($fileSize / 1024, 2).' KB)');

                if (! $isDryRun) {
                    try {
                        $localDisk->delete($relativePath);
                        $deletedFiles++;
                        $deletedBytes += $fileSize;
                        Log::info("[CleanUntracked] 刪除孤立檔案: {$relativePath}");
                    } catch (\Throwable $e) {
                        $this->error("  刪除失敗: {$relativePath} — ".$e->getMessage());
                        Log::error("[CleanUntracked] 刪除失敗: {$relativePath}", ['error' => $e->getMessage()]);
                    }
                } else {
                    $deletedFiles++;
                    $deletedBytes += $fileSize;
                }
            }
        }

        // ── 2. 清除過期的 allFiles.json 快取 ─────────────────────────────
        //    規則：若該資料夾下已沒有任何實際業務檔案（或資料夾已被刪除），刪除 allFiles.json
        $this->info('');
        $this->info('【步驟 1/2 延伸】掃描並清除過期的 allFiles.json 快取...');

        $allCacheFiles = collect($localDisk->allFiles('ShareTable'))
            ->filter(fn ($f) => str_ends_with($f, 'allFiles.json'));

        foreach ($allCacheFiles as $cacheFile) {
            $cacheDir = dirname($cacheFile);

            // 重新取得此資料夾（删除後可能已空）內的業務檔案
            $siblings = collect($localDisk->files($cacheDir))
                ->filter(fn ($f) => ! str_ends_with($f, 'allFiles.json'))
                ->values();

            $subDirs = $localDisk->directories($cacheDir);

            // 若資料夾沒有任何業務檔案且沒有子資料夾，快取已無意義
            if ($siblings->isEmpty() && empty($subDirs)) {
                $this->line("  [過期快取] {$cacheFile}");

                if (! $isDryRun) {
                    try {
                        $localDisk->delete($cacheFile);
                        $deletedCacheFiles++;
                        Log::info("[CleanUntracked] 刪除過期快取: {$cacheFile}");
                    } catch (\Throwable $e) {
                        $this->error("  刪除失敗: {$cacheFile} — ".$e->getMessage());
                    }
                } else {
                    $deletedCacheFiles++;
                }
            } else {
                // 資料夾仍有業務檔案 → 刷新 allFiles.json 內容
                if (! $isDryRun) {
                    $freshFiles = $localDisk->allFiles($cacheDir);
                    $fresh = json_encode(
                        array_values(array_filter($freshFiles, fn ($f) => ! str_ends_with($f, 'allFiles.json'))),
                        JSON_UNESCAPED_UNICODE
                    );
                    $localDisk->put($cacheFile, $fresh);
                    $this->line("  [已刷新] {$cacheFile}");
                }
            }
        }

        // ── 2b. 刪除空資料夾（由深到淺遞迴清除）─────────────────────────
        $this->info('');
        $this->info('【步驟 1/2 延伸 B】清除 ShareTable/ 下的空資料夾...');

        $deletedEmptyDirs = 0;

        // allDirectories() 回傳所有子資料夾，按路徑長度降序（最深的先處理）
        $allDirs = collect($localDisk->allDirectories('ShareTable'))
            ->sortByDesc(fn ($d) => substr_count($d, '/'))
            ->values();

        foreach ($allDirs as $dir) {
            // 永遠保留根目錄 ShareTable 本身
            if ($dir === 'ShareTable') {
                continue;
            }

            $files = $localDisk->files($dir);
            $subDirsRemaining = $localDisk->directories($dir);

            // 視 .gitkeep 為「佔位符」— 若資料夾只剩 .gitkeep 且無子目錄也視為空
            $meaningfulFiles = array_filter(
                $files,
                fn ($f) => ! str_ends_with($f, '.gitkeep') && ! str_ends_with($f, 'allFiles.json')
            );

            if (empty($meaningfulFiles) && empty($subDirsRemaining)) {
                $this->line("  [空資料夾] {$dir}");

                if (! $isDryRun) {
                    try {
                        $localDisk->deleteDirectory($dir);
                        $deletedEmptyDirs++;
                        Log::info("[CleanUntracked] 刪除空資料夾: {$dir}");
                    } catch (\Throwable $e) {
                        $this->error("  刪除失敗: {$dir} — ".$e->getMessage());
                    }
                } else {
                    $deletedEmptyDirs++;
                }
            }
        }

        // ── 3. 掃描 public disk DashVideos/ 孤立資料夾 ───────────────────
        $this->info('');
        $this->info('【步驟 2/2】掃描 public disk DashVideos/ 資料夾中孤立的 DASH 目錄...');

        $publicDisk = Storage::disk('public');

        if ($publicDisk->exists('DashVideos')) {
            $dashDirs = $publicDisk->directories('DashVideos');

            // 取得資料庫中所有有效的 dash_videos path (disk='public') 的目錄前綴
            $trackedDashPaths = DashVideos::query()
                ->whereNotNull('path')
                ->where('disk', 'public')
                ->pluck('path')
                ->map(fn ($p) => dirname($p)) // 取出資料夾部分
                ->unique()
                ->flip()
                ->all();

            foreach ($dashDirs as $dir) {
                // $dir 例如 DashVideos/12
                $subDirs = $publicDisk->directories($dir);

                // 找每個子資料夾（即各個 UUID 命名的 dash 輸出目錄）是否有對應 DB 記錄
                foreach ($subDirs as $subDir) {
                    if (! array_key_exists($subDir, $trackedDashPaths)) {
                        $this->line("  [孤立 Dash] {$subDir}");

                        if (! $isDryRun) {
                            try {
                                $publicDisk->deleteDirectory($subDir);
                                $deletedDashDirs++;
                                Log::info("[CleanUntracked] 刪除孤立 Dash 目錄: {$subDir}");
                            } catch (\Throwable $e) {
                                $this->error("  刪除失敗: {$subDir} — ".$e->getMessage());
                            }
                        } else {
                            $deletedDashDirs++;
                        }
                    } else {
                        // 刷新此 Dash 目錄的 allFiles.json 快取
                        $cacheFile = $subDir.'/allFiles.json';
                        if (! $isDryRun) {
                            $freshFiles = $publicDisk->allFiles($subDir);
                            $fresh = json_encode(
                                array_values(array_filter($freshFiles, fn ($f) => ! str_ends_with($f, 'allFiles.json'))),
                                JSON_UNESCAPED_UNICODE
                            );
                            $publicDisk->put($cacheFile, $fresh);
                        }
                    }
                }

                // 若父層 DashVideos/{id} 資料夾已空，也一併刪除
                $remainingSubDirs = $publicDisk->directories($dir);
                $remainingFiles = $publicDisk->files($dir);
                if (empty($remainingSubDirs) && empty($remainingFiles)) {
                    $this->line("  [空目錄] {$dir}");

                    if (! $isDryRun) {
                        try {
                            $publicDisk->deleteDirectory($dir);
                            Log::info("[CleanUntracked] 刪除空目錄: {$dir}");
                        } catch (\Throwable $e) {
                            $this->error("  刪除失敗: {$dir} — ".$e->getMessage());
                        }
                    }
                }
            }
        }

        // ── 4. 結果摘要 ──────────────────────────────────────────────────
        $this->info('');
        $this->info('=========================================');
        $label = $isDryRun ? '【模擬】將' : '已';

        $this->info("{$label}刪除孤立業務檔案：{$deletedFiles} 個 (".number_format($deletedBytes / 1024 / 1024, 2).' MB)');
        $this->info("{$label}清除過期 allFiles.json：{$deletedCacheFiles} 個");
        $this->info("{$label}刪除空資料夾：{$deletedEmptyDirs} 個");
        $this->info("{$label}刪除孤立 Dash 目錄：{$deletedDashDirs} 個");
        $this->info('=========================================');

        if ($isDryRun) {
            $this->warn('模擬完成，加上 --force 旗標以實際執行刪除。');
        }

        return 0;
    }
}
