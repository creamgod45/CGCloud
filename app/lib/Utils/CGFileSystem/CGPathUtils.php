<?php

namespace App\Lib\Utils\CGFileSystem;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class CGPathUtils
{
    // Windows 最大路徑長度 (使用 Win32 API Extended-Length 路徑可達 32,767 字元)
    const WINDOWS_MAX_PATH = 260; // Windows 標準路徑限制
    const WINDOWS_EXTENDED_MAX_PATH = 32767; // Windows 擴展路徑限制 (使用 \\?\ 前綴)

    // Linux/Unix 最大路徑長度 (大多數 Linux 發行版)
    const LINUX_MAX_PATH = 4096;

    // macOS 最大路徑長度 (HFS+ 和 APFS 檔案系統)
    const MACOS_MAX_PATH = 1024; // HFS+ 標準限制
    const MACOS_EXTENDED_MAX_PATH = 4096; // APFS 和新版 macOS

    /**
     * 將反斜線轉換為正斜線
     */
    public static function converterPathSlash(string $path): string
    {
        return str_replace("\\", "/", $path);
    }

    /**
     * 判斷當前作業系統是否為 Windows
     *
     * @return bool 是否為 Windows 系統
     */
    public static function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * 判斷當前作業系統是否為 Linux
     *
     * @return bool 是否為 Linux 系統
     */
    public static function isLinux(): bool
    {
        $os = strtoupper(PHP_OS);
        return $os === 'LINUX' || $os === 'FREEBSD' || $os === 'NETBSD' || $os === 'OPENBSD';
    }

    /**
     * 判斷當前作業系統是否為 macOS
     *
     * @return bool 是否為 macOS 系統
     */
    public static function isMacOS(): bool
    {
        return strtoupper(PHP_OS) === 'DARWIN';
    }

    /**
     * 判斷當前作業系統是否為 Unix-like (包括 Linux 和 macOS)
     *
     * @return bool 是否為 Unix-like 系統
     */
    public static function isUnixLike(): bool
    {
        return self::isLinux() || self::isMacOS();
    }

    /**
     * 獲取當前作業系統的詳細資訊
     *
     * @param string $mode 可選參數，指定返回資訊的方式 (同 php_uname 的參數)
     * @return string 作業系統資訊
     */
    public static function getOSInfo(string $mode = 'a'): string
    {
        return php_uname($mode);
    }

    /**
     * 獲取當前系統的目錄分隔符
     *
     * @return string 目錄分隔符
     */
    public static function getDirectorySeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * 獲取當前系統的路徑分隔符 (用於環境變數 PATH 中分隔多個路徑)
     *
     * @return string 路徑分隔符
     */
    public static function getPathSeparator(): string
    {
        return PATH_SEPARATOR; // Windows使用 ; Linux/Mac使用 :
    }

    /**
     * 獲取當前系統的類型名稱
     *
     * @return string "windows", "linux", "macos" 或 "unknown"
     */
    public static function getOSType(): string
    {
        if (self::isWindows()) {
            return 'windows';
        } elseif (self::isLinux()) {
            return 'linux';
        } elseif (self::isMacOS()) {
            return 'macos';
        } else {
            return 'unknown';
        }
    }

    /**
     * 獲取當前系統的最大路徑長度
     *
     * @param bool $useExtended 是否使用擴展路徑長度限制
     * @return int 最大路徑長度
     */
    public static function getMaxPathLength(bool $useExtended = false): int
    {
        if (self::isWindows()) {
            return $useExtended ? self::WINDOWS_EXTENDED_MAX_PATH : self::WINDOWS_MAX_PATH;
        } elseif (self::isMacOS()) {
            return $useExtended ? self::MACOS_EXTENDED_MAX_PATH : self::MACOS_MAX_PATH;
        } else {
            return self::LINUX_MAX_PATH;
        }
    }

    /**
     * 在 Windows 平台上取得檔案的 ACL 資訊，包括擁有者和群組
     *
     * 透過 PowerShell 的 `Get-Acl` 指令，解析檔案的 ACL 資訊，並回傳包含擁有者和群組的陣列。
     * 如果運行環境非 Windows，或傳入的路徑無效，則會拋出例外。
     *
     * @param string $path 檔案的完整路徑
     *
     * @return array 包含擁有者和群組的資訊
     *
     * @throws Exception 如果執行環境不支援或路徑無效
     */
    #[ArrayShape([
        "OWNER" => "string",
        "GROUP" => "string",
    ])]
    public static function windowsGetAclInfo(string $path): array
    {
        if (self::isWindows()) {
            $path = self::converterPathSlash($path);
            try {
                if (self::isValidPath($path)) {
                    // 透過 PowerShell Get-Acl 指令取得檔案擁有者與群組資訊
                    $tpath = escapeshellarg($path);
                    $output = shell_exec("powershell -command \"Get-Acl '$tpath' | Format-List\"");
                    //echo $output;
                    if ($output && $output !== "") {
                        $outputArray = explode("\n", $output);
                        $newOutputArray = [];
                        foreach ($outputArray as $outputLine) {
                            if ($outputLine === "") {
                                continue;
                            }
                            if (str_contains($outputLine, "Owner  :") || str_contains($outputLine, "Group  :")) {
                                $explode = explode(": ", $outputLine);
                                $newOutputArray[trim(strtoupper($explode[0]))] = $explode[1];
                            }
                        }
                        //var_dump($newOutputArray);
                        return $newOutputArray;
                    }
                }
            } catch (Exception $e) {
                return ["OWNER" => "not valid path", "GROUP" => "not valid path"];
            }
        }
        throw new Exception("not support", 0x0007);
    }

    /**
     * 檢查路徑是否有效
     *
     * @param string $path        要檢查的路徑
     * @param bool   $checkExists 是否檢查路徑實際存在
     * @param bool   $checkLength 是否檢查路徑長度限制
     *
     * @return bool 路徑是否有效
     * @throws Exception
     */
    public static function isValidPath(string $path, bool $checkExists = true, bool $checkLength = true): bool
    {
        // 檢查路徑是否為空
        if (empty(trim($path))) {
            throw new Exception("empty path", 0x0001);
        }

        // 標準化路徑格式
        $path = self::converterPathSlash($path);

        // 檢查路徑是否包含無效字元 (系統特定)
        if (self::isWindows()) {
            // Windows 特別禁止的字元: < > : " | ? *
            // 但要排除路徑中合法使用的冒號，如 C:/Users
            $pathWithoutDrive = preg_replace('/^[A-Za-z]:\//', '', $path);

            // 檢查剩餘路徑部分
            if (preg_match('/[<>:"|\?\*]/', $pathWithoutDrive)) {
                throw new Exception("[windows] path contains invalid characters: $path", 0x0002);
            }
        } elseif (self::isMacOS()) {
            // macOS 禁止的字元: : 和 /
            if (preg_match('/[\:](?!\/)/', $path)) { // 冒號不能作為檔名的一部分，但可以在路徑中如 /Volumes/Drive:/
                throw new Exception("[mac] path contains invalid characters: $path", 0x0003);
            }
        }

        // 檢查所有系統通用的無效字元 (包括控制字元)
        if (preg_match('/[\x00-\x1F\x7F]/', $path)) {
            throw new Exception("invalid characters: $path", 0x0004);
        }

        // 檢查路徑長度
        if ($checkLength) {
            $pathLength = strlen($path);
            $maxLength = self::getMaxPathLength();

            if (self::isWindows()) {
                // Windows 路徑長度檢查
                // 如果是擴展路徑格式 (\\?\)
                if (strpos($path, '\\\\?\\') === 0 || strpos($path, '//?/') === 0) {
                    $maxLength = self::WINDOWS_EXTENDED_MAX_PATH;
                }
            }

            if ($pathLength >= $maxLength) {
                throw new Exception("path length exceeds maximum length: $path", 0x0005);
            }
        }

        // 檢查路徑是否存在
        if ($checkExists && !file_exists($path)) {
            throw new Exception("path not exists: $path", 0x0006);
        }

        return true;
    }

    /**
     * 處理 macOS 上的資源分支路徑 (Resource Fork)
     * macOS 允許檔案有資源分支，通過 /path/to/file/..namedfork/rsrc 訪問
     *
     * @param string $path 原始檔案路徑
     * @param bool $createIfNotExists 如果資源分支不存在是否創建
     * @return string 資源分支路徑
     */
    public static function getMacResourceForkPath(string $path, bool $createIfNotExists = false): string
    {
        if (!self::isMacOS()) {
            return '';
        }

        $resourcePath = $path . '/..namedfork/rsrc';

        if ($createIfNotExists && !file_exists($resourcePath)) {
            @file_put_contents($resourcePath, '');
        }

        return file_exists($resourcePath) ? $resourcePath : '';
    }

    /**
     * 檢測路徑是否包含 macOS 特有的隱藏檔案或目錄
     * (以 "." 開頭或具有隱藏標記)
     *
     * @param string $path 要檢查的路徑
     * @return bool 是否為 macOS 隱藏檔案或目錄
     */
    public static function isMacHiddenPath(string $path): bool
    {
        // 檢查是否以 "." 開頭 (Unix-like 系統的隱藏檔案)
        $basename = basename($path);
        if (substr($basename, 0, 1) === '.') {
            return true;
        }

        // 檢查 macOS 特殊系統檔案
        $macSpecialFiles = [
            '.DS_Store',
            '.Spotlight-V100',
            '.Trashes',
            '.fseventsd',
            '.TemporaryItems'
        ];

        if (in_array($basename, $macSpecialFiles)) {
            return true;
        }

        // 在 macOS 上檢查隱藏標記 (需要 shell_exec 權限)
        if (self::isMacOS() && function_exists('shell_exec')) {
            $result = shell_exec('ls -lO ' . escapeshellarg($path) . ' 2>/dev/null');
            return (strpos($result, 'hidden') !== false);
        }

        return false;
    }

    /**
     * 轉換路徑為擴展格式 (Windows) 或處理特殊格式 (macOS)
     *
     * @param string $path 要轉換的路徑
     * @return string 轉換後的路徑
     */
    public static function toExtendedPath(string $path): string
    {
        if (self::isWindows()) {
            // 判斷路徑是否已經是擴展格式
            if (strpos($path, '\\\\?\\') === 0 || strpos($path, '//?/') === 0) {
                return $path;
            }

            // 轉換為絕對路徑
            $absPath = realpath($path);

            if ($absPath === false) {
                return '';
            }

            // 添加擴展路徑前綴
            return '\\\\?\\' . $absPath;
        } elseif (self::isMacOS()) {
            // macOS 處理，確保路徑正規化
            $path = self::converterPathSlash($path);

            // 處理 macOS 的特殊路徑如 /Volumes/
            if (strpos($path, '/Volumes/') !== 0 && $path !== '/' && strpos($path, '/') === 0) {
                $realPath = realpath($path);
                return $realPath !== false ? $realPath : $path;
            }
        }

        return $path;
    }

    /**
     * 分析路徑並提供詳細信息
     *
     * @param string $path 要分析的路徑
     * @return array 路徑相關信息
     */
    public static function analyzePath(string $path): array
    {
        $normalizedPath = self::converterPathSlash($path);
        $pathInfo = pathinfo($normalizedPath);

        $info = [
            'original_path' => $path,
            'normalized_path' => $normalizedPath,
            'dirname' => $pathInfo['dirname'] ?? '',
            'basename' => $pathInfo['basename'] ?? '',
            'extension' => $pathInfo['extension'] ?? '',
            'filename' => $pathInfo['filename'] ?? '',
            'path_length' => strlen($path),
            'is_absolute' => self::isAbsolutePath($normalizedPath),
            'max_path_length' => self::getMaxPathLength(),
            'is_valid_length' => strlen($path) < self::getMaxPathLength(),
            'exists' => file_exists($path),
            'is_windows' => self::isWindows(),
            'is_linux' => self::isLinux(),
            'is_macos' => self::isMacOS(),
            'directory_separator' => self::getDirectorySeparator(),
            'os_type' => self::getOSType()
        ];

        // macOS 特定資訊
        if (self::isMacOS() && file_exists($path)) {
            $info['is_mac_hidden'] = self::isMacHiddenPath($path);
            $info['has_resource_fork'] = file_exists($path . '/..namedfork/rsrc');

            // 取得 macOS 檔案類型和創建者代碼 (如果可用)
            if (function_exists('shell_exec')) {
                $fileInfo = shell_exec('mdls -name kMDItemKind ' . escapeshellarg($path) . ' 2>/dev/null');
                if ($fileInfo) {
                    $info['mac_file_kind'] = trim(str_replace('kMDItemKind = ', '', $fileInfo));
                }
            }
        }

        return $info;
    }

    /**
     * 檢查路徑是否為絕對路徑
     *
     * @param string $path 要檢查的路徑
     * @return bool 是否為絕對路徑
     */
    public static function isAbsolutePath(string $path): bool
    {
        // 統一處理成正斜線
        $path = self::converterPathSlash($path);

        if (self::isWindows()) {
            // 檢查 Windows 絕對路徑 (如 C:/ 或 \\server\share)
            return preg_match('/^[A-Za-z]:\/|^\/\/[^\/]+\/[^\/]+/', $path) === 1;
        } else {
            // Unix/Linux/macOS 絕對路徑 (以 / 開頭)
            return strpos($path, '/') === 0;
        }
    }

    /**
     * 獲取系統特定的臨時目錄路徑
     *
     * @return string 臨時目錄路徑
     */
    public static function getTempDirectoryPath(): string
    {
        return sys_get_temp_dir();
    }

    /**
     * 檢查路徑是否包含系統特定的保留名稱
     *
     * @param string $path 要檢查的路徑
     * @return bool 是否包含保留名稱
     */
    public static function containsReservedName(string $path): bool
    {
        $basename = strtoupper(basename(self::converterPathSlash($path)));

        // Windows 保留檔名
        if (self::isWindows()) {
            $reservedNames = [
                'CON', 'PRN', 'AUX', 'NUL',
                'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
                'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'
            ];

            // 檢查完整名稱或檔案名稱是否為保留名稱
            return in_array($basename, $reservedNames) ||
                preg_match('/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(\..+)?$/', $basename);
        } elseif (self::isMacOS()) {
            // macOS 特有的保留檔名
            $reservedNames = [
                '.DS_STORE',
                '.SPOTLIGHT-V100',
                '.TRASHES',
                '.FSEVENTSD',
                '.APDISK'
            ];

            return in_array($basename, $reservedNames);
        }

        return false;
    }
}
