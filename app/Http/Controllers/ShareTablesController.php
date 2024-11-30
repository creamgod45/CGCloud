<?php

namespace App\Http\Controllers;

use App\Lib\I18N\ELanguageText;
use App\Lib\Server\CSRF;
use App\Lib\Type\Array\CGArray;
use App\Lib\Type\String\CGString;
use App\Lib\Type\String\CGStringable;
use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\RouteNameField;
use App\Lib\Utils\Utilsv2;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\SharePermissions;
use App\Models\ShareTable;
use App\Models\VirtualFile;
use Exception;
use Hamcrest\Util;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\Response as ResponseHTTP;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;
use function Laravel\Prompts\error;

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

    public function apiPreviewFileTemporary(Request $request)
    {
        $fileUUID = $request->route('fileId');
        $virtualFile = VirtualFile::where('uuid', '=', $fileUUID)->first();
        if($virtualFile !== null) {
            $disk = Storage::disk($virtualFile->disk);
            $filename = $virtualFile->path;

            return new StreamedResponse(function () use ($disk, $filename) {
                $stream = $disk->readStream($filename);
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, 200, [
                'Content-Type' => $disk->mimeType($filename),
                'Content-Length' => $disk->size($filename),
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        }
        abort(404);
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

    public function shareTableItemCreatePost(Request $request)
    {
        $CGLCI = self::baseControllerInit($request);
        $fingerprint = $CGLCI->getFingerprint();
        $key = 'sharTableItemPost' . $fingerprint;
        if (Cache::has($key)) {
            //dump($request->all());
            $CSRF = new CSRF(RouteNameField::APIShareTableItemCreatePost->value);
            $i18N = $CGLCI->getI18N();
            $vb = new ValidatorBuilder($i18N, EValidatorType::SHARETABLECREATE);
            $v = $vb->validate($request->all(), ['password', 'password_confirmation'], true);
            if ($v instanceof MessageBag) {
                $alertView = View::make('components.alert',
                    ["type" => "%type%", "messages" => $v->all()]);
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => $CSRF->get(),
                    "message" => $alertView->render(),
                    "error_keys" => $v->keys(),
                ], ResponseHTTP::HTTP_OK);
            } else {
                $CSRF->reset();
                $password = $v['password'];
                $shareTableName = $v['shareTableName'];
                $shareTableShortCode = $v['shareTableShortCode'];
                $shareTableDescription = $v['shareTableDescription'];
                $shareTableType = $v['shareTableType'];
                $shareMembers = $v['shareMembers'];
                $files = $v['files'];
                Log::info("Create share table item");
                $shareTable =  ShareTable::createOrFirst([
                    'name' => $shareTableName,
                    'description' => $shareTableDescription,
                    'type' => $shareTableType,
                    'short_code' => $shareTableShortCode ?? Str::random(10),
                    'secret' => (!empty($password))? Hash::make($password) : null,
                    'expired_at' => now()->addDays(15)->timestamp,
                    'member_id' => Auth::user()->id,
                ]);

                foreach ($shareMembers as $shareMember) {
                    SharePermissions::create([
                        'share_tables_id' => $shareTable->id,
                        'member_id' => (int)$shareMember,
                        'permission_type' => '7',
                        'expired_at' => now()->addDays(15)->timestamp,
                    ]);
                }

                $shareTable->virtualFiles()->attach($files);

                VirtualFile::whereIn('uuid', $files)->update(['type' => 'persistent', 'expired_at' => now()->addDays(15)->timestamp]);

                return response()->json([
                    "type" => true,
                    "message" => $i18N->getLanguage(ELanguageText::ShareTableItemCreatePostSuccessMessage),
                    "redirect" => route(RouteNameField::PageHome->value),
                ]);
            }
        }
    }

    public function shareTableItemPost(Request $request)
    {
        $CGLCI = self::baseControllerInit($request);
        $fingerprint = $CGLCI->getFingerprint();
        $key = 'sharTableItemPost' . $fingerprint;
        if (Cache::has($key)) {
            $files = [];
            foreach ($request->get('ItemImages') as $item) {
                $gallery = $item;
                $string = new CGString($gallery);

                if ($string->StartWith('[') && Utilsv2::isJson($gallery)) {
                    $uuids = Json::decode($gallery, true);
                    if (!empty($uuids)) {
                        $f = VirtualFile::whereIn('uuid', $uuids)->get();
                        $files = array_merge($files, $f->all());
                    }
                } else {
                    // 處理非陣列情況
                    $virtualFile = VirtualFile::where('uuid', '=', $gallery)->first();
                    if ($virtualFile !== null) {
                        $files[] = $virtualFile;
                    }
                }
            }

            return view('ShareTable.add', Controller::baseControllerInit($request, ["files" => $files])->toArrayable());
        }
        return redirect(route(RouteNameField::PageHome->value));
    }

    public function shareTableItemUploadImageFetch(Request $request)
    {
        $fileId = $request->route("fileId");
        $virtualFile = VirtualFile::where('uuid', '=', $fileId)->first();
        if (Storage::disk('local')->exists($virtualFile->path)) {
            $resource = Storage::disk('local')->readStream($virtualFile->path);
            if ($resource === false) {
                return response()->json(['status' => 'error', 'message' => 'Failed to open file stream'], 500);
            }

            // 获取文件的 MIME 类型
            $mimeType = Storage::disk('local')->mimeType($virtualFile->path);

            // 获取文件的大小
            $fileSize = Storage::disk('local')->size($virtualFile->path);

            //Log::info($files[0]);
            // 返回流响应，模拟文件下载
            return response()->stream(function () use ($resource) {
                fpassthru($resource); // 输出流内容
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'attachment; filename*=UTF-8\'\'' . rawurlencode(basename($virtualFile->path)) . '"',
            ]);
        } else {
            $files = Storage::disk('local')->files($virtualFile->path);
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
        Log::info($raw_paths);
        $cgstring = new CGString($raw_paths);
        $is_json = $cgstring->StartWith('[');
        if ($is_json && Utilsv2::isJson($raw_paths)) {
            $uuids = Json::decode($raw_paths, true);
            $success = 0;
            foreach ($uuids as $uuid) {
                $virtualFile = VirtualFile::where('uuid', '=', $uuid)->first();
                if ($virtualFile !== null) {
                    Storage::disk($virtualFile->disk)->delete($virtualFile->path);
                    //Storage::disk($virtualFile->disk)->deleteDirectory(str_replace(basename($virtualFile->path), '', $virtualFile->path));
                    $virtualFile->delete();
                    $success++;
                } else {
                    Log::info("File revert failed: " . $uuid);
                }
            }
            if ($success > 0) {
                return response()->json(['success' => true, 'message' => 'File revert amount:' . $success]);
            }
        } else {
            $virtualFile = VirtualFile::where('uuid', '=', $raw_paths)->first();
            if ($virtualFile !== null) {
                Storage::disk($virtualFile->disk)->delete($virtualFile->path);
                //Storage::disk($virtualFile->disk)->deleteDirectory(str_replace($virtualFile->filename, '', $virtualFile->path));
                $virtualFile->delete();
                return response()->json(['success' => true, 'message' => 'File revert']);
            }
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    public function shareTableItemUploadImagePost(Request $request)
    {
        // 一般檔案上傳處理(不包含分塊上傳)
        /** @var UploadedFile[] $files */
        $files = $request->allFiles();
        //Log::info(\App\Http\Controllers\ShopController::class . " " . __FUNCTION__ . " " . __LINE__ . " " . var_export($files, true));
        $CGLCI = self::baseControllerInit($request);
        $fingerprint = $CGLCI->getFingerprint();
        $key = 'sharTableItemPost' . $fingerprint;

        if (empty($files)) {
            Log::info("Create folder for upload");
            $path = 'ShareTable/TEMP/Patch/TEMP_' . Str::random(10);
            Storage::disk('local')->makeDirectory($path);
            $uuid = Str::uuid()->toString();
            VirtualFile::insert([
                'uuid' => $uuid,
                'members_id' => Auth::user()->id ?? null,
                'type' => 'temporary',
                'filename' => 'folder',
                'path' => $path,
                'disk' => 'local',
                'extension' => 'null',
                'minetypes' => 'null',
                'expired_at' => now()->addMinutes(10)->timestamp,
            ]);
            return response($uuid, 200);
        } else {
            Log::info("Upload files");
            $file_path_array = [];
            foreach ($files as $file) {
                $random = Str::random(10);
                foreach ($file as $item) {
                    if ($item instanceof UploadedFile) {
                        $filePath = $item->storePublicly('ShareTable/TEMP/Block/' . $random, 'local');
                        $uuid = Str::uuid();
                        VirtualFile::insert([
                            'uuid' => $uuid->toString(),
                            'members_id' => Auth::user()->id ?? null,
                            'type' => 'temporary',
                            'filename' => $item->getClientOriginalName(),
                            'path' => $filePath,
                            'disk' => 'local',
                            'extension' => $item->getClientOriginalExtension(),
                            'minetypes' => $item->getMimeType(),
                            'expired_at' => now()->addMinutes(10)->timestamp,
                        ]);
                        $file_path_array [] = $uuid;
                    }
                }
            }
            if(count($file_path_array) > 0)
            {
                Cache::put($key, true, now()->addMinutes(10));
            }
            return response()->json($file_path_array);
        }
    }

    public function shareTableItemUploadImageHead(Request $request)
    {
        $fileInfo = $request->route('fileinfo');
        $virtualFile = VirtualFile::where('uuid', '=', $fileInfo)->first();
        if ($virtualFile !== null) {
            Storage::disk($virtualFile->disk)->delete($virtualFile->path);
            //Storage::disk($virtualFile->disk)->deleteDirectory(str_replace($virtualFile->filename, '', $virtualFile->path));
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'File not found'], 404);
        }
    }

    public function shareTableItemUploadImagePatch(Request $request)
    {
        // 处理分块上传请求
        $CGLCI = self::baseControllerInit($request);
        $fingerprint = $CGLCI->getFingerprint();
        $key = 'sharTableItemPost' . $fingerprint;
        $fileName = basename($request->header('Upload-Name'));
        $offset = $request->header('Upload-Offset');
        $fileId = $request->header('Upload-Id');
        $fileStream = $request->getContent(true); // 获取文件流
        $fileInfo = $request->route('fileinfo');



        $virtualFile = VirtualFile::where('uuid', '=', $fileInfo)->first();

        Log::debug(new CGStringable([
            "request" => $request,
            "fileName" => $fileName,
            "offset" => $offset,
            "fileInfo" => $fileInfo,
        ]));

        // 确保上传目录存在
        if ($offset === "0" and !str_contains($virtualFile->path, $fileName)) {
            $filePath = $virtualFile->path . '/' . $fileName;
        } else {
            $filePath = $virtualFile->path;
        }

        // 打开文件流用于追加内容
        $storagePath = Storage::disk('local')->path($filePath);

        // 打开文件流
        $handle = fopen($storagePath, 'c+'); // 'c+' 模式打开用于读写，如果不存在则创建

        if ($handle === false) {
            Log::error('Failed to open file stream for writing');
            return response()->json(['status' => 'error', 'message' => 'Failed to open file stream'], 500);
        }
        Cache::put($key, true, now()->addMinutes(10));

        // 移动文件指针到指定的偏移量
        fseek($handle, $offset);

        // 写入当前块的数据
        while (!feof($fileStream)) {
            fwrite($handle, fread($fileStream, 8192)); // 逐步读取和写入 8KB 数据
        }

        // 关闭文件句柄
        fclose($handle);
        fclose($fileStream);

        if ($offset === "0") {
            $virtualFile->update([
                'members_id' => Auth::user()->id ?? null,
                'filename' => $fileName,
                'path' => $filePath,
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                'minetypes' => Storage::disk('local')->mimeType($filePath),
                'expired_at' => now()->addMinutes(10)->timestamp,
            ]);
        }

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
