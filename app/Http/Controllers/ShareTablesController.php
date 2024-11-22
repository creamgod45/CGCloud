<?php

namespace App\Http\Controllers;

use App\Lib\Type\Array\CGArray;
use App\Lib\Type\String\CGStringable;
use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\RouteNameField;
use App\Lib\Utils\Utilsv2;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\VirtualFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Yajra\DataTables\Facades\DataTables;

class ShareTablesController extends Controller
{

    public function index(Request $request)
    {

    }

    public function list(Request $request)
    {
        return view('Shop.ShopItemTables',
            Controller::baseControllerInit($request)->toArrayable());
    }

    public function shareTableItemListJson()
    {
        /*$inventorys = Inventory::select([
            'id',
            'name',
            'type',
            'enable',
            'price',
            'last_price',
            'amount',
            'max_amount',
            'purchases_num',
        ])->newQuery();
        $array = DataTables::eloquent($inventorys)
            ->escapeColumns()
            ->addColumn("checkbox", "rows[]")
            ->addColumn("#", "")
            ->addColumn("goto", '↗️');
        return $array->toJson();*/
    }

    public function addShopItemPost(Request $request)
    {
        $gallery = $request->get('ItemImages');
        $files = [];
        foreach ($gallery as $item) {
            if (Utilsv2::isJson($item)) {
                try {
                    $itemEl = Json::decode($item, true);
                    $itemELCG = new CGArray($itemEl);
                    if (!$itemELCG->hasKey('folder')) {
                        foreach ($itemEl as $item2) {
                            $files[] = $item2;
                        }
                    } else {
                        $itemEl['folder'] = urldecode($itemEl['folder']);
                        if (Utilsv2::isBase64($itemEl['folder'])) {
                            $itemEl['folder'] = base64_decode($itemEl['folder']);
                            $files[] = $itemEl['folder'];
                        }
                    }
                } catch (JsonException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
        return view('Shop.AddShopItem', Controller::baseControllerInit($request, ["files" => $files])->toArrayable());
    }

    public function shareTableItemPost(Request $request)
    {
        $gallery = $request->get('ItemImages');
        $files = [];
        foreach ($gallery as $item) {

            if (Utilsv2::isJson($item)) {
                try {
                    $itemEl = Json::decode($item, true);
                    $itemELCG = new CGArray($itemEl);
                    if (!$itemELCG->hasKey('folder')) {
                        foreach ($itemEl as $item2) {
                            $files[] = $item2;
                        }
                    } else {
                        $itemEl['folder'] = urldecode($itemEl['folder']);
                        if (Utilsv2::isBase64($itemEl['folder'])) {
                            $itemEl['folder'] = base64_decode($itemEl['folder']);
                            $files[] = $itemEl['folder'];
                        }
                    }
                } catch (JsonException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
        return view('ShareTable.add', Controller::baseControllerInit($request, ["files" => $files])->toArrayable());
    }

    public function shareTableItemUploadImageFetch(Request $request)
    {
        $path = $request->route("folder") . "/" . $request->route("file");
        $filename = explode('.', basename($path));
        $count = count($filename);
        if (Storage::disk('local')->exists($path) && $count > 1) {
            $resource = Storage::disk('local')->readStream($path);
            if ($resource === false) {
                return response()->json(['status' => 'error', 'message' => 'Failed to open file stream'], 500);
            }

            // 获取文件的 MIME 类型
            $mimeType = Storage::disk('local')->mimeType($path);

            // 获取文件的大小
            $fileSize = Storage::disk('local')->size($path);

            //Log::info($files[0]);
            // 返回流响应，模拟文件下载
            return response()->stream(function () use ($resource) {
                fpassthru($resource); // 输出流内容
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'attachment; filename*=UTF-8\'\'' . rawurlencode(basename($path)) . '"',
            ]);
        } else {
            $files = Storage::disk('local')->files($path);
            $resource = Storage::disk('local')->readStream($files[0]);
            if ($resource === false) {
                return response()->json(['status' => 'error', 'message' => 'Failed to open file stream'], 500);
            }

            // 获取文件的 MIME 类型
            $mimeType = Storage::disk('local')->mimeType($files[0]);

            // 获取文件的大小
            $fileSize = Storage::disk('local')->size($files[0]);

            //Log::info($files[0]);
            // 返回流响应，模拟文件下载
            return response()->stream(function () use ($resource) {
                fpassthru($resource); // 输出流内容
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'attachment; filename*=UTF-8\'\'' . rawurlencode(basename($files[0])) . '"',
            ]);
        }
    }

    public function shareTableItemUploadImageRevert(Request $request)
    {
        // 撤銷上傳請求檔案
        $raw_paths = $request->getContent();
        try {
            $decode = Json::decode($raw_paths, true);
            $success = 0;
            foreach ($decode as $item) {
                if(is_string($item)) {
                    if (Utilsv2::isBase64(urldecode($item))) {
                        $item = base64_decode(urldecode($item));
                        Storage::disk('local')->deleteDirectory($item);
                    } else {
                        Storage::disk('local')->delete($item);
                    }
                    $success++;
                }
            }
            if ($success > 0) {
                return response()->json(['success' => true, 'message' => 'File deleted successfully']);
            }
        } catch (JsonException $e) {
            Log::error($e->getMessage());
        }
        return response()->json(['failed' => true, 'message' => 'File not found'], 404);
    }

    public function shareTableItemUploadImagePost(Request $request)
    {
        // 一般檔案上傳處理(不包含分塊上傳)
        /** @var UploadedFile[] $files */
        $files = $request->allFiles();
        //Log::info(\App\Http\Controllers\ShopController::class . " " . __FUNCTION__ . " " . __LINE__ . " " . var_export($files, true));

        if (empty($files)) {
            Log::info("Create folder for upload");
            $path = 'ShareTable/TEMP/Patch/TEMP_' . Str::random(10);
            Storage::disk('local')->makeDirectory($path);
            return response()->json(["folder" => urlencode(base64_encode($path))]);
        } else {
            Log::info("Upload files");
            $file_path_array = [];
            foreach ($files as $file) {
                $random = Str::random(10);
                foreach ($file as $item) {
                    if ($item instanceof UploadedFile) {
                        $filePath = $item->storePublicly('ShareTable/TEMP/Block/'. $random, 'local');
                        $uuid = Str::uuid();
                        VirtualFile::insert([
                            'uuid' => $uuid,
                            'type' => 'temporary',
                            'filename' => $item->getClientOriginalName(),
                            'path' => $filePath,
                            'disk' => 'local',
                            'extension' => $item->getExtension(),
                            'minetypes' => $item->getMimeType(),
                            'expires_at' => now()->addMinutes(10)->timestamp
                        ]);
                        $file_path_array [] = $uuid;
                    }
                }
            }
            return response()->json($file_path_array);
        }
    }

    public function shareTableItemUploadImageHead(Request $request)
    {
        $fileInfo = $request->route('fileinfo');

        try {
            $fileInfo = json_decode($fileInfo, true);
            $fileInfo['folder'] = base64_decode(urldecode($fileInfo['folder']));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON'], 400);
        }
        Storage::disk('local')->deleteDirectory($fileInfo['folder']);
        Storage::disk('local')->makeDirectory($fileInfo['folder']);
        return response()->json(['status' => 'success']);
    }

    public function shareTableItemUploadImagePatch(Request $request)
    {
        // 处理分块上传请求
        $fileName = $request->header('Upload-Name');
        $offset = $request->header('Upload-Offset');
        $fileId = $request->header('Upload-Id');
        $fileStream = $request->getContent(true); // 获取文件流
        $fileInfo = $request->route('fileinfo');

        try {
            $fileInfo = json_decode($fileInfo, true);
            $fileInfo['folder'] = base64_decode(urldecode($fileInfo['folder']));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON'], 400);
        }

        Log::debug(new CGStringable([
            "request" => $request,
            "fileName" => $fileName,
            "offset" => $offset,
            "fileInfo" => $fileInfo,
        ]));

        // 确保上传目录存在
        $filePath = $fileInfo['folder'] . '/' . $fileId . '_' . $fileName;

        // 打开文件流用于追加内容
        $storagePath = Storage::disk('local')->path($filePath);

        // 打开文件流
        $handle = fopen($storagePath, 'c+'); // 'c+' 模式打开用于读写，如果不存在则创建

        if ($handle === false) {
            Log::error('Failed to open file stream for writing');
            return response()->json(['status' => 'error', 'message' => 'Failed to open file stream'], 500);
        }

        // 移动文件指针到指定的偏移量
        fseek($handle, $offset);

        // 写入当前块的数据
        while (!feof($fileStream)) {
            fwrite($handle, fread($fileStream, 8192)); // 逐步读取和写入 8KB 数据
        }

        // 关闭文件句柄
        fclose($handle);
        fclose($fileStream);

        // 返回响应
        return response()->json(['status' => 'success']);
    }

    public function popover(Request $request)
    {
    }

    public function search(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $vb = new ValidatorBuilder($i18N, EValidatorType::SEARCH);
        if ($request['q'] === null) {
            $request['q'] = "";
        } else {
            $request['q'] = Utilsv2::htmlSanitizer($request['q']);
        }
        if ($request['advanced_search'] === null) {
            $request['advanced_search'] = "";
        }
        $v = $vb->validate($request->all());
        if ($v instanceof MessageBag) {
            $v->add("debug_info", json_encode($request->all(), JSON_UNESCAPED_UNICODE));
            return redirect()->back()->withErrors($v)->withInput();
        } else {
            $q = $v['q'];
        }
    }
}
