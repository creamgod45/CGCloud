<?php

namespace App\Jobs;

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
use Ramsey\Uuid\Uuid;
use Streaming\FFMpeg;
use Streaming\Format\X264;
use Streaming\Representation;

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
            'ffmpeg.threads'   => 2,   // The number of threads that FFmpeg should use
        ];

        if (Config::get('app.platform') === "Windows") {
            $this->config['ffmpeg.binaries'] = $applicationStorage->path('ffmpeg-2025-01-27-959b799c8b.exe');
            $this->config['ffprobe.binaries'] = $applicationStorage->path('ffprobe-2025-01-27-959b799c8b.exe');
        }

    }

    public function handle(): void
    {
        set_time_limit(60*60*24*365);
        Log::info("[JOBS]VideoFileToDashJob");
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
            dump("start processing ".$dashVideos->id);
            $filename = pathinfo($virtualFile->path, PATHINFO_FILENAME);

            $tfilename = str_replace($filename, '', $virtualFile->path) . '/ffmpeg-streaming.log';
            if(!Storage::disk($virtualFile->disk)->exists($tfilename)){
                Storage::disk($virtualFile->disk)->put($tfilename, "");
            }
            // 獲取檔案實際路徑
            $filePath = Storage::disk($virtualFile->disk)->path($tfilename);
            $filePath = str_replace('\\', '/', $filePath);

            // 建立 Logger 並設置檔案路徑寫入日誌
            $log = new Logger('FFmpeg_Streaming');
            $log->pushHandler(new StreamHandler($filePath)); // 記錄到檔案路徑

            $ffmpeg1 = FFMpeg::create($this->config, $log);
            $copyed = Storage::disk($virtualFile->disk)->copy($virtualFile->path, $virtualFile->path.".".$virtualFile->extension);
            Log::info("[JOBS]copyed: ".$copyed);
            $fullpath = Storage::disk($virtualFile->disk)->path($virtualFile->path.".".$virtualFile->extension);
            $fullpath = str_replace('\\', '/', $fullpath);
            Log::info("[JOBS]fullpath: ".$fullpath);

            // 添加浮水印
            $pipLineStream = $ffmpeg1->open($fullpath);
            $watermarkImagePath = public_path('assets/images/watermark-cgcloud.png');
            FileSystem::copy($watermarkImagePath, './watermark.png');
            $pipLineStream->filters()
                ->watermark('./watermark.png')
                ->synchronize();

            // 取得封面圖片
            $disk = 'local';
            $fullpath2 = Storage::disk($virtualFile->disk)->path($virtualFile->path."_output.".$virtualFile->extension);
            $fullpath2 = str_replace('\\', '/', $fullpath2);
            $format = new \FFMpeg\Format\Video\X264();
            $kiloBitrate = $pipLineStream->getFormat()->get('bit_rate') / 1000 * 0.8; // 轉換為 Kbps
            $format->setKiloBitrate($kiloBitrate);
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
                $seconds_left = 100 * $diff_time / $percentage - $diff_time;
                //var_dump($seconds_left);

                return gmdate("H:i:s", $seconds_left);
            };
            $format->on('progress', function ($video, $format, $percentage) use($percentage_to_time_left, $dashVideos) {
                // You can update a field in your database or can log it to a file
                // You can also create a socket connection and show a progress bar to users
                $a = sprintf("\rTranscoding watermark...(%s%%) %s [%s%s]", $percentage, $percentage_to_time_left($percentage), str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
                Log::info($a);
                try {
                    Storage::disk('local')->put('ffmpeg_watermark_progress_'.$dashVideos->id, $percentage);
                    Log::info('Cache stored successfully.');
                } catch (Exception $exception) {
                    Log::error('Cache writing failed: ' . $exception->getMessage());
                }
                dump($a);
            });
            $saveWaterMarkVideo = $pipLineStream->save(
                $format,
                $fullpath2,
            )->getPathfile();
            Log::info("[JOBS]saveWaterMarkVideo: ".$saveWaterMarkVideo);

            $this->makeThumbFile( $virtualFile, $dashVideos, $log, $fullpath2);

            $path = $this->proccessed($virtualFile, $dashVideos, $log, $fullpath2);
            $newFileName = pathinfo($path, PATHINFO_FILENAME);
            $newExtension = pathinfo($path, PATHINFO_EXTENSION);
            $size = filesize($path);
            $dashVideos->update([
                'type' => 'success',
                'path' => str_replace(storage_path('app/public').'\\', '', $path), // @todo 修改過長的 path
                'filename' => $newFileName,
                'extension' => $newExtension,
                "size" => $size,
                'disk' => "public",
            ]);

            Storage::disk($virtualFile->disk)->delete($virtualFile->path.".".$virtualFile->extension);
            Storage::disk($virtualFile->disk)->delete(str_replace(pathinfo($virtualFile->path, PATHINFO_FILENAME), '', $virtualFile->path)."thumb001.jpg");
            Storage::disk($virtualFile->disk)->delete(str_replace(pathinfo($virtualFile->path, PATHINFO_FILENAME), '', $virtualFile->path)."ffmpeg-streaming.log");
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

        $r_144p  = (new Representation())->setKiloBitrate(95)->setResize(256, 144);
        $r_240p  = (new Representation())->setKiloBitrate(150)->setResize(426, 240);
        $r_360p  = (new Representation())->setKiloBitrate(276)->setResize(640, 360);
        $r_480p  = (new Representation())->setKiloBitrate(750)->setResize(854, 480);
        $r_720p  = (new Representation())->setKiloBitrate(2048)->setResize(1280, 720);
        $r_1080p = (new Representation())->setKiloBitrate(4096)->setResize(1920, 1080);

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
            $seconds_left = 100 * $diff_time / $percentage - $diff_time;
            //var_dump($seconds_left);

            return gmdate("H:i:s", $seconds_left);
        };

        $format->on('progress', function ($video, $format, $percentage) use($percentage_to_time_left, $dashVideos) {
            // You can update a field in your database or can log it to a file
            // You can also create a socket connection and show a progress bar to users
            $a = sprintf("Transcoding Streaming...(%s%%) %s [%s%s]", $percentage, $percentage_to_time_left($percentage), str_repeat('#', $percentage), str_repeat('-', (100 - $percentage)));
            Log::info($a);
            Storage::disk('local')->put('ffmpeg_streaming_progress_'.$dashVideos->id, $percentage);
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
        Log::info("[JOBS]path: ". $saveDashPath);
        return $saveDashPath;
    }

    private function makeThumbFile(VirtualFile $virtualFile, DashVideos $dashVideos, $log, $fullpath)
    {
        $ffmpeg = \FFMpeg\FFMpeg::create($this->config, $log);

        // 開啟影片檔案
        $video = $ffmpeg->open($fullpath);

        // 例如：取得影片在第 10 秒的影格
        $timeCode = TimeCode::fromSeconds(1);
        $frame = $video->frame($timeCode);

        $saveThumbPath = Storage::disk('local')->path(str_replace(pathinfo($virtualFile->path, PATHINFO_FILENAME), '', $virtualFile->path));
        $saveThumbPath = str_replace('\\', '/', $saveThumbPath);

        // 儲存擷取的影格圖片
        $frame->save($saveThumbPath.'thumb%03d.jpg');

        Log::info("[JOBS]saveThumbPath: ". $saveThumbPath);

        $path = $saveThumbPath . 'thumb001.jpg';
        $size = filesize($path);
        $mimeType = mime_content_type($path);
        $storage_path = str_replace("\\", '/', storage_path('app'));

        $path2 = storage_path('app').'/'.str_replace(pathinfo($virtualFile->path, PATHINFO_FILENAME), '',
                $virtualFile->path) . pathinfo($virtualFile->path, PATHINFO_FILENAME) . "_thumb.jpg";
        FileSystem::copy($path, $path2);
        Log::info("[JOBS]path: ". $path);
        Log::info("[JOBS]path2: ". $path2);

        $uuid = Uuid::uuid4()->toString();
        $attributes = [
            'uuid' => $uuid,
            'disk' => 'local',
            'path' => str_replace($storage_path.'/', '', $path2),
            'type' => 'persistent',
            'filename' => $virtualFile->filename . "_thumb.jpg",
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
