<?php

namespace App\Lib\Utils\CGFileSystem;

use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CGBaseFile extends CGBaseFileObject
{
    public function __construct(string $path, bool $defaultFileExists = false)
    {
        parent::__construct($path, $defaultFileExists);
    }

    public function isSupportImageFile()
    {
        $mimetype = strtolower(parent::getMimeType());
        $mimeTypes = explode('::',
            'image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif');
        if (in_array($mimetype, $mimeTypes)) {
            return true;
        }
        return false;
    }

    public function isSupportVideoFile()
    {
        $mimetype = strtolower(parent::getMimeType());
        $mimeTypes = explode('::',
            'video/av1::video/H264::video/H264-SVC::video/H264-RCDO::video/H265::video/JPEG::video/JPEG::video/mpeg::video/mpeg4-generic::video/ogg::video/quicktime::video/JPEG::video/vnd.mpegurl::video/vnd.youtube.yt::video/VP8::video/VP9::video/mp4::video/mp4V-ES::video/MPV::video/vnd.directv.mpeg::video/vnd.dece.mp4::video/vnd.uvvu.mp4::video/H266::video/H263::video/H263-1998::video/H263-2000::video/H261');
        if (in_array($mimetype, $mimeTypes)) {
            return true;
        }
        return false;
    }

    /**
     * @return CGBaseFile
     * @throws Exception
     */
    public function rebuild(): static
    {
        parent::__construct(parent::getPath());
        return $this;
    }

    /**
     * @param string $filename
     * @param bool   $moveFile
     * @param bool   $defaultFileExists
     *
     * @return CGBaseFile
     * @throws Exception
     */
    public function renameToNewInstance(
        string $filename,
        bool $moveFile = false,
        bool $defaultFileExists = false,
    ): CGBaseFile {
        $filename = basename($filename);
        if (str_starts_with($filename, '/')) {
            $filename = str_replace('/', '', $filename);
        }
        if (str_starts_with($filename, './')) {
            $filename = str_replace('./', '', $filename);
        }
        if (str_starts_with($filename, '\\')) {
            $filename = str_replace('\\', '', $filename);
        }
        $CGBaseFile = new CGBaseFile(parent::getDirname() . "/" . $filename, $defaultFileExists);
        if ($moveFile) {
            rename(parent::getPath(), $CGBaseFile->getPath());
        }
        return $CGBaseFile;
    }

    /**
     * 逐條讀取文件，並將其內容返回為行數組。
     *
     * @return false|array
     * @throws Exception
     */
    public function readFileWithLine(): false|array
    {
        // 確保目標是檔案
        if (!$this->isFile()) {
            throw new RuntimeException('無法開啟非檔案類型的資源');
        }

        // 檢查讀取權限
        if (!$this->isFileReadAccessed()) {
            throw new RuntimeException('沒有檔案的讀取權限');
        }
        return file(parent::getPath());
    }

    /**
     * 逐條讀取文件，並將其內容返回為行數組。
     *
     * @return false|array
     * @throws Exception
     */
    public function readFile(): false|string
    {
        // 確保目標是檔案
        if (!$this->isFile()) {
            throw new RuntimeException('無法開啟非檔案類型的資源');
        }

        // 檢查讀取權限
        if (!$this->isFileReadAccessed()) {
            throw new RuntimeException('沒有檔案的讀取權限');
        }
        return file_get_contents(parent::getPath());
    }

    /**
     * @param string $content
     *
     * @return false|int 函數返回寫入文件的字節數或失敗時錯誤的字節數。
     */
    public function writeFile(string $content): false|int
    {
        return file_put_contents(parent::getPath(), $content);
    }

    /**
     * @param string $content
     *
     * @return bool
     * @throws Exception
     */
    public function writeFileStream(string $content): bool
    {
        $stream = $this->readFileStream();
        $fwrite = fwrite($stream, $content);
        if ($fwrite === false) {
            throw new Exception(
                '錯誤編寫文件：' . parent::getPath() . ' - ' . json_encode(error_get_last(),
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                0x0008,
            );
        }
        return fclose($stream);
    }

    private function fileOpenExtends($mode = 'r')
    {
        return @fopen(parent::getPath(), $mode);
    }

    public function readFileStream()
    {
        // 確保目標是檔案
        if (!$this->isFile()) {
            throw new RuntimeException('無法開啟非檔案類型的資源');
        }

        // 檢查讀取權限
        if (!$this->isFileReadAccessed()) {
            throw new RuntimeException('沒有檔案的讀取權限');
        }

        $path = parent::getPath();
        $stream = $this->fileOpenExtends();

        if ($stream === false) {
            throw new RuntimeException('無法開啟檔案: ' . $path . ' - ' . json_encode(error_get_last(),
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        return $stream;

    }

    /**
     * @param string $content
     *
     * @return false|int 函數返回寫入文件的字節數或失敗時錯誤的字節數。
     */
    public function appendFile(string $content): false|int
    {
        return file_put_contents(parent::getPath(), $content, FILE_APPEND);
    }


    /**
     * @param string $content
     *
     * @return bool
     * @throws Exception
     */
    public function appendFileStream(string $content): bool
    {
        $stream = $this->fileOpenExtends('a'); // 'a' 是 append 模式，會自動定位到檔案末端
        $fwrite = fwrite($stream, $content);
        if ($fwrite === false) {
            throw new Exception(
                '錯誤編寫文件：' . parent::getPath() . ' - ' . json_encode(error_get_last(),
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                0x0008,
            );
        }
        return fclose($stream);
    }

    public function delete(): bool
    {
        return unlink(parent::getPath());
    }

    /**
     * @param string $content
     *
     * @return false|int 函數返回寫入文件的字節數或失敗時錯誤的字節數。
     */
    public function prependFile(string $content): false|int
    {
        // 先讀取現有的檔案內容
        $path = parent::getPath();
        $originalData = file_exists($path) ? file_get_contents($path) : '';

        // 將新資料加在檔案內容之前
        $newData = $content . $originalData;

        // 寫回檔案（會清空檔案）
        return file_put_contents($path, $newData);
    }

    /**
     * @param string $content
     *
     * @return false|int 函數返回寫入文件的字節數或失敗時 false。
     */
    public function prependFileStream(string $content): false|int
    {
        $path = parent::getPath();

        // 開啟檔案，若檔案不存在則建立新檔
        $fp = $this->fileOpenExtends('c+');

        if (!$fp) {
            return false;
        }

        // 鎖定檔案避免併發寫入問題
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }

        // 讀取原本內容
        $originalData = stream_get_contents($fp);

        // 移動指標到檔案開頭
        fseek($fp, 0, SEEK_SET);

        // 寫入新資料
        $bytesWritten = fwrite($fp, $content . $originalData);

        // 如果新資料比較短，需截斷檔案多餘的部分
        ftruncate($fp, strlen($content . $originalData));

        // 解鎖並關閉檔案
        flock($fp, LOCK_UN);
        fclose($fp);

        return $bytesWritten;
    }

    /**
     * @param CGBaseFileObject|CGBaseFile|string $path
     *
     * @return bool
     */
    public function copyFile(CGBaseFileObject|CGBaseFile|string $path): bool
    {
        if (is_string($path)) {
            try {
                $path = new CGBaseFile($path);
            } catch (Exception $e) {
                Log::error($e);
                return false;
            }
        }
        return copy(parent::getPath(), $path->getPath());
    }
}
