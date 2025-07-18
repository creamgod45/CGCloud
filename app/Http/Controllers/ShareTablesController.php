<?php

namespace App\Http\Controllers;

use App\Lib\EShareTableType;
use App\Lib\I18N\ELanguageText;
use App\Lib\Server\CSRF;
use App\Lib\Type\Array\CGArray;
use App\Lib\Type\String\CGString;
use App\Lib\Type\String\CGStringable;
use App\Lib\Utils\CGFileSystem\CGBaseFile;
use App\Lib\Utils\CGFileSystem\CGBaseFolder;
use App\Lib\Utils\CGFileSystem\CGFileSystem;
use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\RouteNameField;
use App\Lib\Utils\Utils;
use App\Lib\Utils\Utilsv2;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\DashVideos;
use App\Models\SharePermissions;
use App\Models\ShareTable;
use App\Models\ShareTableVirtualFile;
use App\Models\VirtualFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseHTTP;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShareTablesController extends Controller
{

    public function index(Request $request)
    {

    }

    public function publicShareableShareTableItem(Request $request)
    {
        $popup = $request->get('popup', false) === '1';
        $id = $request->route('shortcode', 0);
        /** @var ShareTable $shareTable */
        $shareTable = ShareTable::where('short_code', '=', $id)->where('type', '=',
            EShareTableType::public->value)->get()->first();
        if ($shareTable !== null) {
            $shareTableId = $shareTable->short_code;
            $virtualFiles = $shareTable->getAllVirtualFiles();
            /** @var ShareTableVirtualFile[] | Collection $shareTableVirtualFiles */
            $shareTableVirtualFiles = $shareTable->shareTableVirtualFile()->getResults();
            foreach ($virtualFiles as $virtualFile) {
                $virtualFile['#'] = '';
                $downloadUrl = route(RouteNameField::PagePublicShareTableDownloadItem->value,
                    ['fileId' => $virtualFile->uuid, 'shortcode' => $shareTableId]);
                $playerItem = "";
                /** @var ShareTableVirtualFile $shareTableVirtualFile */
                $shareTableVirtualFile = $shareTableVirtualFiles->firstWhere('virtual_file_uuid', '=',
                    $virtualFile->uuid);
                if ($shareTableVirtualFile !== null) {
                    if ($shareTableVirtualFile->isAvailableDashVideo() && $shareTableVirtualFile->isCreateDashVideo()) {
                        /** @var DashVideos $results */
                        $results = $shareTableVirtualFile->dashVideos()->getResults();
                        $playerItemUrl = route(RouteNameField::PagePublicShareTablePreviewFilePlayerDash->value, [
                            'shareTableId' => $shareTable->id,
                            'fileId' => $virtualFile->uuid,
                            'fileName' => $results->filename . "." . $results->extension,
                        ]);
                        $playerItem = '<a target="_blank" rel="noreferrer noopener" href="' . $playerItemUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;Dash 線上預覽</a>';
                    }
                }
                if ($virtualFile->size <= 1024 * 1024 * 400) {
                    $previewUrl = URL::temporarySignedRoute(RouteNameField::PagePublicShareTablePreviewItem->value,
                        now()->addMinutes(5), ['fileId' => $virtualFile->uuid, 'shortcode' => $shareTableId]);
                    $preview = '<a target="_blank" rel="noreferrer noopener" href="' . $previewUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;預覽</a>';
                } else {
                    $preview = '<a class="btn-md btn-border-0 btn btn-dead tippyer" data-placement="auto" data-content="檔案過大無法預覽文件"><i class="fa-solid fa-eye"></i>&nbsp;預覽</a>';
                }
                $virtualFile['action'] = '<div class="flex gap-3">' . $playerItem . $preview . '<a href="' . $downloadUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-color7"><i class="fa-solid fa-download"></i>&nbsp;下載</a></div>';
                $virtualFile['size'] = Utils::convertByte($virtualFile['size']);
            }
            $sharePermissions = SharePermissions::where('share_tables_id', '=', $shareTableId)->get();
            return view('ShareTable.view', Controller::baseControllerInit($request, [
                "shareTable" => $shareTable,
                "virtualFiles" => $virtualFiles,
                'sharePermissions' => $sharePermissions,
                'popup' => $popup,
                'type' => "public",
            ])->toArrayable());
        } else {
            abort(404);
        }
    }

    public function publicShareableShareTablePreviewItem(Request $request)
    {
        $CGLCI = self::baseControllerInit($request);
        $i18N = $CGLCI->getI18N();
        $rawfileUUID = $request->route('fileId', 0);
        $shareTableId = $request->route('shortcode', 0);
        $vb = new ValidatorBuilder($i18N, EValidatorType::PublicShareablePreviewItem);
        $v = $vb->validate(['fileId' => $rawfileUUID, 'shortcode' => $shareTableId]);
        if ($v instanceof MessageBag) {
            abort(400);
        } else {
            $cgstring = new CGString($v['fileId']);
            $array = $cgstring->Split('.');
            if (count($array->toArray()) === 2) {
                $fileUUID = $array->Get("0");
                $extension = $array->Get("1");
            } else {
                $fileUUID = $v['fileId'];
            }
            if ($shareTableId !== null) {
                $shareTable = ShareTable::where('short_code', '=', $shareTableId)->where('type', '=',
                    EShareTableType::public->value)->get()->first();
                if ($shareTable !== null) {
                    $collection = $shareTable->getAllVirtualFiles();
                    if ($collection->contains('uuid', '=', $fileUUID)) {
                        $virtualFile = $collection->expectKeys('uuid', $fileUUID)->first();
                        if ($virtualFile !== null && $virtualFile->size <= 1024 * 1024 * 400) {
                            return $this->filePreview($virtualFile);
                        } else {
                            abort(400, "File size is too large. Can not preview server side.");
                        }
                    }
                }
            }
        }
        abort(404);
    }

    /**
     * Streams the contents of a virtual file to the response.
     *
     * If the virtual file is not null, it attempts to read the file from the specified disk
     * and streams its contents directly to the output. The streamed response includes headers
     * indicating the content type, content length, and content disposition to facilitate inline
     * display of the file in a browser.
     *
     * In case the virtual file is null, the function will abort the request with a 404 error.
     *
     * @param mixed $virtualFile An object containing the disk and path of the file to be streamed.
     *
     * @return StreamedResponse The HTTP streamed response with the file content.
     *
     * @throws HttpException If the file is not found or cannot be read, a 404 error is returned.
     */
    private function filePreview(VirtualFile $virtualFile): StreamedResponse
    {
        if ($virtualFile !== null) {
            $disk = Storage::disk($virtualFile->disk);
            $filename = $virtualFile->path;

            $stream = $disk->readStream($filename);
            return new StreamedResponse(function () use ($stream) {
                stream_copy_to_stream($stream, fopen('php://output', 'wb'));
                fclose($stream);
            }, 200, [
                'Content-Type' => $disk->mimeType($filename),
                'Content-Length' => $disk->size($filename),
                'Content-Disposition' => 'inline; filename="' . $virtualFile->filename . '"',
            ]);
        }
        abort(404);
    }

    public function publicShareableShareTableDownloadItem(Request $request)
    {
        set_time_limit(60 * 60 * 24);
        // TODO XSS RCE TYPE CHECKER
        $shareTableId = $request->route('shortcode', 0);
        $fileId = $request->route('fileId', 0);

        $shareTable = ShareTable::where('short_code', '=', $shareTableId)->where('type', '=',
            EShareTableType::public->value)->get()->first();
        if ($shareTable !== null) {
            $allRelatedIds = $shareTable->getAllVirtualFiles();
            if ($allRelatedIds->contains('uuid', '=', $fileId)) {
                $virtualFile = $allRelatedIds->expectKeys('uuid', $fileId)->first();

                if ($virtualFile !== null) {
                    $disk = Storage::disk($virtualFile->disk);
                    $path = $virtualFile->path;

                    if ($disk->exists($path)) {
                        $stream = $disk->readStream($path);
                        // 每次传输的字节数
                        $rateLimit = 1024 * config('app.downloadFileRateKB'); // 每秒最大传输速率
                        return new StreamedResponse(function () use ($stream, $rateLimit) {
                            $chunkSize = 1024 * 2048;
                            $delay = $chunkSize / $rateLimit; // 每次传输后的延迟时间
                            while (!feof($stream)) {
                                echo fread($stream, $chunkSize);
                                flush(); // 確保輸出的數據立即傳輸到客戶端
                                // 限制传输速率，通过延迟来实现
                                usleep($delay * 1e6); // 将秒数转换为微秒
                            }
                            fclose($stream);
                        }, 200, [
                            'Content-Type' => $disk->mimeType($path),
                            'Content-Length' => $disk->size($path),
                            'Content-Disposition' => 'attachment; filename="' . $virtualFile->filename . '"',
                        ]);
                    } else {
                        abort(404, 'File not in physics storage.');
                    }
                } else {
                    abort(404, 'Virtual file not found.');
                }
            }
            abort(404, 'File not found.');
        }
        abort(404, 'Sharetable not found.');
    }

    public function shareableShareTableItemPost(Request $request)
    {
        $id = $request->route('id', 0);

    }

    public function viewShareTableItem(Request $request)
    {
        // TODO XSS RCE TYPE CHECKER
        $popup = $request->get('popup', false) === '1';
        $shareTableId = $request->route('id', 0);
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null) {
            $virtualFiles = $shareTable->getAllVirtualFiles();
            foreach ($virtualFiles as $virtualFile) {
                $virtualFile['#'] = '';
                $downloadUrl = route(RouteNameField::PageShareTableItemDownload->value,
                    ['fileId' => $virtualFile->uuid, 'id' => $shareTableId]);
                $deleteUrl = route(RouteNameField::PageShareTableItemDelete->value,
                    ['fileId' => $virtualFile->uuid, 'id' => $shareTableId]);

                $preview = '';
                $results = $virtualFile->dashVideos()->getResults();
                if($results instanceof DashVideos){
                    $dashVideo = $results;
                    if ($dashVideo->isSuccess() && $dashVideo->isCreateDashVideo()) {
                        $playerItemUrl = route(RouteNameField::PagePreviewFilePlayerDash->value, [
                            'shareTableId' => $shareTable->id,
                            'fileId' => $virtualFile->uuid,
                            'fileName' => $dashVideo->filename . "." . $dashVideo->extension,
                        ]);
                        $preview .= '<a target="_blank" rel="noreferrer noopener" href="' . $playerItemUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;Dash 線上預覽</a>';
                    }
                }

                if ($virtualFile->size <= 1024 * 1024 * 400) {
                    $preview .= '<a target="_blank" rel="noreferrer noopener" href="' . $virtualFile->getTemporaryUrl(now()->addMinutes(30),
                            $shareTableId) . '" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;預覽</a>';
                } else {
                    $preview .= '<a class="btn-md btn-border-0 btn btn-dead tippyer" data-placement="auto" data-content="檔案過大無法預覽文件"><i class="fa-solid fa-eye"></i>&nbsp;預覽</a>';
                }
                $virtualFile['action'] = '<div class="flex gap-3">' . $preview . '<a href="' . $downloadUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-color7"><i class="fa-solid fa-download"></i>&nbsp;下載</a><a data-fn="popover_shareable_delete_file" data-type="error" data-parent="#popover_index" data-title="是否確認刪除此檔案?" data-confirmboxcontent="此操作將會永遠的刪除!!" data-href="' . $deleteUrl . '" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-trash"></i>&nbsp;刪除</a></div>';
                $virtualFile['size'] = Utils::convertByte($virtualFile['size']);
            }
            $sharePermissions = SharePermissions::where('share_tables_id', '=', $shareTableId)->get();
            return view('ShareTable.view', Controller::baseControllerInit($request, [
                "shareTable" => $shareTable,
                "virtualFiles" => $virtualFiles,
                'sharePermissions' => $sharePermissions,
                'popup' => $popup,
            ])->toArrayable());
        } else {
            abort(404);
        }
    }

    public function deleteShareTable(Request $request)
    {
        $shareTableId = $request->route('id', 0);
        $shareTable = ShareTable::find($shareTableId);
        $member = Auth::user();
        if ($member !== null) {
            if ($shareTable !== null) {
                if ($member->id === $shareTable->member_id) {
                    /** @var ShareTableVirtualFile[] | Collection $results */
                    $results = $shareTable->shareTableVirtualFile()->getResults();
                    foreach ($results as $result) {
                        /** @var DashVideos $dashVideos */
                        $dashVideos = $result->dashVideos()->getResults();
                        if ($dashVideos === null) {
                            continue;
                        }
                        $dashVideoFilePath = Storage::disk($dashVideos->disk)->path($dashVideos->path ?? "");
                        $object = CGFileSystem::getCGFileObject($dashVideoFilePath);
                        if ($object instanceof CGBaseFile) {
                            $folderObj = CGFileSystem::getCGFileObject($object->getDirname());
                            if ($folderObj instanceof CGBaseFolder) {
                                $folderObj->delete();
                            }
                        }
                    }
                    $shareTable->getAllVirtualFiles()->each(function ($virtualFile) {
                        Storage::disk($virtualFile->disk)->delete($virtualFile->path);
                        $virtualFile->delete();
                    });
                    $shareTable->delete();
                    $this->clearShareTableIndexCaches();
                    return view('ShareTable.delete', Controller::baseControllerInit($request, [
                        '$href' => back()->getTargetUrl(),
                        '$content' => '分享資源 ' . e($shareTable->name) . ' 刪除成功 5秒後自動跳轉(如果沒有請點我跳轉)',
                    ])->toArrayable());
                } else {
                    return view('ShareTable.delete', Controller::baseControllerInit($request, [
                        '$href' => back()->getTargetUrl(),
                        '$content' => '分享資源刪除失敗 無授權權限 5秒後自動跳轉(如果沒有請點我跳轉)',
                    ])->toArrayable());
                }
            } else {
                return view('ShareTable.delete', Controller::baseControllerInit($request, [
                    '$href' => back()->getTargetUrl(),
                    '$content' => '分享資源刪除失敗 無此相關紀錄 5秒後自動跳轉(如果沒有請點我跳轉)',
                ])->toArrayable());
            }
        }
        abort(403);
    }

    private function clearShareTableIndexCaches()
    {
        if (Cache::has('shareTableIndexCaches')) {
            $var = Cache::get('shareTableIndexCaches');
            if (is_array($var)) {
                foreach ($var as $value) {
                    //$pageKey = 'shareTableIndexCache_p_' . $value;
                    //Cache::forget($pageKey);
                    Log::debug("CLEAR CACHE KEY ".$value);
                    Cache::forget($value);
                }
                Cache::put('shareTableIndexCaches', []);
            }
        }
    }

    public function successShareTable(Request $request)
    {
        $popup = $request->get('popup', 0);
        return view('ShareTable.success', Controller::baseControllerInit($request, [
            '$content' => '編輯成功(將會自動關閉)',
            '$popup' => $popup,
        ])->toArrayable());
    }

    public function editShareTablePost(Request $request)
    {
        $CGLCI = self::baseControllerInit($request);
        $shareTableId = $request->route('id', 0);
        $CSRF = new CSRF(RouteNameField::APIShareTableItemCreatePost->value);

        $i18N = $CGLCI->getI18N();
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null && $shareTable->member_id === Auth::user()->id) {
            $vb = new ValidatorBuilder($i18N, EValidatorType::SHARETABLEEDIT);
            $data = $request->all();
            if(is_array($data["files"])) {
                $tFiles = [];
                foreach ($data["files"] as $file) {
                    if($file !== null) {
                        if(Utilsv2::isJson($file)){
                            try {
                                $tFiles = array_merge($tFiles, Json::decode($file, true));
                            } catch (JsonException $e) {

                            }
                        } else {
                            $tFiles[] = $file;
                        }
                    }
                }
                $data["files"] = $tFiles;
            }
            $v = $vb->validate($data, ['current_password', 'password', 'password_confirmation'], true);
            if ($v instanceof MessageBag) {
                $alertView = View::make('components.alert', ["type" => "danger", "messages" => $v->all(), "customClass" => "mb-3"]);
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => $CSRF->get(),
                    "message" => $alertView->render(),
                    "error_keys" => $v->keys(),
                ], ResponseHTTP::HTTP_OK);
            } else {
                $CSRF->release();
                // 關聯資料庫 files UUID
                $files = $shareTable->getAllVirtualFiles();
                $tFiles = VirtualFile::whereIn('uuid', $v['files'])->get()->collect();

                // 傳入的檔案 UUID array
                $newFiles = $tFiles->diff($files)->all(); // 新增的檔案 UUID
                $detectFiles = $files->diff($tFiles)->makeVisible(['uuid'])->all(); // 需要移除的檔案 UUID
                //$originalFiles = $files->intersect($tFiles)->all(); // 原本的檔案 UUID // 原本的檔案 uuid



//                dump([
//                    "deattchFiles: " => $detectFiles,
//                    "newFiles: " => $newFiles,
//                    "originalFiles: " => $originalFiles,
//                ]);
                $tShareMembers = [];
                //DB::enableQueryLog();
                $members = $shareTable->relationMember();
                //Log::debug(var_export(DB::getQueryLog(), true));
                foreach ($v["shareMembers"] as $memberId) {
                    if($memberId !== $shareTable->member_id && !$members->contains('member_id', '=', $memberId)){
                        $tShareMembers[] = [
                            'share_table_id' => $shareTable->id,
                            'member_id' => $memberId,
                            'permission_type' => '7',
                            'expired_at' => now()->addDays(15)->timestamp,
                        ];
                    };
                }

                $shareTable->shareTablePermission()->createMany($tShareMembers);

                $shareTable->update([
                    'name' => $v['shareTableName'],
                    'description' => $v['shareTableDescription'],
                    'type' => $v['shareTableType'],
                    'secret' => (!empty($v['password'])) ? Hash::make($v['password']) : null,
                ]);

                $tShareTableVF = [];
                foreach ($newFiles as $file) {
                    $tShareTableVF[] = [
                        'share_table_id' => $shareTable->id,
                        'virtual_file_uuid' => $file['uuid'],
                    ];
                    $file->type = "persistent";
                    $file->expired_at = -1;
                    $file->save();
                }

                $shareTable->shareTableVirtualFile()->createMany($tShareTableVF);

                $shareTable->shareTableVirtualFile()->whereIn('virtual_file_uuid', $detectFiles)->delete();

                $files->diff($tFiles)->map(function ($file) {
                    $file->type = "temporary";
                    $file->save();
                });

                $this->clearShareTableIndexCaches();

                return response()->json([
                    "type" => true,
                    "message" => "編輯分享資源成功",
                    "redirect" => route(RouteNameField::PageShareTableItemSuccess->value, ['popup' => 1]),
                ]);
            }
        }
    }

    public function editShareTablePost2(Request $request)
    {
        $CGLCI = self::baseControllerInit($request);
        $shareTableId = $request->route('id', 0);
        $CSRF = new CSRF(RouteNameField::APIShareTableItemCreatePost->value);

        $i18N = $CGLCI->getI18N();
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null && $shareTable->member_id === Auth::user()->id) {
            $vb = new ValidatorBuilder($i18N, EValidatorType::SHARETABLEEDIT);
            $v = $vb->validate($request->all(), ['current_password', 'password', 'password_confirmation'], true);
            if ($v instanceof MessageBag) {
                $alertView = View::make('components.alert', ["type" => "%type%", "messages" => $v->all(), "customClass" => "mt-3"]);
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => $CSRF->get(),
                    "message" => $alertView->render(),
                    "error_keys" => $v->keys(),
                ], ResponseHTTP::HTTP_OK);
            } else {
                $CSRF->release();
                // 關聯資料庫 files UUID
                $files = $shareTable->getAllVirtualFiles();
                $tFiles = VirtualFile::whereIn('uuid', $v['files'])->get()->collect();

                // 傳入的檔案 UUID array
                $newFiles = $tFiles->diff($files)->all(); // 新增的檔案 UUID
                $deattchFiles = $files->diff($tFiles)->all(); // 需要移除的檔案 UUID
                $originalFiles = $files->intersect($tFiles)->all(); // 原本的檔案 UUID // 原本的檔案 uuid

                dd([
                    "deattchFiles: " => $deattchFiles,
                    "newFiles: " => $newFiles,
                    "originalFiles: " => $originalFiles,
                ]);

                $shareTable->update([
                    'name' => $v['shareTableName'],
                    'description' => $v['shareTableDescription'],
                    'type' => $v['shareTableType'],
                    'secret' => (!empty($v['password'])) ? Hash::make($v['password']) : null,
                ]);

                $this->clearShareTableIndexCaches();

                return response()->json([
                    "type" => true,
                    "message" => "編輯分享資源成功",
                    "redirect" => route(RouteNameField::PageShareTableItemSuccess->value, ['popup' => 1]),
                ]);
            }
        }
    }

    public function editShareTable(Request $request)
    {
        $shareTableId = $request->route('id', 0);
        $popup = $request->get('popup', 0);
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null && $shareTable->member_id === Auth::user()->id) {
            $virtualFiles = $shareTable->getAllVirtualFiles();
            $relatedPermissionIds = $shareTable->shareTablePermission()->getResults();
            $members = [];
            /**
             * @var SharePermissions[] | Collection<SharePermissions> $relatedPermissionIds
             */
            foreach ($relatedPermissionIds as $item) {
                $results = $item->member()->getResults();
                $members[] = $results;
            }
            return view('ShareTable.add', Controller::baseControllerInit($request, [
                "files" => $virtualFiles,
                "popup" => $popup,
                "value" => [
                    "shareTableId" => $shareTable->id,
                    "shareTableType" => $shareTable->type,
                    "shareTableName" => $shareTable->name,
                    "shareTableDescription" => $shareTable->description,
                    "shareMembers" => $members,
                ],
            ])->toArrayable());
        } else {
            abort(404);
        }
    }

    public function deleteShareTableItem(Request $request)
    {
        // TODO XSS RCE TYPE CHECKER
        $shareTableId = $request->route('id', 0);
        $fileId = $request->route('fileId', 0);
        $shareTable = ShareTable::find($shareTableId);
        $member = Auth::user();
        if ($member !== null) {
            if ($shareTable !== null) {
                if ($member->id === $shareTable->member_id) {
                    $allRelatedIds = $shareTable->getAllVirtualFiles();
                    if ($allRelatedIds->contains('uuid', '=', $fileId)) {
                        $virtualFile = $allRelatedIds->except([$fileId])->first();

                        if ($virtualFile !== null) {
                            $disk = Storage::disk($virtualFile->disk);
                            $path = $virtualFile->path;

                            if ($disk->exists($path)) {
                                $disk->delete($path);
                                $shareTableVirtualFiles = $shareTable->shareTableVirtualFile()->getResults();
                                dump($shareTableVirtualFiles);

                                $filename = $virtualFile->filename;
                                $virtualFile->delete();
                                return view('ShareTable.delete', Controller::baseControllerInit($request, [
                                    '$href' => back()->getTargetUrl(),
                                    '$content' => '檔案 ' . e($filename) . ' 刪除成功 5秒後自動跳轉(如果沒有請點我跳轉)',
                                ])->toArrayable());
                            } else {
                                abort(404, 'File not in physics storage.');
                            }
                        } else {
                            abort(404, 'Virtual file not found.');
                        }
                    }
                    abort(404, 'File not found.');
                }
                abort(403, 'No permission to delete this file.');
            }
        }
        abort(404, 'Sharetable not found.');
    }

    public function getDashProgress(Request $request)
    {
        $id = $request->get('id', 0);
        $dashVideos = DashVideos::where('virtual_file_uuid', '=', $id)->get()->first();
        if ($dashVideos !== null && !$dashVideos->isCreateDashVideo()) {
            $waterMarkProgress = Cache::get('ffmpeg_watermark_progress_' . $dashVideos->id);
            $streamProgress = Cache::get('ffmpeg_streaming_progress_' . $dashVideos->id);
            if ($streamProgress !== null && (int)$streamProgress <= 100) {
                Cache::delete('ffmpeg_streaming_progress_' . $dashVideos->id);
                return response()->json([
                    'message' => 'success2',
                    'value' => $streamProgress,
                ]);
            } elseif ($waterMarkProgress !== null && $waterMarkProgress !== "") {
                Cache::put('pending_process_' . $dashVideos->id, true, now()->addMinutes(1));
                Cache::delete('ffmpeg_watermark_progress_' . $dashVideos->id);
                return response()->json([
                    'message' => 'success',
                    'value' => $waterMarkProgress,
                ]);
            }
        }
        if ($dashVideos !== null && $dashVideos->isCreateDashVideo()) {
            return response()->json([
                'message' => 'stop',
                'value' => "已處理完成",
            ]);
        }
        if ($dashVideos !== null && $dashVideos->isProcessing()) {
            return response()->json([
                'message' => 'processing',
                'value' => "處理中",
            ]);
        }
        if ($dashVideos !== null && $dashVideos->isWait()) {
            return response()->json([
                'message' => 'wait',
                'value' => "列隊中...",
            ]);
        }
        if ($dashVideos !== null && $dashVideos->isFail()) {
            return response()->json([
                'message' => 'failed',
                'value' => "轉檔失敗(請重新點選轉換按鈕，如果一直失敗可能考慮回報此問題)",
            ]);
        }
        return response()->json([
            'message' => 'not work',
            'value' => "沒有執行",
        ]);
    }

    public function conversionShareTableItem(Request $request)
    {
        $shareTableId = $request->route('id', 0);
        $fileId = $request->route('fileId', 0);
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null) {
            /** @var VirtualFile[] | Collection<VirtualFile> $results */
            $results = $shareTable->getAllVirtualFiles();
            if ($results->contains('uuid', '=', $fileId)) {
                $virtualFile = $results->expectKeys('uuid', $fileId)->first();
                /** @var ShareTableVirtualFile $shareTableVirtualFiles */
                $shareTableVirtualFiles = $virtualFile->shareTables()->getResults();
                if ($shareTableVirtualFiles !== null && !$shareTableVirtualFiles->isCreateDashVideo()) {
                    $dashVideos = DashVideos::createOrFirst([
                        'type' => 'wait',
                        'member_id' => Auth::user()->id,
                        'virtual_file_uuid' => $virtualFile->uuid,
                        'share_table_virtual_file_id' => $shareTableVirtualFiles->id,
                    ]);
                    Cache::put('pending_process_' . $dashVideos->id, true, now()->addDay());
                    $shareTableVirtualFiles->update([
                        'dash_videos_id' => $dashVideos->id,
                    ]);
                    return response()->json([
                        'message' => "請求處理中...",
                    ]);
                } elseif ($shareTableVirtualFiles !== null && $shareTableVirtualFiles->isCreateDashVideo()) {
                    $dashVideos = $shareTableVirtualFiles->dashVideos()->getResults();
                    if ($dashVideos !== null && $dashVideos->type === 'failed') {
                        $dashVideos->update([
                            'type' => 'wait',
                        ]);
                        return response()->json([
                            'message' => "請求處理中...",
                        ]);
                    }
                }
            }
            abort(404, 'File not found.');
        }
        abort(403, 'Sharetable not found.');
    }

    public function dashPreviewFile(Request $request)
    {
        $shareTableId = $request->route('shareTableId', 0);
        $fileId = $request->route('fileId', 0);
        $fileName = $request->route('fileName', "");

        $shareTable = ShareTable::find($shareTableId);
        $virtualFiles = $shareTable->getAllVirtualFiles();
        if ($shareTable !== null && $virtualFiles->contains('uuid', '=', $fileId)) {
            //Log::info('0');
            /** @var ShareTableVirtualFile[] $shareTableVirtualFile */
            $shareTableVirtualFile = $shareTable->shareTableVirtualFile()->getResults();
            if ($shareTableVirtualFile !== null) {
                //Log::info('1');
                foreach ($shareTableVirtualFile as $item) {
                    //Log::info('2');
                    /** @var DashVideos $dashVideos */
                    $dashVideos = $item->dashVideos()->getResults();
                    //Log::info(json_encode($dashVideos->toArray(), JSON_UNESCAPED_UNICODE));
                    $disk = Storage::disk($dashVideos->disk);
                    $path = str_replace($dashVideos->filename . "." . $dashVideos->extension, '', $dashVideos->path);
                    $files = [];
                    if (file_exists($disk->path($path) . "/allFiles.json")) {
                        $CGBaseFolder = CGFileSystem::getCGFileObject($disk->path($path));
                        if ($CGBaseFolder instanceof CGBaseFolder) {
                            $json = FileSystem::read($disk->path($path) . "/allFiles.json");
                            $files = json_decode($json, true);
                        }
                    } else {
                        $files = $disk->allFiles($path);
                        $json1 = json_encode($files, JSON_UNESCAPED_UNICODE);
                        FileSystem::write($disk->path($path) . "/allFiles.json", $json1);
                    }
                    //Log::info("path: ".$path);
                    //Log::info(json_encode($files, JSON_UNESCAPED_UNICODE));
                    foreach ($files as $file) {
                        //Log::info('3');
                        if (str_contains($file, $fileName)) {
                            //Log::info('4');
                            $stream = $disk->readStream($file);
                            return new StreamedResponse(function () use ($fileName, $disk, $file, $stream) {
                                stream_copy_to_stream($stream, fopen('php://output', 'wb'));
                                fclose($stream);
                            }, 200, [
                                'Content-Type' => $disk->mimeType($file),
                                'Content-Length' => $disk->size($file),
                                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                            ]);
                        }
                    }
                    abort(404, 'File not found.');
                }
            }
        }
    }

    public function publicShareableShareTableDashPreviewFile(Request $request)
    {
        $shareTableId = $request->route('shareTableId', 0);
        $fileId = $request->route('fileId', 0);
        $fileName = $request->route('fileName', "");

        $shareTable = ShareTable::find($shareTableId);
        $virtualFiles = $shareTable->getAllVirtualFiles();
        if ($shareTable !== null && $virtualFiles->contains('uuid', '=', $fileId)) {
            /** @var ShareTableVirtualFile[] $shareTableVirtualFile */
            $shareTableVirtualFile = $shareTable->shareTableVirtualFile()->getResults();
            if ($shareTableVirtualFile !== null) {
                foreach ($shareTableVirtualFile as $item) {
                    /** @var DashVideos $dashVideos */
                    $dashVideos = $item->dashVideos()->getResults();
                    $disk = Storage::disk($dashVideos->disk);
                    $path = str_replace($dashVideos->filename . "." . $dashVideos->extension, '', $dashVideos->path);
                    $files = [];
                    if (file_exists($disk->path($path) . "/allFiles.json")) {
                        $CGBaseFolder = CGFileSystem::getCGFileObject($disk->path($path));
                        if ($CGBaseFolder instanceof CGBaseFolder) {
                            $json = FileSystem::read($disk->path($path) . "/allFiles.json");
                            $files = json_decode($json, true);
                        }
                    } else {
                        $files = $disk->allFiles($path);
                        $json1 = json_encode($files, JSON_UNESCAPED_UNICODE);
                        FileSystem::write($disk->path($path) . "/allFiles.json", $json1);
                    }
                    foreach ($files as $file) {
                        if (str_contains($file, $fileName)) {
                            $stream = $disk->readStream($file);
                            return new StreamedResponse(function () use ($fileName, $disk, $file, $stream) {
                                stream_copy_to_stream($stream, fopen('php://output', 'wb'));
                                fclose($stream);
                            }, 200, [
                                'Content-Type' => $disk->mimeType($file),
                                'Content-Length' => $disk->size($file),
                                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                            ]);
                        }
                    }
                    abort(404, 'File not found.');
                }
            }
        }
    }

    public function playerPreviewFile(Request $request)
    {
        $shareTableId = $request->route('shareTableId', 0);
        $fileId = $request->route('fileId', 0);
        $fileName = $request->route('fileName', "");
        return view('ShareTable.player', Controller::baseControllerInit($request, [
            'url' => route(RouteNameField::APIPreviewFileDash->value,
                ['shareTableId' => $shareTableId, 'fileId' => $fileId, 'fileName' => $fileName]),
        ])->toArrayable());
    }

    public function publicShareableShareTablePlayerPreviewFile(Request $request)
    {
        $shareTableId = $request->route('shareTableId', 0);
        $fileId = $request->route('fileId', 0);
        $fileName = $request->route('fileName', "");
        return view('ShareTable.player', Controller::baseControllerInit($request, [
            'url' => route(RouteNameField::APIPublicShareTablePreviewFileDash->value,
                ['shareTableId' => $shareTableId, 'fileId' => $fileId, 'fileName' => $fileName]),
        ])->toArrayable());
    }

    public function downloadShareTableItem(Request $request)
    {
        set_time_limit(60 * 60 * 24);
        // TODO XSS RCE TYPE CHECKER
        $shareTableId = $request->route('id', 0);
        $fileId = $request->route('fileId', 0);

        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null) {
            $allRelatedIds = $shareTable->getAllVirtualFiles();
            if ($allRelatedIds->contains('uuid', '=', $fileId)) {
                $virtualFile = $allRelatedIds->expectKeys('uuid', $fileId)->first();

                if ($virtualFile !== null) {
                    $disk = Storage::disk($virtualFile->disk);
                    $path = $virtualFile->path;

                    if ($disk->exists($path)) {
                        $stream = $disk->readStream($path);
                        // 每次传输的字节数
                        $rateLimit = 1024 * config('app.downloadFileRateKB'); // 每秒最大传输速率
                        return new StreamedResponse(function () use ($stream, $rateLimit) {
                            $chunkSize = 1024 * 2048;
                            $delay = $chunkSize / $rateLimit; // 每次传输后的延迟时间
                            while (!feof($stream)) {
                                echo fread($stream, $chunkSize);
                                flush(); // 確保輸出的數據立即傳輸到客戶端
                                // 限制传输速率，通过延迟来实现
                                usleep($delay * 1e6); // 将秒数转换为微秒
                            }
                            fclose($stream);
                        }, 200, [
                            'Content-Type' => $disk->mimeType($path),
                            'Content-Length' => $disk->size($path),
                            'Content-Disposition' => 'attachment; filename="' . $virtualFile->filename . '"',
                        ]);
                    } else {
                        abort(404, 'File not in physics storage.');
                    }
                } else {
                    abort(404, 'Virtual file not found.');
                }
            }
            abort(404, 'File not found.');
        }
        abort(404, 'Sharetable not found.');
    }

    public function list(Request $request)
    {
        return view('Shop.ShopItemTables', Controller::baseControllerInit($request)->toArrayable());
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
        $fileUUID = $request->route('fileId', 0);
        $CGLCI = self::baseControllerInit($request);
        $fingerprint = $CGLCI->getFingerprint();
        $key = 'sharTableItemPost' . $fingerprint;
        if (Cache::has($key)) {
            $virtualFile = VirtualFile::where('uuid', '=', $fileUUID)->first();
            if ($virtualFile !== null && $virtualFile->size <= 1024 * 1024 * 400) {
                return $this->filePreview($virtualFile);
            } else {
                abort(400, "File size is too large. Can not preview server side.");
            }
        } else {
            abort(404);
        }
    }

    /**
     * @throws JsonException
     */
    public function apiPreviewFileTemporary3(Request $request)
    {
        $fileUUID = $request->get('fileId', 0);
        $shareTableId = $request->get('shareTableId', 0);
        if ($shareTableId !== null) {
            $shareTable = ShareTable::find($shareTableId);
            if ($shareTable !== null && is_array($fileUUID)) {
                $collection = $shareTable->getAllVirtualFiles();
                $toArray = $collection->toArray();
                if (sort($fileUUID) === sort($toArray)) {
                    $virtualFile = VirtualFile::whereIn('uuid', $fileUUID)->get();
                    $urls = [];
                    foreach ($virtualFile as $item) {
                        $urls[] = $item->getTemporaryUrl(now()->addDays(), $shareTable->id);
                    }
                    return response()->json($urls);
                }
            }
        }
        abort(403);
    }

    public function apiPreviewFileTemporary2(Request $request)
    {
        $fileUUID = $request->route('fileId', 0);
        $shareTableId = $request->route('shareTableId', 0);
        $shareTable = ShareTable::find($shareTableId);
        if ($shareTable !== null) {
            $collection = $shareTable->getAllVirtualFiles();
            if ($collection->contains('uuid', '=', $fileUUID)) {
                $virtualFile = $collection->expectKeys('uuid', $fileUUID)->first();
                if ($virtualFile !== null && $virtualFile->size <= 1024 * 1024 * 400) {
                    return $this->filePreview($virtualFile);
                } else {
                    abort(400, "File size is too large. Can not preview server side.");
                }
            }
        }
        abort(404);
    }

    public function apiPreviewFileTemporary4(Request $request)
    {
        $fileUUID = $request->route('fileId', 0);
        $vf = VirtualFile::whereUuid($fileUUID)->first();
        if ($vf !== null && $vf->size <= 1024 * 1024 * 400 && str_contains($vf->filename, "_output_thumb001_thumb.jpg")) {
            if (Storage::disk($vf->disk)->exists($vf->path)) {
                return $this->filePreview($vf);
            }
        }
        abort(404);
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
                $alertView = View::make('components.alert', ["type" => "error", "messages" => $v->all(), "customClass" => "mb-3"]);
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
                $shareTable = ShareTable::createOrFirst([
                    'name' => $shareTableName,
                    'description' => $shareTableDescription,
                    'type' => $shareTableType,
                    'short_code' => $shareTableShortCode ?? Str::random(10),
                    'secret' => (!empty($password)) ? Hash::make($password) : null,
                    'expired_at' => now()->addDays(15)->timestamp,
                    'member_id' => Auth::user()->id,
                ]);

                foreach ($shareMembers as $shareMember) {
                    if($shareMember !== Auth::user()?->id){
                        SharePermissions::create([
                            'share_tables_id' => $shareTable->id,
                            'member_id' => (int)$shareMember,
                            'permission_type' => '7',
                            'expired_at' => now()->addDays(15)->timestamp,
                        ]);
                    }
                }

                foreach ($files as $file) {
                    $shareTable->shareTableVirtualFile()->insert([
                        'share_table_id' => $shareTable->id,
                        'virtual_file_uuid' => $file,
                    ]);
                }

                $virtualFiles = VirtualFile::whereIn('uuid', $files)->get();
                foreach ($virtualFiles as $virtualFile) {
                    $virtualFile->update([
                        'size' => Storage::disk($virtualFile->disk)->size($virtualFile->path),
                        'type' => 'persistent',
                        'expired_at' => now()->addDays(15)->timestamp,
                    ]);
                }
                $this->clearShareTableIndexCaches();

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
                if (!is_string($item)) {
                    continue;
                }
                $gallery = $item;
                $string = new CGString($gallery);

                if ($string->StartWith('[') && Utilsv2::isJson($gallery)) {
                    $uuids = Json::decode($gallery, true);
                    if (!empty($uuids)) {
                        $f = VirtualFile::whereIn('uuid', $uuids)->get();
                        foreach ($f->all() as $i) {
                            if ($i->size === 0) {
                                $i->size = Storage::disk($i->disk)->size($i->path);
                                $i->save();
                            }
                        }
                        $files = array_merge($files, $f->all());
                    }
                } else {
                    // 處理非陣列情況
                    $virtualFile = VirtualFile::where('uuid', '=', $gallery)->first();
                    if ($virtualFile !== null) {
                        if ($virtualFile->size === 0) {
                            $virtualFile->size = Storage::disk($virtualFile->disk)->size($virtualFile->path);
                            $virtualFile->save();
                        }
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
        $fileId = $request->route("fileId", 0);
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
            VirtualFile::create([
                'uuid' => $uuid,
                'members_id' => Auth::user()->id ?? null,
                'type' => 'temporary',
                'filename' => 'folder',
                'path' => $path,
                'disk' => 'local',
                'extension' => 'null',
                'minetypes' => 'null',
                'size' => '0',
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
                        $filePath = $item->storeAs('ShareTable/TEMP/Block/' , $random, 'local');
                        $uuid = Str::uuid();
                        $mimeType = $item->getMimeType();
                        $object = CGFileSystem::getCGFileObject($filePath);
                        if($object instanceof CGBaseFile) {
                            try {
                                $mimeType = $object->mimeLikely();
                            } catch (Exception $e) {

                            }
                        }
                        $v = VirtualFile::create([
                            'uuid' => $uuid->toString(),
                            'members_id' => Auth::user()->id ?? null,
                            'type' => 'temporary',
                            'filename' => $item->getClientOriginalName(),
                            'path' => $filePath,
                            'disk' => 'local',
                            'extension' => $item->getClientOriginalExtension(),
                            'minetypes' => $mimeType,
                            'size' => $item->getSize(),
                            'expired_at' => now()->addMinutes(10)->timestamp,
                        ]);
                        $file_path_array [] = $uuid;
                    }
                }
            }
            if (count($file_path_array) > 0) {
                Cache::put($key, true, now()->addMinutes(10));
            }
            return response()->json($file_path_array);
        }
    }

    public function shareTableItemUploadImageHead(Request $request)
    {
        $fileInfo = $request->route('fileinfo', 0);
        $virtualFile = VirtualFile::where('uuid', '=', $fileInfo)->first();
        if ($virtualFile !== null && $virtualFile->type === 'temporary') {
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
        $fileInfo = $request->route('fileinfo', 0);


        $virtualFile = VirtualFile::where('uuid', '=', $fileInfo)->first();

        Log::debug(new CGStringable([
            "request" => $request,
            "fileName" => $fileName,
            "offset" => $offset,
            "fileInfo" => $fileInfo,
        ]));

        // 确保上传目录存在
        if ($offset === "0" and !str_contains($virtualFile->path, md5($fileName))) {
            $filePath = $virtualFile->path . '/' . md5($fileName);
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

        $stat = stat($storagePath);
        // 設定最大檔案大小為 50MB
        if (($stat['size'] + $offset) > 1024 * 1024 * 1024) {

        }

        // 关闭文件句柄
        fclose($handle);
        fclose($fileStream);

        if ($offset === "0") {
            $CGBaseFolder = CGFileSystem::getCGFileObject($storagePath);
            $mime_candidates = "";
            if ($CGBaseFolder instanceof CGBaseFile) {
                try {
                    $mime_candidates = $CGBaseFolder->mimeLikely();
                    Log::info($mime_candidates);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            $virtualFile->update([
                'members_id' => Auth::user()->id ?? null,
                'filename' => $fileName,
                'path' => $filePath,
                'extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                'minetypes' => $mime_candidates,
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

    public function myShareTables(Request $request)
    {
        $user = Auth::user();
        if($user !== null) {
            $user->id;
            // 記錄所有快取後的分頁 ( 頁數為 1)
            $pageKey = 'shareTableIndexCache_p_' . $request->get('page', 1)."_user_".$user->id;
        } else {
            $pageKey = 'shareTableIndexCache_p_' . $request->get('page', 1);
        }
        DB::enableQueryLog();
        $shareTables = Cache::remember($pageKey, now()->addMinutes(15), function () use ($pageKey, $user) {
            // 紀錄所有快取後的分頁
            $key = 'shareTableIndexCaches';
            if (Cache::has($key)) {
                $var = Cache::get($key);
                if (is_array($var) && !in_array($pageKey, $var)) {
                    $var [] = $pageKey;
                }
                Cache::put($key, $var, now()->addDays());
            } else {
                Cache::put($key, [$pageKey], now()->addDays());
            }
            DB::enableQueryLog();
            if ($user !== null) {
                // 使用子查詢或聯表方式一次獲取所有需要的數據
                $shareTable = ShareTable::select('share_tables.*')
                    ->where(function($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                                $q->where('share_tables.type', '=', EShareTableType::private->value)
                                    ->where('share_tables.member_id', '=', $user->id);
                            });
                    })
                    ->orWhereExists(function ($query) use ($user) {
                        $query->select(DB::raw(1))
                            ->from('share_permissions')
                            ->whereColumn('share_permissions.share_tables_id', 'share_tables.id')
                            ->where('share_permissions.member_id', '=', $user->id)
                            ->where('share_tables.type', '=', EShareTableType::private->value);
                    })
                    ->orderBy('share_tables.created_at', 'desc')
                    ->paginate(30);
            } else {
                $shareTable = ShareTable::where('type', '=', EShareTableType::public->value)
                    ->orderBy('created_at', 'desc')
                    ->paginate(30);
            }
            $orderBy = $shareTable;
            if ($user !== null) {
                // 使用 with 預加載關聯數據
                $sharePermissions = SharePermissions::where('member_id', '=', $user->id)
                    ->with('shareTable')
                    ->get();

                $privateShareTables = $sharePermissions
                    ->map(function ($permission) {
                        return $permission->shareTable;
                    })
                    ->filter(function ($shareTable) use ($orderBy) {
                        return $shareTable &&
                            !$orderBy->contains($shareTable) &&
                            $shareTable->type === EShareTableType::private->value;
                    });

                // 一次性添加所有私有表
                $orderBy->push(...$privateShareTables);

                // 排序
                $orderBy->setCollection($orderBy->sortBy([
                    ['created_at', 'desc'],
                    ['id', 'desc'],
                ]));
            }

            $shareTables = $orderBy;
            Log::info(json_encode(DB::getQueryLog(), JSON_PRETTY_PRINT));
            return $shareTables;
        });
        return view('ShareTable.my', Controller::baseControllerInit($request, [ '$shareTables' => $shareTables])->toArrayable());
    }

    private function filePreviewLimitSpeed(?VirtualFile $virtualFile): StreamedResponse
    {
        set_time_limit(60 * 60 * 24);
        if ($virtualFile !== null) {
            $disk = Storage::disk($virtualFile->disk);
            $filename = $virtualFile->path;

            $stream = $disk->readStream($filename);
            return new StreamedResponse(function () use ($stream, $disk, $filename, $virtualFile) {
                $chunkSize = 1024 * 2048; // 每次传输的字节数
                $rateLimit = 1024 * 1512; // 每秒最大传输速率
                $delay = $chunkSize / $rateLimit; // 每次传输后的延迟时间

                // 打开 php://output 流
                $output = fopen('php://output', 'wb');

                if (!$output || !$stream) {
                    throw new RuntimeException('Failed to open streams for reading or writing.');
                }

                while (!feof($stream)) {
                    // 从输入流读取一块数据
                    $buffer = fread($stream, $chunkSize);

                    // 写入到输出流
                    fwrite($output, $buffer);

                    // 清空 PHP 输出缓冲区，确保及时发送数据
                    flush();
                    ob_flush();

                    // 限制传输速率，通过延迟来实现
                    usleep($delay * 1e6); // 将秒数转换为微秒
                }

                // 关闭流
                fclose($stream);
                fclose($output);
            }, 200, [
                'Content-Type' => $disk->mimeType($filename),
                'Content-Length' => $disk->size($filename),
                'Content-Disposition' => 'inline; filename="' . $virtualFile->filename . '"',
            ]);
        }
        abort(404);
    }
}
