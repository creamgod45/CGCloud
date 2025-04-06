<?php

namespace App\Lib\Utils\CGFileSystem;

use Exception;

class CGBaseFileObject
{
    /**
     * @var ECGBaseFileType 檔案類型
     */
    private ECGBaseFileType $type;
    /**
     * @var string 完整路徑
     */
    private string $path;

    /**
     * @var string 完整路徑(不包含檔案名稱)
     */
    private string $dirname;

    /**
     * @var ?string 檔案名稱
     */
    private ?string $filenameAndExtension;
    /**
     * @var ?string 檔案名稱(不含附檔名)
     */
    private ?string $filename;
    /**
     * @var ?string 附檔名
     */
    private ?string $extension;
    /**
     * @var ?string MIME type
     */
    private ?string $mimeType;

    private string $fileSize;

    private string $fileModified;

    private string $fileCreated;

    private string $fileAccessedTime;

    private bool $fileReadAccessed;

    private bool $fileWriteAccessed;

    private string $fileOwner;

    private string $fileGroup;

    private string $filePermissions;

    /**
     * @param string $path
     * @param bool   $defaultFileExists
     *
     * @throws Exception
     */
    public function __construct(string $path, bool $defaultFileExists = false){

        try {
            if (!CGPathUtils::isValidPath($path, $defaultFileExists)) {
                throw new Exception("Invalid Path");
            }
        } catch (Exception $e) {
            dump($e);
        }

        $this->path = CGPathUtils::converterPathSlash($path);

        $this->type = match (true) {
            is_link($this->path) => ECGBaseFileType::symlink,
            is_dir($this->path) => ECGBaseFileType::folder,
            is_file($this->path) => ECGBaseFileType::file,
            @filetype($this->path) === 'block' => ECGBaseFileType::blockDevice,
            @filetype($this->path) === 'char' => ECGBaseFileType::charDevice,
            @filetype($this->path) === 'fifo' => ECGBaseFileType::fifo,
            @filetype($this->path) === 'socket' => ECGBaseFileType::socket,
            default => ECGBaseFileType::unknown,
        };

        if($this->type === ECGBaseFileType::file){
            $this->filename = pathinfo($this->path, PATHINFO_FILENAME);
            $this->extension = pathinfo($this->path, PATHINFO_EXTENSION);
            $this->filenameAndExtension = pathinfo($this->path, PATHINFO_BASENAME);
            $this->mimeType = mime_content_type($this->path);
        } else {
            $this->filename = null;
            $this->extension = null;
            $this->filenameAndExtension = null;
            $this->mimeType = null;
        }

        $this->dirname = pathinfo($this->path, PATHINFO_DIRNAME);
        $stat = @stat($this->path);
        if(is_array($stat)){
            $this->fileSize = $stat['size'];
            $this->fileModified = $stat['mtime'];
            $this->fileCreated = $stat['ctime'];
            $this->fileAccessedTime = $stat['atime'];
            if(!CGPathUtils::isWindows()){
                $this->fileOwner = $stat['uid'];
                $this->fileGroup = $stat['gid'];
            }
            $this->filePermissions = $stat['mode'];
        }
        $this->fileReadAccessed = is_readable($this->path);
        $this->fileWriteAccessed = is_writeable($this->path);
        if(CGPathUtils::isWindows()){
            $windowsGetAclInfo = CGPathUtils::windowsGetAclInfo($this->path, $defaultFileExists);
            $this->fileOwner = $windowsGetAclInfo['OWNER'];
            $this->fileGroup = $windowsGetAclInfo['GROUP'];
        }
    }

    public function __toString(){
        return json_encode([
            'type' => $this->type,
            'path' => $this->path,
            'dirname' => $this->dirname,
            'filenameAndExtension' => $this->filenameAndExtension,
            'filename' => $this->filename,
            'extension' => $this->extension,
            'mimeType' => $this->mimeType,
            'fileSize' => $this->fileSize,
            'fileModified' => $this->fileModified,
            'fileCreated' => $this->fileCreated,
            'fileAccessedTime' => $this->fileAccessedTime,
            'fileReadAccessed' => $this->fileReadAccessed,
            'fileWriteAccessed' => $this->fileWriteAccessed,
            'fileOwner' => $this->fileOwner,
            'fileGroup' => $this->fileGroup,
            'filePermissions' => $this->filePermissions,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?? "can't converter json encode";
    }

    public function isFile(){
        return $this->type === ECGBaseFileType::file;
    }

    public function isDirectory(){
        return $this->type === ECGBaseFileType::folder;
    }

    public function isSymlink(){
        return $this->type === ECGBaseFileType::symlink;
    }

    public function isBlockDevice(){
        return $this->type === ECGBaseFileType::blockDevice;
    }

    public function isCharDevice(){
        return $this->type === ECGBaseFileType::charDevice;
    }

    public function isFifo(){
        return $this->type === ECGBaseFileType::fifo;
    }

    public function isSocket(){
        return $this->type === ECGBaseFileType::socket;
    }

    public function isUnknown(){
        return $this->type === ECGBaseFileType::unknown;
    }

    /**
     * @return ECGBaseFileType
     */
    public function getType(): ECGBaseFileType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function getWinPath(): string
    {
        return str_replace("/", "\\", $this->path);
    }

    /**
     * @return string
     */
    public function getDirname(): string
    {
        return $this->dirname;
    }

    /**
     * @return string|null
     */
    public function getFilenameAndExtension(): ?string
    {
        return $this->filenameAndExtension;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     *
     * @return CGBaseFileObject
     */
    public function setFilename(?string $filename): CGBaseFileObject
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getFileSize(): string
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getFileModified(): string
    {
        return $this->fileModified;
    }

    /**
     * @return string
     */
    public function getFileCreated(): string
    {
        return $this->fileCreated;
    }

    /**
     * @return string
     */
    public function getFileAccessedTime(): string
    {
        return $this->fileAccessedTime;
    }

    /**
     * @return bool
     */
    public function isFileReadAccessed(): bool
    {
        return $this->fileReadAccessed;
    }

    /**
     * @return bool
     */
    public function isFileWriteAccessed(): bool
    {
        return $this->fileWriteAccessed;
    }

    /**
     * @return string
     */
    public function getFileOwner(): string
    {
        return $this->fileOwner;
    }

    /**
     * @return string
     */
    public function getFileGroup(): string
    {
        return $this->fileGroup;
    }

    /**
     * @return string
     */
    public function getFilePermissions(): string
    {
        return $this->filePermissions;
    }
}
