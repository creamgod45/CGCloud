<?php

namespace App\Lib\Utils\CGFileSystem;

use Exception;
use Illuminate\Support\Facades\Log;

class CGFileSystem
{
    /**
     * @throws Exception
     */
    public static function baseFileObject(string $path): CGBaseFileObject
    {
        return new CGBaseFileObject($path);
    }

    public static function getCGFileObject(string $path): CGBaseFile | CGBaseFolder | CGBaseFileObject | null
    {
        try {
            $cgBFO = new CGBaseFileObject($path);
            if ($cgBFO->isFile()) {
                return new CGBaseFile($path);
            } elseif ($cgBFO->isDirectory()) {
                return new CGBaseFolder($path);
            }
            return $cgBFO;
        } catch (Exception $e) {
            Log::error($e);
        }
        return null;
    }
}
