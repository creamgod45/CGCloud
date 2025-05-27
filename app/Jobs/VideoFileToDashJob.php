<?php

namespace App\Jobs;

use App\Lib\Utils\CGFileSystem\CGBaseFile;
use App\Lib\Utils\CGFileSystem\CGBaseFileObject;
use App\Lib\Utils\CGFileSystem\CGBaseFolder;
use App\Lib\Utils\CGFileSystem\CGFileSystem;
use App\Lib\Utils\CGFileSystem\CGPathUtils;
use App\Models\DashVideos;
use App\Models\ShareTable;
use App\Models\ShareTableVirtualFile;
use App\Models\VirtualFile;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nette\Utils\FileSystem;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Streaming\FFMpeg as StreamingFFMpeg;
use Streaming\Format\X264;
use Streaming\Representation;

ini_set('max_execution_time', 0); // 禁用 PHP 執行時間限制

class VideoFileToDashJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    #[ArrayShape([
        'timeout' => 'int',
        'ffmpeg.threads' => 'int',
        'temporary_directory' => 'string',
        'ffmpeg.binaries' => 'string',
        'ffprobe.binaries' => 'string',
    ])]
    private array $config;

    public function __construct()
    {
        $applicationStorage = Storage::disk('Applications');

        $this->config = [
            'timeout' => 60 * 60 * 24 * 365,
            'ffmpeg.threads' => Config::get('app.videoToDashCPUCoreUsed', 1),
            'temporary_directory' => storage_path('framework/cache'),
            // The number of threads that FFmpeg should use
        ];

        if ($this->isWindows()) {
            $this->config['ffmpeg.binaries'] = $applicationStorage->path(Config::get('app.ffmpegBinariesFileName',
                'ffmpeg-2025-01-27-959b799c8b.exe'));
            $this->config['ffprobe.binaries'] = $applicationStorage->path(Config::get('app.ffprobeBinariesFileName',
                'ffprobe-2025-01-27-959b799c8b.exe'));
        }

        if ($this->isLinux()) {
            $this->config['ffmpeg.binaries'] = $applicationStorage->path(Config::get('app.ffmpegBinariesFileName',
                'ffmpeg'));
            $this->config['ffprobe.binaries'] = $applicationStorage->path(Config::get('app.ffprobeBinariesFileName',
                'ffprobe'));
        }

    }

    public function isWindows(): bool
    {
        return Config::get('app.platform') === "Windows";
    }

    public function isLinux(): bool
    {
        return Config::get('app.platform') === "Linux";
    }


    /**
     * Parses FFmpeg output and extracts key information such as frame count, FPS, quality factor (q),
     * file size, processing time, bitrate, speed, progress, estimated total frames, and completion percentage.
     *
     * The function processes the given FFmpeg output string, splits it into lines, and searches for the most
     * recent line containing progress information. It extracts relevant data using regular expressions and
     * calculates additional values such as total processing time in seconds, estimated total frames, and
     * completion percentage if sufficient information is available.
     *
     * @param string $output The raw FFmpeg output as a string.
     *
     * @return array An associative array containing the parsed FFmpeg output details:
     *               - 'frame': (int|null) Processed frame count.
     *               - 'fps': (int|null) Frames per second.
     *               - 'q': (float|null) Quality factor.
     *               - 'size': (string|null) File size in KiB.
     *               - 'time': (string|null) Processed time in HH:MM:SS.ms format.
     *               - 'time_in_seconds': (float|null) Total processed time in seconds.
     *               - 'bitrate': (string|null) Bitrate in kbits/s.
     *               - 'speed': (string|null) Processing speed as a multiplier (e.g., "2.5x").
     *               - 'progress': (int) Progress percentage (default 0 if not calculated).
     *               - 'estimated_total_frames': (int|null) Estimated total frame count if calculable.
     *               - 'completion_percentage': (float|null) Estimated percentage of completion.
     *               - 'raw_lines': (array) All lines of the parsed FFmpeg output.
     */
    #[ArrayShape([
        'frame' => 'int|null',
        'fps' => 'int|null',
        'q' => 'float|null',
        'size' => 'string|null',
        'time' => 'string|null',
        'time_in_seconds' => 'float|null',
        'bitrate' => 'string|null',
        'speed' => 'string|null',
        'progress' => 'int',
        'estimated_total_frames' => 'int|null',
        'completion_percentage' => 'float|null',
        'raw_lines' => 'array',
    ])]
    public function parseFFmpegOutput(
        string $output,
    ): array {
        $result = [
            'frame' => null,
            'fps' => null,
            'q' => null,
            'size' => null,
            'time' => null,
            'bitrate' => null,
            'speed' => null,
            'progress' => 0,
            'estimated_total_frames' => null,
            'completion_percentage' => null,
            'raw_lines' => [],
        ];

        // 分割輸出為多行
        $lines = explode("\n", $output);
        $result['raw_lines'] = $lines;

        // 尋找最後一個包含進度資訊的行
        $progressLine = '';
        foreach (array_reverse($lines) as $line) {
            if (str_contains($line, "frame=")) {
                $explode = explode("frame=", $line);
                $progressLine = "frame=".array_reverse($explode)[0];
                Log::debug("[JOBS]selected \$progressLine: " . $progressLine);
                break;
            }
        }

        // 如果找到進度行，解析其中的值
        if ($progressLine) {
            // 提取幀數
            if (preg_match('/frame=\s*(\d+)/', $progressLine, $matches)) {
                $result['frame'] = (int)$matches[1];
            }

            // 提取 FPS
            if (preg_match('/fps=\s*(\d+)/', $progressLine, $matches)) {
                $result['fps'] = (int)$matches[1];
            }

            // 提取品質因子
            if (preg_match('/q=\s*([\d\.]+)/', $progressLine, $matches)) {
                $result['q'] = (float)$matches[1];
            }

            // 提取檔案大小
            if (preg_match('/size=\s*(\d+)\s*KiB/', $progressLine, $matches)) {
                $result['size'] = (int)$matches[1] . ' KiB';
            }

            // 提取處理時間
            if (preg_match('/time=\s*(\d+:\d+:\d+\.\d+)/', $progressLine, $matches)) {
                $result['time'] = $matches[1];

                // 將時間轉換為秒
                list($hours, $minutes, $seconds) = explode(':', $result['time']);
                $totalSeconds = (int)$hours * 3600 + (int)$minutes * 60 + (float)$seconds;
                $result['time_in_seconds'] = $totalSeconds;
            }

            // 提取比特率
            if (preg_match('/bitrate=\s*([\d\.]+)\s*kbits\/s/', $progressLine, $matches)) {
                $result['bitrate'] = $matches[1] . ' kbits/s';
            }

            // 提取速度
            if (preg_match('/speed=\s*([\d\.]+)\s*x/', $progressLine, $matches)) {
                $result['speed'] = (float)$matches[1] . 'x';
            }

            // 估算總幀數和完成百分比
            // 如果有輸入影片的總時長，可以更精確地計算
            // 此處使用簡單的估算方法
            if (isset($result['time_in_seconds']) && isset($result['fps']) && $result['fps'] > 0) {
                // 尋找影片總時長資訊
                $durationLine = '';
                foreach ($lines as $line) {
                    if (strpos($line, 'time=') !== false) {
                        $durationLine = $line;
                        break;
                    }
                }

                if ($durationLine && preg_match('/time=\s*(\d+):(\d+):(\d+\.\d+)/', $durationLine, $matches)) {
                    $totalHours = (int)$matches[1];
                    $totalMinutes = (int)$matches[2];
                    $totalSeconds = (float)$matches[3];
                    $totalDuration = $totalHours * 3600 + $totalMinutes * 60 + $totalSeconds;

                    if ($totalDuration > 0) {
                        $result['estimated_total_frames'] = (int)($totalDuration * $result['fps']);
                        $result['progress'] = min(100,
                            round(($result['time_in_seconds'] / $totalDuration) * 100, 2));
                    }
                }
            }
        }

        return $result;
    }

    public function handle(): void
    {
        set_time_limit(60 * 60 * 24 * 365);
        Log::info("[JOBS]VideoFileToDashJob");
        Log::info("[JOBS]workDir: " . getcwd());
        $dashVideos = DashVideos::where('type', '=', 'wait')->get()->first();
        if ($dashVideos === null) {
            return;
        }
        $virtualFile = $dashVideos->virtualFile()->get()->first();

        $dashVideos->update([
            'type' => 'processing',
        ]);

        try {
            Cache::forget('pending_process_' . $dashVideos->id);
            Log::info("[JOBS]start processing " . $dashVideos->id);
            $object = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path));
            if (!($object instanceof CGBaseFile)) {
                throw new Exception("不支援轉換非檔案文件");
            }
            if (!$object->isSupportVideoFile()) {
                throw new Exception("不支援檔案類型");
            }

            $FFMPEG_streaming_log = $object->renameToNewInstance('ffmpeg-streaming.log');
            $tfilename = $object->getDirname() . '/ffmpeg-streaming.log';
            if (file_exists($tfilename)) {
                FileSystem::write($tfilename, "");
            }

            // 獲取檔案實際路徑
            $ffmpegLogPath = $FFMPEG_streaming_log->getPath();

            // 建立 Logger 並設置檔案路徑寫入日誌
            $FFMPEGLogger = new Logger('FFmpeg_Streaming');
            $FFMPEGLogger->pushHandler(new StreamHandler($ffmpegLogPath)); // 記錄到檔案路徑

            if (!file_exists($object->getDirname() . "/" . $object->getFilename() . "_output." . $object->getExtension())) {
                chdir($object->getDirname());
                Log::info("[JOBS]workDir: " . getcwd());
                $cwdObject = CGFileSystem::getCGFileObject(getcwd());
                Log::info("[JOBS]cwdObject: " . $cwdObject->getPath());
                $processVideoObject = $object->renameToNewInstance($object->getFilename() . "." . $virtualFile->extension);

                $ffmpeg = FFMpeg::create($this->config, $FFMPEGLogger);
                $copyed = $object->copyFile($processVideoObject);
                $processVideoObject->rebuild();
                Log::info("[JOBS]copyed: " . $copyed);
                $preparedVideoFilePath = $processVideoObject->getPath();
                Log::info("[JOBS]fullpath: " . $preparedVideoFilePath);

                // 添加浮水印
                // 使用 filter_complex 實現更精確的浮水印控制
                $hasAudio = $this->hasAudio($ffmpegLogPath, $preparedVideoFilePath);
                Log::info("[JOBS]hasAudio: " . $hasAudio);
                $watermarkImagePath = CGPathUtils::converterPathSlash(public_path('assets/images/watermark-cgcloud.png'));
                Log::info("[JOBS]watermarkImagePath: " . $watermarkImagePath);

                // 取得封面圖片
                $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));
                $watermarkedVideoPath = $watermarkedVideoObject->getPath();

                // 準備 filter_complex 命令
                $format = new \FFMpeg\Format\Video\X264();
                $videoFile = $ffmpeg->open($preparedVideoFilePath);

                try {
                    $kiloBitrate = $videoFile->getStreams()->videos()->first()->get('bit_rate') / 1000 * Config::get('app.videoToDashKiloBitrate');
                } catch (Exception $e) {

                }

                // 獲取視頻比特率並設置輸出視頻比特率
                try {
                    if (isset($kiloBitrate)) {
                        $format->setKiloBitrate($kiloBitrate);
                    }
                } catch (Exception $e) {
                    Log::error($e->getTraceAsString());
                }

                // 設置音頻參數
                if ($hasAudio) {
                    $format->setAudioKiloBitrate(128); // 只有在有音軌時設定
                } else {
                    $format->setAdditionalParameters(['-an']); // 無音軌時忽略音訊
                }

                // 保存處理後的視頻
                try {
                    $ffmpegPath = $this->config['ffmpeg.binaries'];
                    // 使用方法
                    $command = sprintf('%s -i %s -i %s -filter_complex "overlay=main_w-overlay_w-10:main_h-overlay_h-10" -codec:a copy -b:v %dk -threads %d %s',
                        escapeshellarg($ffmpegPath),
                        escapeshellarg($preparedVideoFilePath),
                        escapeshellarg($watermarkImagePath),
                        escapeshellarg($kiloBitrate ?? "5000"),
                        $this->config['ffmpeg.threads'],
                        escapeshellarg($watermarkedVideoPath),
                    );

                    Log::debug("[JOBS]executeCommandWithLiveOutput: " . $command);

                    $this->executeCommandWithLiveOutput($command, $dashVideos->id);
                    Log::info("[JOBS]executeCommandWithLiveOutput Done");

                    Log::info("[JOBS]saveWaterMarkVideo: " . $watermarkedVideoPath);
                } catch (Exception $e) {
                    Log::error("[JOBS]Error saving watermarked video: " . $e->getMessage());
                    throw $e;
                }

                $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));

                $this->makeThumbFile($dashVideos, $FFMPEGLogger, $watermarkedVideoObject);
            } else {
                Log::info("[JOBS]skip processing " . $dashVideos->id);
                $watermarkedVideoPath = $object->getDirname() . "/" . $object->getFilename() . "_output." . $object->getExtension();
                $watermarkedVideoObject = null;
                //$tempWaterMarkFilePath = null;
            }

            $analyze = $this->analyze($watermarkedVideoPath, $FFMPEGLogger);

            $path = $this->processed($virtualFile, $dashVideos, $FFMPEGLogger, $watermarkedVideoPath, $analyze);
            $CGBaseFolder = CGFileSystem::getCGFileObject($object->getDirname());
            if ($CGBaseFolder instanceof CGBaseFolder) {
                $allFiles = json_encode($CGBaseFolder->allFiles(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                FileSystem::write($object->getDirname() . "/allFiles.json", $allFiles);
            }
            $newFileName = pathinfo($path, PATHINFO_FILENAME);
            $newExtension = pathinfo($path, PATHINFO_EXTENSION);
            $size = filesize($path);
            Log::info("[JOBS]\$path: " . $path);

            $str = CGPathUtils::converterPathSlash(storage_path('app' . DIRECTORY_SEPARATOR . 'public') . DIRECTORY_SEPARATOR);
            $str1 = CGPathUtils::converterPathSlash($path);

            $str_replace = CGPathUtils::converterPathSlash(str_replace($str, '', $str1));
            $arr = [
                'type' => 'success',
                'path' => $str_replace,
                'filename' => $newFileName,
                'extension' => $newExtension,
                "size" => $size,
                'disk' => "public",
            ];
            $dashVideos->update(array_merge($arr, $analyze));

            //$FFMPEG_streaming_log->delete();

            if ($watermarkedVideoObject instanceof CGBaseFile) {
                $watermarkedVideoObject->delete();
            }
            //if ($tempWaterMarkFilePath && file_exists($tempWaterMarkFilePath)) {
            //    unlink($tempWaterMarkFilePath);
            //}
        } catch (Exception $e) {
            Log::error("[JOBS]" . $e->getMessage() . $e->getTraceAsString());
            $dashVideos->update([
                'type' => 'failed',
            ]);
        }
    }

    public function hasAudio($logPath, $filePath): bool
    {
        // 建立 Logger 並設置檔案路徑寫入日誌
        $log = new Logger('FFmpeg_Streaming');
        $log->pushHandler(new StreamHandler($logPath)); // 記錄到檔案路徑

        $ffmpeg = FFMpeg::create($this->config, $log);
        // 使用 ffprobe 分析檔案
        $streams = $ffmpeg->open($filePath);

        // 取出「所有音訊」串流
        $audioStreams = $streams->getStreams()->audios();

        // 檢查是否至少有一個音訊串流
        return $audioStreams->count() > 0;
    }

    public function executeCommandWithLiveOutput($command, $dashVideoId): void
    {

        $process = Process::timeout($this->config['timeout'])->start($command);

        while ($process->running()) {
            Log::debug("[JOBS]executeCommandWithLiveOutput process running");
            $errorOutput = $process->errorOutput();
            $tErrorOutput = $this->parseFFmpegOutput($errorOutput);
            Cache::put('ffmpeg_watermark_progress_' . $dashVideoId, $tErrorOutput['time'], now()->addMinutes(1));
            usleep(500000); // 100ms
        }

        $result = $process->wait()->output();
        Log::debug("[JOBS]executeCommandWithLiveOutput final output: " . $result);
        Log::debug("[JOBS]executeCommandWithLiveOutput process stop");
    }

    /**
     * @throws Exception
     */
    private function makeThumbFile(
        DashVideos $dashVideos,
        LoggerInterface $log,
        CGBaseFile|CGBaseFileObject $object,
    ): void {
        $ffmpeg = FFMpeg::create($this->config, $log);

        $fullpath = $object->getPath();

        // 開啟影片檔案
        $video = $ffmpeg->open($fullpath);

        // 例如：取得影片在第 10 秒的影格
        $timeCode = TimeCode::fromSeconds(1);
        $frame = $video->frame($timeCode);

        $saveThumbObject = null;
        try {
            $saveThumbObject = $object->renameToNewInstance($object->getFilename() . '_thumb%03d.jpg');
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$object->renameToNewInstance => " . $e->getMessage());
        }
        if ($saveThumbObject === null) {
            throw new Exception("saveThumbObject is null");
        }
        $saveThumbPath = $saveThumbObject->getPath();

        Log::info("[JOBS]saveThumbPath(before): " . $saveThumbPath);

        // 儲存擷取的影格圖片
        $frame->save($saveThumbPath);

        sleep(3);

        try {
            $saveThumbObject = CGFileSystem::getCGFileObject($saveThumbObject->getDirname() . DIRECTORY_SEPARATOR . $object->getFilename() . '_thumb001.jpg');
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::CGFileSystem::getCGFileObject => " . $e->getMessage());
        }
        $saveThumbPath = $saveThumbObject->getPath();

        Log::info("[JOBS]saveThumbPath(after): " . $saveThumbPath);

        $path = $saveThumbObject->getPath();
        $size = filesize($path);
        $mimeType = mime_content_type($path);

        $saveThumbObject2 = null;
        try {
            $saveThumbObject2 = $saveThumbObject->renameToNewInstance($saveThumbObject->getFilename() . '_thumb.jpg',
                true);
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$saveThumbObject2->renameToNewInstance => " . $e->getMessage());
        }
        if ($saveThumbObject2 === null) {
            throw new Exception("saveThumbObject2 is null");
        }
        $saveThumbPath2 = $saveThumbObject2->getPath();

        $path2 = $saveThumbPath2;
        Log::info("[JOBS]path: " . $path);
        Log::info("[JOBS]path2: " . $path2);

        $path2 = CGPathUtils::converterPathSlash($path2);
        $replaceString = CGPathUtils::converterPathSlash(storage_path('app') . DIRECTORY_SEPARATOR);
        $path2 = str_replace($replaceString, '', $path2);

        $uuid = Uuid::uuid4()->toString();
        $attributes = [
            'uuid' => $uuid,
            'disk' => 'local',
            'path' => $path2,
            'type' => 'persistent',
            'filename' => basename($path2),
            "size" => $size,
            'extension' => "jpg",
            'minetypes' => $mimeType,
            'expired_at' => -1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        Log::info("[JOBS]attributes(json): " . json_encode($attributes, JSON_PRETTY_PRINT));
        $vf = VirtualFile::createOrFirst($attributes);
        $dashVideos->update([
            'thumb_virtual_file_uuid' => $vf->uuid,
        ]);
    }

    public function processed(VirtualFile $virtualFile, DashVideos $dashVideos, $log, $fullpath, array $analyze)
    {
        $ffmpeg = StreamingFFMpeg::create($this->config, $log);

        if ($analyze['width'] > $analyze['height']) {
            $r_144p = (new Representation())->setKiloBitrate(80)->setResize(256, 144);
            $r_240p = (new Representation())->setKiloBitrate(150)->setResize(426, 240);
            $r_360p = (new Representation())->setKiloBitrate(300)->setResize(640, 360);
            $r_480p = (new Representation())->setKiloBitrate(500)->setResize(854, 480);
            $r_720p = (new Representation())->setKiloBitrate(1500)->setResize(1280, 720);
            $r_1080p = (new Representation())->setKiloBitrate(3000)->setResize(1920, 1080);
        } else if ($analyze['width'] < $analyze['height']) {
            $r_144p = (new Representation())->setKiloBitrate(80)->setResize(144, 256);
            $r_240p = (new Representation())->setKiloBitrate(150)->setResize(240, 426);
            $r_360p = (new Representation())->setKiloBitrate(300)->setResize(360, 640);
            $r_480p = (new Representation())->setKiloBitrate(500)->setResize(480, 854);
            $r_720p = (new Representation())->setKiloBitrate(1500)->setResize(720, 1280);
            $r_1080p = (new Representation())->setKiloBitrate(3000)->setResize(1080, 1920);
        } else {
            $r_144p = (new Representation())->setKiloBitrate(80)->setResize(144, 144);
            $r_240p = (new Representation())->setKiloBitrate(150)->setResize(240, 240);
            $r_360p = (new Representation())->setKiloBitrate(300)->setResize(360, 360);
            $r_480p = (new Representation())->setKiloBitrate(500)->setResize(480, 480);
            $r_720p = (new Representation())->setKiloBitrate(1500)->setResize(720, 720);
            $r_1080p = (new Representation())->setKiloBitrate(3000)->setResize(1080, 1080);
        }

        $format = new X264();
        $start_time = 0;

        $percentage_to_time_left = function ($percentage) use (&$start_time) {
            if ($start_time === 0) {
                $start_time = time();
                return "Calculating...";
            }

            $diff_time = time() - $start_time;
            if ($percentage == 0) {
                $percentage = (float)0.01;
            }
            $seconds_left = (int)(100 * $diff_time / $percentage - $diff_time);
            //var_dump($seconds_left);

            return gmdate("H:i:s", $seconds_left);
        };

        $format->on('progress', function ($video, $format, $percentage) use ($percentage_to_time_left, $dashVideos) {
            // You can update a field in your database or can log it to a file
            // You can also create a socket connection and show a progress bar to users
            $a = sprintf("Transcoding Streaming...(%s%%) %s [%s%s]", $percentage, $percentage_to_time_left($percentage),
                str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
            Log::info($a);
            Cache::put('ffmpeg_streaming_progress_' . $dashVideos->id, $percentage, now()->addMinutes(5));
            if (Cache::has('pending_process_' . $dashVideos->id)) {
                Cache::forget('pending_process_' . $dashVideos->id);
            }
            dump($a);
        });

        $video = $ffmpeg->open($fullpath);
        $filename = pathinfo($virtualFile->path, PATHINFO_FILENAME);
        /** @var ShareTableVirtualFile $shareTableVirtualFile */
        $shareTableVirtualFile = $dashVideos->shareTableVirtualFile()->getResults();
        /** @var ShareTable $shareTable */
        $shareTable = $shareTableVirtualFile->shareTable()->getResults();

        $saveDashPath = Storage::disk('public')->path("DashVideos/" . $shareTable->id . '/' . $filename . ".mpd");
        $video->dash()->setFormat($format)->setSegDuration(3) // Default value is 10
        //->setAdaption('id=0,streams=v id=1,streams=a')
        //->x264()
        ->addRepresentations([
            $r_144p,
            $r_240p,
            $r_360p,
            $r_480p,
            $r_720p,
            $r_1080p,
        ])->save($saveDashPath);
        Log::info("[JOBS]saveDashFilePath: " . $saveDashPath);
        return $saveDashPath;
    }

    #[ArrayShape([
        'format' => 'string',
        'audioCodec' => 'string',
        'videoCodec' => 'string',
        'width' => 'integer',
        'height' => 'integer',
        'framerate' => 'string',
        'bitrate' => 'string',
        'duration' => 'integer',
        'channels' => 'string',
        'sampleRate' => 'string',
        'videoFrames' => 'string',
        'metadata' => 'string',
        'videoStream' => 'string',
        'audioStream' => 'string',
    ])]
    private function analyze(string $videoFilePath, ?LoggerInterface $log): array
    {
        $object = CGFileSystem::getCGFileObject($videoFilePath);
        if ($object instanceof CGBaseFile) {
            $ffprobe = FFProbe::create($this->config, $log);

            // 取得影片 metadata
            $format = $ffprobe->format($object->getPath());
            $streams = $ffprobe->streams($object->getPath());

            // 基本格式資訊
            $formatName = $format->get('format_name');
            $duration = $format->get('duration');
            $bitrate = $format->get('bit_rate');
            $metadata = $format->get('tags');

            // 取得 video stream
            $videoStream = $streams->videos();
            if ($videoStream !== null) {
                $vs = $videoStream->first();
                if ($vs !== null) {
                    $videoCodec = $vs->get('codec_name');
                    $width = $vs->get('width');
                    $height = $vs->get('height');
                    list($num, $den) = explode('/', $vs->get('r_frame_rate'));
                    $framerate = $den != 0 ? $num / $den : 0;
                    $videoFrames = $vs->get('nb_frames');
                }
            }

            // 取得 audio stream
            $audioStream = $streams->audios();
            if ($audioStream !== null) {
                $AS = $audioStream->first();
                if ($AS !== null) {
                    $audioCodec = $AS->get('codec_name');
                    $channels = $AS->get('channels');
                    $sampleRate = $AS->get('sample_rate');
                }
            }

            // 輸出所有資訊
            return [
                'format' => $formatName,
                'audioCodec' => $audioCodec ?? "",
                'videoCodec' => $videoCodec ?? "",
                'width' => intval($width ?? 0),
                'height' => intval($height ?? 0),
                'framerate' => $framerate ?? 0,
                'bitrate' => $bitrate,
                'duration' => intval($duration),
                'channels' => $channels ?? 0,
                'sampleRate' => $sampleRate ?? 0,
                'videoFrames' => $videoFrames ?? 0,
                'metadata' => $metadata,
                'videoStream' => json_encode($videoStream->all()),
                'audioStream' => json_encode($audioStream->all()),
            ];
        } else {
            return [];
        }
    }
}
