<?php

namespace App\Lib\Utils\CGFileSystem;

enum ECGBaseFileType
{
    case symlink;
    case hardlink;
    case file;
    case folder;
    case unknown;
    case blockDevice;
    case charDevice;
    case fifo;
    case socket;
}
