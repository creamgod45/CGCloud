<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class removeShopItemUploadTempFileCommand extends Command
{
    protected $signature = 'remove:shop-item-upload-temp-file';

    protected $description = '刪除暫存檔案';

    public function handle(): void
    {
        if(Storage::disk('local')->deleteDirectory('shopItemUploadTemp'))
            $this->info("成功刪除暫存檔案");
        else
            $this->info("無執行任何操作");
    }
}
