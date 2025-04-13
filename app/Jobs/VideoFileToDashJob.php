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

    private array $config;

    public function __construct()
    {
        $applicationStorage = Storage::disk('Applications');

        $this->config = [
            'timeout' => 60 * 60 * 24 * 365,
            'ffmpeg.threads' => Config::get('app.videoToDashCPUCoreUsed'),
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

                $ffmpeg = StreamingFFMpeg::create($this->config, $FFMPEGLogger);
                $copyed = $object->copyFile($processVideoObject);
                $processVideoObject->rebuild();
                Log::info("[JOBS]copyed: " . $copyed);
                $preparedVideoFilePath = $processVideoObject->getPath();
                Log::info("[JOBS]fullpath: " . $preparedVideoFilePath);

                // 添加浮水印
                // FFMPEG 多數錯誤問題跟 watermark 路徑有關
                $hasAudio = $this->hasAudio($ffmpegLogPath, $preparedVideoFilePath);
                Log::info("[JOBS]hasAudio: " . $hasAudio);
                $pipLineStream = $ffmpeg->open($preparedVideoFilePath);
                $watermarkImagePath = public_path('assets/images/watermark-cgcloud.png');
                Log::info("[JOBS]watermarkImagePath: " . $watermarkImagePath);
                $tempWaterMarkFilePath = './watermark.png';
                FileSystem::copy($watermarkImagePath, $tempWaterMarkFilePath);
                $pipLineStream->filters()->watermark($tempWaterMarkFilePath)->synchronize();

                // 取得封面圖片
                $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));
                $watermarkedVideoPath = $watermarkedVideoObject->getPath();
                $format = new \FFMpeg\Format\Video\X264();
                $kiloBitrate = $pipLineStream->getFormat()->get('bit_rate') / 1000 * Config::get('app.videoToDashKiloBitrate'); // 轉換為 Kbps
                $format->setKiloBitrate($kiloBitrate);

                if ($hasAudio) {
                    $format->setAudioKiloBitrate(128); // 只有在有音軌時設定
                } else {
                    $format->setAdditionalParameters(['-an']); // 無音軌時忽略音訊
                }
                $start_time = 0;

                $percentage_to_time_left = function ($percentage) use (&$start_time) {
                    if ($start_time === 0) {
                        $start_time = time();
                        return "Calculating...";
                    }

                    $diff_time = time() - $start_time;
                    if ($percentage == 0) {
                        $percentage = 0.01;
                    }
                    $seconds_left = (int)(100 * $diff_time / $percentage - $diff_time);
                    //var_dump($seconds_left);

                    return @gmdate("H:i:s", $seconds_left);
                };
                $format->on('progress',
                    function ($video, $format, $percentage) use ($percentage_to_time_left, $dashVideos) {
                        // You can update a field in your database or can log it to a file
                        // You can also create a socket connection and show a progress bar to users
                        $a = sprintf("\rTranscoding watermark...(%s%%) %s [%s%s]", $percentage,
                            $percentage_to_time_left($percentage), str_repeat('#', $percentage),
                            str_repeat('-', (100 - $percentage)));
                        Log::info($a);
                        Cache::put('ffmpeg_watermark_progress_' . $dashVideos->id, $percentage, now()->addMinutes(2));
                        //dump($a);
                    });
                $saveWaterMarkVideo = $pipLineStream->save($format, $watermarkedVideoPath)->getPathfile();
                Log::info("[JOBS]saveWaterMarkVideo: " . $saveWaterMarkVideo);

                $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));

                $this->makeThumbFile($virtualFile, $dashVideos, $FFMPEGLogger, $watermarkedVideoObject);
            } else {
                Log::info("[JOBS]skip processing " . $dashVideos->id);
                $watermarkedVideoPath = $object->getDirname() . "/" . $object->getFilename() . "_output." . $object->getExtension();
                $watermarkedVideoObject = null;
                $tempWaterMarkFilePath = null;
            }


            $path = $this->proccessed($virtualFile, $dashVideos, $FFMPEGLogger, $watermarkedVideoPath);
            $CGBaseFolder = CGFileSystem::getCGFileObject($object->getDirname());
            if ($CGBaseFolder instanceof CGBaseFolder) {
                $allFiles = json_encode($CGBaseFolder->allFiles(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                FileSystem::write($object->getDirname() . "/allFiles.json", $allFiles);
            }
            $analyze = $this->analyze($watermarkedVideoPath, $FFMPEGLogger);
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

            $FFMPEG_streaming_log->delete();

            if ($watermarkedVideoObject instanceof CGBaseFile) {
                $watermarkedVideoObject->delete();
            }
            if ($tempWaterMarkFilePath && file_exists($tempWaterMarkFilePath)) {
                unlink($tempWaterMarkFilePath);
            }
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

    private function makeThumbFile(
        VirtualFile $virtualFile,
        DashVideos $dashVideos,
        LoggerInterface $log,
        CGBaseFile|CGBaseFileObject $object,
    ) {
        $ffmpeg = FFMpeg::create($this->config, $log);

        $fullpath = $object->getPath();

        // 開啟影片檔案
        $video = $ffmpeg->open($fullpath);

        // 例如：取得影片在第 10 秒的影格
        $timeCode = TimeCode::fromSeconds(1);
        $frame = $video->frame($timeCode);

        try {
            $saveThumbObject = $object->renameToNewInstance($object->getFilename() . '_thumb%03d.jpg');
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$object->renameToNewInstance => " . $e->getMessage());
        }
        $saveThumbPath = $saveThumbObject->getPath();

        Log::info("[JOBS]saveThumbPath(before): " . $saveThumbPath);

        // 儲存擷取的影格圖片
        $frame->save($saveThumbPath);

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

        try {
            $saveThumbObject2 = $saveThumbObject->renameToNewInstance($saveThumbObject->getFilename() . '_thumb.jpg',
                true);
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$saveThumbObject2->renameToNewInstance => " . $e->getMessage());
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

    public function proccessed(VirtualFile $virtualFile, DashVideos $dashVideos, $log, $fullpath)
    {
        $ffmpeg = StreamingFFMpeg::create($this->config, $log);

        $r_144p = (new Representation())->setKiloBitrate(80)->setResize(256, 144);
        $r_240p = (new Representation())->setKiloBitrate(150)->setResize(426, 240);
        $r_360p = (new Representation())->setKiloBitrate(300)->setResize(640, 360);
        $r_480p = (new Representation())->setKiloBitrate(500)->setResize(854, 480);
        $r_720p = (new Representation())->setKiloBitrate(1500)->setResize(1280, 720);
        $r_1080p = (new Representation())->setKiloBitrate(3000)->setResize(1920, 1080);

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
            Cache::put('ffmpeg_streaming_progress_' . $dashVideos->id, $percentage, now()->addMinutes(2));
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
            $videoStream = $streams->videos()->first();
            $videoCodec = $videoStream->get('codec_name');
            $width = $videoStream->get('width');
            $height = $videoStream->get('height');
            list($num, $den) = explode('/', $videoStream->get('r_frame_rate'));
            $framerate = $den != 0 ? $num / $den : 0;
            $videoFrames = $videoStream->get('nb_frames');

            // 取得 audio stream
            $audioStream = $streams->audios()->first();
            $audioCodec = $audioStream->get('codec_name');
            $channels = $audioStream->get('channels');
            $sampleRate = $audioStream->get('sample_rate');

            // 輸出所有資訊
            $info = [
                'format' => $formatName,
                'audioCodec' => $audioCodec,
                'videoCodec' => $videoCodec,
                'width' => intval($width),
                'height' => intval($height),
                'framerate' => $framerate,
                'bitrate' => $bitrate,
                'duration' => intval($duration),
                'channels' => $channels,
                'sampleRate' => $sampleRate,
                'videoFrames' => $videoFrames,
                'metadata' => $metadata,
                'videoStream' => json_encode($videoStream->all()),
                'audioStream' => json_encode($audioStream->all()),
            ];
            return $info;
        } else {
            return [];
        }
    }
}
