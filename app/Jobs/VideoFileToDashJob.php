<?php

namespace App\Jobs;

use App\Lib\Utils\CGFileSystem\CGBaseFile;
use App\Lib\Utils\CGFileSystem\CGBaseFileObject;
use App\Lib\Utils\CGFileSystem\CGFileSystem;
use App\Lib\Utils\CGFileSystem\CGPathUtils;
use App\Models\DashVideos;
use App\Models\ShareTable;
use App\Models\ShareTableVirtualFile;
use App\Models\VirtualFile;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nette\Utils\FileSystem;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Streaming\FFMpeg;
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
            'timeout'          => 60*60*24*365,
            'ffmpeg.threads'   => 8,   // The number of threads that FFmpeg should use
        ];

        if (Config::get('app.platform') === "Windows") {
            $this->config['ffmpeg.binaries'] = $applicationStorage->path('ffmpeg-2025-01-27-959b799c8b.exe');
            $this->config['ffprobe.binaries'] = $applicationStorage->path('ffprobe-2025-01-27-959b799c8b.exe');
        }

        if (Config::get('app.platform') === "Linux") {
            $this->config['ffmpeg.binaries'] = $applicationStorage->path('ffmpeg');
            $this->config['ffprobe.binaries'] = $applicationStorage->path('ffprobe');
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

    public function hasAudio($logPath, $filePath): bool
    {
        // 建立 Logger 並設置檔案路徑寫入日誌
        $log = new Logger('FFmpeg_Streaming');
        $log->pushHandler(new StreamHandler($logPath)); // 記錄到檔案路徑

        $ffmpeg = \FFMpeg\FFMpeg::create($this->config, $log);
        // 使用 ffprobe 分析檔案
        $streams = $ffmpeg->open($filePath);

        // 取出「所有音訊」串流
        $audioStreams = $streams->getStreams()->audios();

        // 檢查是否至少有一個音訊串流
        return $audioStreams->count() > 0;
    }

    public function handle(): void
    {
        set_time_limit(60*60*24*365);
        Log::info("[JOBS]VideoFileToDashJob");
        Log::info("[JOBS]workDir: ".getcwd());
        $dashVideos = DashVideos::where('type', '=', 'wait')->get()->first();
        if($dashVideos === null){
            return;
        }
        $virtualFile = $dashVideos->virtualFile()->get()->first();

        $dashVideos->update([
            'type' => 'processing',
        ]);

        try {
            Log::info("[JOBS]start processing ".$dashVideos->id);
            $object = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path));
            if(!($object instanceof CGBaseFile)) return;
            if(!$object->isSupportVideoFile()) return;

            chdir($object->getDirname());
            Log::info("[JOBS]workDir: ".getcwd());
            $cwdObject = CGFileSystem::getCGFileObject(getcwd());
            Log::info("[JOBS]cwdObject: ".$cwdObject->getPath());
            $processVideoObject = $object->renameToNewInstance($object->getFilename().".".$virtualFile->extension);

            $FFMPEG_streaming_log = $object->renameToNewInstance('ffmpeg-streaming.log');
            $tfilename = $object->getDirname() . '/ffmpeg-streaming.log';
            if(file_exists($tfilename)){
                FileSystem::write($tfilename, "");
            }

            // 獲取檔案實際路徑
            $ffmpegLogPath = $FFMPEG_streaming_log->getPath();

            // 建立 Logger 並設置檔案路徑寫入日誌
            $FFMPEGLogger = new Logger('FFmpeg_Streaming');
            $FFMPEGLogger->pushHandler(new StreamHandler($ffmpegLogPath)); // 記錄到檔案路徑

            $ffmpeg = FFMpeg::create($this->config, $FFMPEGLogger);
            $copyed = $object->copyFile($processVideoObject);
            $processVideoObject->rebuild();
            Log::info("[JOBS]copyed: ".$copyed);
            $preparedVideoFilePath = $processVideoObject->getPath();
            Log::info("[JOBS]fullpath: ".$preparedVideoFilePath);

            // 添加浮水印
            // FFMPEG 多數錯誤問題跟 watermark 路徑有關
            $hasAudio = $this->hasAudio($ffmpegLogPath, $preparedVideoFilePath);
            Log::info("[JOBS]hasAudio: ".$hasAudio);
            $pipLineStream = $ffmpeg->open($preparedVideoFilePath);
            $watermarkImagePath = public_path('assets/images/watermark-cgcloud.png');
            Log::info("[JOBS]watermarkImagePath: ".$watermarkImagePath);
            $tempWaterMarkFilePath = './watermark.png';
            FileSystem::copy($watermarkImagePath, $tempWaterMarkFilePath);
            $pipLineStream->filters()
                ->watermark($tempWaterMarkFilePath)
                ->synchronize();

            // 取得封面圖片
            $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));
            $watermarkedVideoPath = $watermarkedVideoObject->getPath();
            $format = new \FFMpeg\Format\Video\X264();
            $kiloBitrate = $pipLineStream->getFormat()->get('bit_rate') / 1000 * 0.7; // 轉換為 Kbps
            $format->setKiloBitrate($kiloBitrate);

            if ($hasAudio) {
                $format->setAudioKiloBitrate(128); // 只有在有音軌時設定
            } else {
                $format->setAdditionalParameters(['-an']); // 無音軌時忽略音訊
            }
            $start_time = 0;

            $percentage_to_time_left = function ($percentage) use (&$start_time) {
                if($start_time === 0){
                    $start_time = time();
                    return "Calculating...";
                }

                $diff_time = time() - $start_time;
                if($percentage==0){
                    $percentage = 0.01;
                }
                $seconds_left = (int)(100 * $diff_time / $percentage - $diff_time);
                //var_dump($seconds_left);

                return @gmdate("H:i:s", $seconds_left);
            };
            $format->on('progress', function ($video, $format, $percentage) use($percentage_to_time_left, $dashVideos) {
                // You can update a field in your database or can log it to a file
                // You can also create a socket connection and show a progress bar to users
                $a = sprintf("\rTranscoding watermark...(%s%%) %s [%s%s]", $percentage, $percentage_to_time_left($percentage), str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
                Log::info($a);
                Cache::put('ffmpeg_watermark_progress_'.$dashVideos->id, $percentage, now()->addMinutes(2));
                //dump($a);
            });
            $saveWaterMarkVideo = $pipLineStream->save(
                $format,
                $watermarkedVideoPath,
            )->getPathfile();
            Log::info("[JOBS]saveWaterMarkVideo: ".$saveWaterMarkVideo);

            $watermarkedVideoObject = CGFileSystem::getCGFileObject(Storage::disk($virtualFile->disk)->path($virtualFile->path . "_output." . $virtualFile->extension));

            $this->makeThumbFile($virtualFile, $dashVideos, $FFMPEGLogger, $watermarkedVideoObject);

            $path = $this->proccessed($virtualFile, $dashVideos, $FFMPEGLogger, $watermarkedVideoPath);
            $newFileName = pathinfo($path, PATHINFO_FILENAME);
            $newExtension = pathinfo($path, PATHINFO_EXTENSION);
            $size = filesize($path);
            Log::info("[JOBS]\$path: ".$path);


            $str = CGPathUtils::converterPathSlash(storage_path('app'.DIRECTORY_SEPARATOR.'public').DIRECTORY_SEPARATOR) ;
            $str1 = CGPathUtils::converterPathSlash($path) ;

            $str_replace = CGPathUtils::converterPathSlash(str_replace($str, '', $str1));
            $dashVideos->update([
                'type' => 'success',
                'path' => $str_replace,
                'filename' => $newFileName,
                'extension' => $newExtension,
                "size" => $size,
                'disk' => "public",
            ]);

            $FFMPEG_streaming_log->delete();
            $watermarkedVideoObject->delete();
            unlink($tempWaterMarkFilePath);
        } catch (Exception $e) {
            Log::error("[JOBS]".$e->getMessage().
                $e->getTraceAsString());
            $dashVideos->update([
                'type' => 'failed',
            ]);
        }
    }

    public function proccessed(VirtualFile $virtualFile, DashVideos $dashVideos, $log, $fullpath)
    {
        $ffmpeg = FFMpeg::create($this->config, $log);

        $r_144p  = (new Representation())->setKiloBitrate(80)->setResize(256, 144);
        $r_240p  = (new Representation())->setKiloBitrate(150)->setResize(426, 240);
        $r_360p  = (new Representation())->setKiloBitrate(300)->setResize(640, 360);
        $r_480p  = (new Representation())->setKiloBitrate(500)->setResize(854, 480);
        $r_720p  = (new Representation())->setKiloBitrate(1500)->setResize(1280, 720);
        $r_1080p = (new Representation())->setKiloBitrate(3000)->setResize(1920, 1080);

        $format = new X264();
        $start_time = 0;

        $percentage_to_time_left = function ($percentage) use (&$start_time) {
            if($start_time === 0){
                $start_time = time();
                return "Calculating...";
            }

            $diff_time = time() - $start_time;
            if($percentage==0){
                $percentage = (float)0.01;
            }
            $seconds_left = (int)(100 * $diff_time / $percentage - $diff_time);
            //var_dump($seconds_left);

            return gmdate("H:i:s", $seconds_left);
        };

        $format->on('progress', function ($video, $format, $percentage) use($percentage_to_time_left, $dashVideos) {
            // You can update a field in your database or can log it to a file
            // You can also create a socket connection and show a progress bar to users
            $a = sprintf("Transcoding Streaming...(%s%%) %s [%s%s]", $percentage, $percentage_to_time_left($percentage), str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
            Log::info($a);
            Cache::put('ffmpeg_streaming_progress_'.$dashVideos->id, $percentage, now()->addMinutes(2));
            dump($a);
        });

        $video = $ffmpeg->open($fullpath);
        $filename = pathinfo($virtualFile->path, PATHINFO_FILENAME);
        /** @var ShareTableVirtualFile $shareTableVirtualFile */
        $shareTableVirtualFile = $dashVideos->shareTableVirtualFile()->getResults();
        /** @var ShareTable $shareTable */
        $shareTable = $shareTableVirtualFile->shareTable()->getResults();

        $saveDashPath = Storage::disk('public')->path("DashVideos/" . $shareTable->id . '/' . $filename . ".mpd");
        $video->dash()
            ->setFormat($format)
            ->setSegDuration(3) // Default value is 10
            //->setAdaption('id=0,streams=v id=1,streams=a')
            //->x264()
            ->addRepresentations([
                $r_144p,
                $r_240p,
                $r_360p,
                $r_480p,
                $r_720p,
                $r_1080p
            ])
            ->save(
                $saveDashPath
            );
        Log::info("[JOBS]saveDashFilePath: ". $saveDashPath);
        return $saveDashPath;
    }

    private function makeThumbFile(VirtualFile $virtualFile, DashVideos $dashVideos, LoggerInterface $log, CGBaseFile | CGBaseFileObject $object)
    {
        $ffmpeg = \FFMpeg\FFMpeg::create($this->config, $log);

        $fullpath = $object->getPath();

        // 開啟影片檔案
        $video = $ffmpeg->open($fullpath);

        // 例如：取得影片在第 10 秒的影格
        $timeCode = TimeCode::fromSeconds(1);
        $frame = $video->frame($timeCode);

        try {
            $saveThumbObject = $object->renameToNewInstance($object->getFilename() . '_thumb%03d.jpg');
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$object->renameToNewInstance => ".$e->getMessage());
        }
        $saveThumbPath = $saveThumbObject->getPath();

        Log::info("[JOBS]saveThumbPath(before): ". $saveThumbPath);

        // 儲存擷取的影格圖片
        $frame->save($saveThumbPath);

        try {
            $saveThumbObject =  CGFileSystem::getCGFileObject($saveThumbObject->getDirname().DIRECTORY_SEPARATOR.$object->getFilename() . '_thumb001.jpg');
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::CGFileSystem::getCGFileObject => ".$e->getMessage());
        }
        $saveThumbPath = $saveThumbObject->getPath();

        Log::info("[JOBS]saveThumbPath(after): ". $saveThumbPath);

        $path = $saveThumbObject->getPath();
        $size = filesize($path);
        $mimeType = mime_content_type($path);

        try {
            $saveThumbObject2 = $saveThumbObject->renameToNewInstance($saveThumbObject->getFilename() . '_thumb.jpg', true);
        } catch (Exception $e) {
            Log::error("[JOBS]makeThumbFile::\$saveThumbObject2->renameToNewInstance => ".$e->getMessage());
        }
        $saveThumbPath2 = $saveThumbObject2->getPath();

        $path2 = $saveThumbPath2;
        Log::info("[JOBS]path: ". $path);
        Log::info("[JOBS]path2: ". $path2);

        $path2 = CGPathUtils::converterPathSlash($path2);
        $replaceString = CGPathUtils::converterPathSlash(storage_path('app'.DIRECTORY_SEPARATOR.'public').DIRECTORY_SEPARATOR);
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
        Log::info("[JOBS]attributes(json): ".json_encode($attributes,JSON_PRETTY_PRINT));
        $vf = VirtualFile::createOrFirst($attributes);
        $dashVideos->update([
            'thumb_virtual_file_uuid' => $vf->uuid,
        ]);
    }
}
