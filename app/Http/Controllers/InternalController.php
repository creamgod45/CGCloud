<?php

namespace App\Http\Controllers;

use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\ELanguageText;
use App\Lib\Type\String\CGStringable;
use App\Lib\Utils\ClientConfig;
use App\Lib\Utils\EncryptedCache;
use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\Utils;
use App\Lib\Utils\Utilsv2;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\CustomizePage;
use App\Models\Inventory;
use App\Models\InventoryType;
use App\Models\ShopConfig;
use App\Models\SystemLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Intervention\Image\Geometry\Factories\BezierFactory;
use Intervention\Image\Geometry\Factories\EllipseFactory;
use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Geometry\Factories\PolygonFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;
use Symfony\Component\HttpFoundation\Response as ResponseHTTP;
use Yajra\DataTables\Facades\DataTables;

class InternalController extends Controller
{
    public function SystemSettings(Request $request)
    {
        $shopConfigs = ShopConfig::all();
        $inventoryTypes = InventoryType::all();
        return view('System.SystemSettings', Controller::baseControllerInit($request,
            ['ShopConfig' => $shopConfigs, 'inventoryTypes' => $inventoryTypes])->toArrayable());
    }

    public function SystemSettingsUploadFile(Request $request)
    {
        $CGLCI = self::baseControllerInit($request, []);
        /** @var UploadedFile[] $files */
        // 從請求中獲取所有上傳檔案
        $files = $request->allFiles();
        Log::info("POST /SystemSettingsUploadFile payloads:" . var_export($files, true));

        $vb = new ValidatorBuilder($CGLCI->getI18N(), EValidatorType::SYSTEMSETTINGUPLOAD);
        $messageBag = $vb->validate($request->all());
        if($messageBag instanceof MessageBag) {
            Log::info("ShopImage, ShopAdPopup, ShopAdItem files do not exist or are invalid");
            return response()->json([
                "status" => false,
                "message" => $messageBag->all()
            ], ResponseHTTP::HTTP_BAD_REQUEST);
        }else{
            Log::info("ShopImage, ShopAdPopup, ShopAdItem files exist and are valid");
            foreach (['ShopImage', 'ShopAdPopup', 'ShopAdItem'] as $key) {
                if (isset($files[$key])) {
                    Log::info("$key files exist and are valid");
                    // 如果存在 'ShopImage', 'ShopAdPopup', 或 'ShopAdItem' 檔案，則執行此區塊內的代碼
                    if (empty($files)) {
                        Log::info("Create folder for upload");
                        $path = 'SystemSettingUpload/'.$key.'/SSU_' . Str::random(10);
                        Storage::disk('local')->makeDirectory($path);
                        return response()->json(["folder" => urlencode(base64_encode($path))]);
                    } else {
                        Log::info("Upload files");
                        $file_path_array = [];
                        foreach ($files as $fileGroup) {
                            $random = Str::random(10);
                            foreach ($fileGroup as $item) {
                                if ($item instanceof UploadedFile) {
                                    $file_path_array[] = $item->storePublicly('SystemSettingUpload/'.$key.'/SSU_' . $random,
                                        'local');
                                }
                            }
                        }
                        return response()->json($file_path_array);
                    }
                }
            }
        }
        return response()->json([
            "status" => false,
            "message" => "Deny Operation"
        ], ResponseHTTP::HTTP_BAD_REQUEST);
    }

    public function CustomPages(Request $request)
    {
        $customizePage = CustomizePage::paginate(15);
        $popup = $request->route('popup');
        return view('System.CustomPages', Controller::baseControllerInit($request,
            ['customizePage' => $customizePage, 'popup' => $popup])->toArrayable());
    }

    public function CustomPage(Request $request)
    {
        if ($request->route('id', false)) {
            $customizePage = CustomizePage::find($request->route('id'));
            $popup = $request->get('popup', false) === '1';
            return view('System.CustomPage', Controller::baseControllerInit($request,
                ['customizePage' => $customizePage, 'popup' => $popup])->toArrayable());
        }

        return redirect()->back();
    }

    public function getSystemLogs(Request $request)
    {
        $logs = SystemLog::query();
        $order = $request->get('order');
        if (is_array($order)) {
            $dir = $order[0]['dir'];
            $name = $order[0]['name'];
            if ($name === "id") {
                if ($dir === "asc") {
                    $dir = "desc";
                } else {
                    $dir = "asc";
                }
                $logs->orderBy($name, $dir);
            }
        }
        $array = DataTables::eloquent($logs)->escapeColumns()->addColumn("checkbox", "rows[]")->addColumn("#", "");
        return $array->toJson();
    }

    public function getClientID(Request $request)
    {
        $ref = $request->get("ref", false);
        return view('getClientID', Controller::baseControllerInit($request, [
            'ref' => $ref,
        ])->toArrayable());
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    /*private function ShopListLoader(Request $request): array
    {
        $str = 'shop.item.list.';
        $key = $str . 'page_' . $request->get('shopitemlist', 1);
        $inventoryPaginate = Cache::remember($key, 60, function () use ($request) {
            $inventories = Inventory::paginate(24, [
                'id',
                'image_url',
                'name',
                'brand',
                'tags',
                'price',
                'last_price',
                'GTIN-13',
                'quality',
                'SKU',
                'status',
            ], 'shopitemlist', $request->get('shopitemlist', 1));
            if ($inventories->isNotEmpty()) {
                Log::info($inventories->toQuery()->toSql());
            }
            return $inventories;
        });
        $prices = Cache::remember($str . 'prices', 60, function () use ($inventoryPaginate) {
            DB::enableQueryLog();
            $priceSelect = Inventory::selectRaw('MAX(price) as max_price, MIN(price) as min_price')->first(['price']);
            Log::info(var_export(DB::getQueryLog(), true));
            return $priceSelect;
        });
        $maxPrice = $prices->max_price;
        $minPrice = $prices->min_price;
        return array($maxPrice, $minPrice, $inventoryPaginate);
    }*/

    public function getClientIDPost(Request $request)
    {
        $cgLCI = self::baseControllerInit($request, []);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::GETCLIENTID);
        $v = $vb->validate($request->all(), ['ID'], true);
        if ($v instanceof MessageBag && !Session::has("ClientID")) {
            return response()->json(['message' => 'failed']);
        } else {
            if (!Session::has('ClientID')) {
                Session::put("ClientID", sha1($v['ID']));
                EncryptedCache::put(Session::get("ClientID") . "_ClientConfig",
                    new ClientConfig(ELanguageCode::zh_TW->name), now()->addDays());
            }
            return response()->json(['message' => 'ok']);
        }
    }

    public function user(Request $request)
    {
        $filter = $request['filter'];
        Log::info("POST /user payloads:" . new CGStringable($filter));
        $user = $request->user();
        $catcher = [];
        if (empty($user)) {
            return response()->json(["message" => "UNAUTHORIZED"], ResponseHTTP::HTTP_UNAUTHORIZED);
        }
        foreach ($filter as $value) {
            if ($value === "password" || $value === "remember_token") {
                continue;
            }
            $catcher [$value] = $user[$value];
        }
        return $catcher;
    }

    /** @noinspection PhpUnused */

    public function browser(Request $request)
    {
        $key = self::fingerprint($request->session()->get('ClientID'));
        return response()->json(['id' => $key]);
    }

    /** @noinspection PhpUnused */

    public function language(Request $request)
    {
        $cgLCI = self::baseControllerInit($request, []);
        $i18N = $cgLCI->getI18N();
        $vb = new ValidatorBuilder($i18N, EValidatorType::Language);
        $v = $vb->validate($request->all());
        if ($v instanceof MessageBag) {
            return response()->json(['message' => 'Error'], ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            $config = EncryptedCache::get(Session::get("ClientID") . "_ClientConfig");
            if ($config instanceof ClientConfig) {
                $language = $config->getLanguage();
                if (empty($request->all())) {
                    return response()->json([
                        'message' => $i18N->getLanguage(ELanguageText::GetLanguage),
                        'lang' => $language,
                    ]);
                } elseif (ELanguageCode::isVaild($request['lang'])) {
                    $config->setLanguage($request['lang']);
                    $config->setLanguageClass(ELanguageCode::valueof($request['lang']));
                    EncryptedCache::put(Session::get("ClientID") . "_ClientConfig", $config, now()->addDays());
                    return response()->json([
                        'message' => $i18N->getLanguage(ELanguageText::DataReceivedSuccessfully),
                        'lang' => $request['lang'],
                    ]);
                }
            }
            return response()->json(['message' => 'Error1'], ResponseHTTP::HTTP_BAD_REQUEST);
        }
    }

    /** @noinspection PhpUnused */

    /**
     * @throws Exception
     */
    public function encodeJson(Request $request)
    {
        $cgLCI = self::baseControllerInit($request, []);
        $i18N = $cgLCI->getI18N();
        $decodeContext = Utilsv2::decodeContext($request["a"]);
        return response()->json([
            'message' => $i18N->getLanguage(ELanguageText::DataReceivedSuccessfully),
            'raw' => $decodeContext,
        ]);
    }

    public function index(Request $request)
    {
        return view('index', Controller::baseControllerInit($request, [])->toArrayable());
    }

    public function index2(Request $request)
    {
        list($maxPrice, $minPrice, $inventoryPaginate) = $this->ShopListLoader($request);
        return view('index2', Controller::baseControllerInit($request, [
            "inventorys" => $inventoryPaginate,
            "search" => null,
            'maxPrice' => $maxPrice,
            'minPrice' => $minPrice,
        ])->toArrayable());
    }

    public function getClientConfig()
    {
        $theme = Cookie::get('theme');
        return response()->json(["theme" => $theme]);
    }

    public function imageGen(Request $request)
    {
        $cgCI = $this->baseGlobalVariable($request);
        $i18N = $cgCI->getI18N();
        $vb = new ValidatorBuilder($i18N, EValidatorType::IMAGESGEN);
        $v = $vb->validate(["text" => $request->route('text'), "color" => "#" . $request->route('color')]);
        if ($v instanceof MessageBag) {
            return response(status: ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            $image = Image::create(600, 600);
            $image->text('Laravel by Intervention\Image', 20, 60, function (FontFactory $font) {
                $font->filename("./font/Roboto-Bold.ttf")->size(36)->lineHeight(1.6)->stroke("a1d45a", 5);
            });
            $image->text($v["text"], 360, 500, function (FontFactory $font) {
                $font->filename("./font/Roboto-Bold.ttf")->size(36)->lineHeight(1.6)->stroke("a1d45a", 5);
                $font->color('#333333');
                $font->align('center');
                $font->valign('middle');
            });
            $image->fill($v["color"], 10, 10);
            $image->drawBezier(function (BezierFactory $bezier) {
                $bezier->point(300, 260); // control point 1
                $bezier->point(150, 335); // control point 2
                $bezier->point(300, 410); // control point 3
                $bezier->background('f00'); // background color
                $bezier->border('ff0'); // border color
            });
            $image->drawPolygon(function (PolygonFactory $polygon) {
                $polygon->point(10, 10); // add point of polygon
                $polygon->point(150, 150); // add point
                $polygon->point(40, 180); // add point
                $polygon->point(60, 100); // add point
                $polygon->background('#b35187'); // background color
                $polygon->border('#ff0', 6); // border color and border width
            });
            $image->drawBezier(function (BezierFactory $bezier) {
                $bezier->point(50, 50); // control point 1
                $bezier->point(200, 50); // control point 2
                $bezier->point(150, 200); // control point 3
                $bezier->point(300, 200); // control point 4
                $bezier->border(Utils::getRandomHexColor(false), 4); // border color & size
            });
            $image->drawRectangle(10, 100, function (RectangleFactory $rectangle) {
                $rectangle->size(180, 125); // width & height of rectangle
                $rectangle->background('orange'); // background color of rectangle
                $rectangle->border('white', 2); // border color & size of rectangle
            });
            $image->drawEllipse(150, 150, function (EllipseFactory $ellipse) {
                $ellipse->size(180, 125); // width & height of ellipse
                $ellipse->background('f00'); // background color
                $ellipse->border('00f', 2); // border color & size
            });
            $image->drawLine(function (LineFactory $line) use ($image) {
                $line->from(0, 0); // starting point of line
                $line->to($image->width(), $image->height()); // ending point
                $line->color(Utils::getRandomHexColor(false)); // color of line
                $line->width(10); // line width in pixels
            });
            $encode = $image->toJpeg();
            // 返回带有适当响应头的图像响应
            return Response::make($encode, 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="test-image.jpg"',
            ]);
        }
    }
}
