<?php

namespace App\Lib\Utils\CGFileSystem;

use Exception;
use Illuminate\Support\Facades\Log;

class CGFileSystem
{
    /**
     * @throws Exception
     */
    public static function baseFileObject(string $path, bool $queryAcl = false): CGBaseFileObject
    {
        return new CGBaseFileObject($path, false, $queryAcl);
    }

    public static function getCGFileObject(string $path, bool $queryAcl = false): CGBaseFile | CGBaseFolder | CGBaseFileObject | null
    {
        try {
            $cgBFO = new CGBaseFileObject($path, false, $queryAcl);
            if ($cgBFO->isFile()) {
                return new CGBaseFile($path, false, $queryAcl);
            } elseif ($cgBFO->isDirectory()) {
                return new CGBaseFolder($path, false, $queryAcl);
            }
            return $cgBFO;
        } catch (Exception $e) {
            Log::error($e);
        }
        return null;
    }
}
