<?php /** @noinspection DuplicatedCode */

namespace App\Http\Controllers;

use App\Events\UserNotification;
use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use App\Lib\Server\CSRF;
use App\Lib\Utils\EValidatorType;
use App\Lib\Utils\RouteNameField;
use App\Lib\Utils\Utils;
use App\Lib\Utils\Utilsv2;
use App\Lib\Utils\ValidatorBuilder;
use App\Models\CustomerSaveOrder;
use App\Models\Member;
use App\Notifications\SendMailVerifyCodeNotification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\Response as ResponseHTTP;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->administrator === "true") {
            $members = Member::paginate(30, ['*'], 'members');
            return view('Member.members',
                $this::baseGlobalVariable($request, ['members' => $members, 'user' => $user])->toArrayable());
        } else {
            return redirect()->route(RouteNameField::PageLogin->value);
        }
    }

    public function CustomerOrderList(Request $request)
    {
        $id = $request->route('id', false);
        $customerSaveOrder = null;
        if($id !== false){
            $customerSaveOrder = CustomerSaveOrder::find($id);
        }
        return view('Member.CustomerOrderList',
            Controller::baseControllerInit($request, [ 'item' => $customerSaveOrder])->toArrayable());
    }

    public function CustomerOrderListPost()
    {
        $customerSaveOrders = CustomerSaveOrder
            //::where('UUID', Auth::user()->UUID)
            ::select([
                'id',
                'name',
                'priceTotal',
                'sentToLine',
                'expired_at',
                DB::raw('JSON_LENGTH(itemList) as countItem') // 使用 JSON_LENGTH 函數計算項目數量
            ])->getQuery();
        $array = DataTables::query($customerSaveOrders)
            ->escapeColumns()
            ->addColumn("checkbox", "rows[]")
            ->addColumn("#", "")
            ->addColumn("goto", '↗️')
            ->editColumn('sentToLine', function ($row) {
                return $row->sentToLine? '是' : '否';
            });
        return $array->toJson();
    }

    public function emailVerify(Request $request)
    {
        // 用户验证逻辑
        // 保证 ID 和 Hash 都是正确的
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $fingerprint = $cgLCI->getFingerprint();
        $vb = new ValidatorBuilder($i18N, EValidatorType::EMAILVERIFICATION);
        $v = $vb->validate(["id" => $request->route('id'), "hash" => $request->route('hash')]);
        $cache = $fingerprint;
        if ($v instanceof MessageBag) {
            $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                ["type" => "%type%", "messages" => $v->all()]);
            event((new UserNotification([
                $alertView->render(),
                $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                "warning",
                "10000",
                $cache,
            ])));
            return redirect(route(RouteNameField::PageHome->value));
        } else {
            $user = Member::find($request->route('id'));
            event((new UserNotification([
                $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                "warning",
                "10000",
                $cache,
            ])));
            if ($user === null) {
                return redirect(route(RouteNameField::PageHome->value));
            }

            if (!hash_equals((string)$request->route('id'), (string)$user->getKey()) ||
                !hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
                //return response()->json(["msg" => "Invalid verification link"], 400);
                return redirect(route(RouteNameField::PageHome->value))->with('mail_result', 0);
            }

            if ($user->hasVerifiedEmail()) {
                return redirect(route(RouteNameField::PageHome->value))->with('mail_result', 1);
            }

            $user->markEmailAsVerified();

            // 触发邮箱验证成功的事件
            event(new Verified($request->user()));

            // 返回验证成功的响应
            return redirect(route(RouteNameField::PageHome->value))->with('mail_result', 2);
        }
    }

    public function passwordReset(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::RESETPASSWORD);
        $v = $vb->validate($request->all());

        if ($v instanceof MessageBag) {
            // validator errors here
            return redirect(route(RouteNameField::PageHome->value))->with('invaild', true)->withErrors($v);
        } else {
            $user = Member::where('email', $v["email"])->first();
            $token = $v["token"];

            if (Password::tokenExists($user, $token)) {
                return view('Member.passwordreset', $this::baseGlobalVariable($request,
                    ['token' => $v["token"], 'email' => $v["email"]])->toArrayable());
            } else {
                return redirect(route(RouteNameField::PageHome->value));
            }
        }
    }

    public function passwordResetPost(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::RESETPASSWORDPOST);
        $v = $vb->validate($request->only('email', 'password', 'password_confirmation', 'token2', 'token'), [
            'password',
            'password_confirmation',
        ], true);
        $CSRF = new CSRF(RouteNameField::PagePasswordResetPost->value);

        if ($v instanceof MessageBag) {
            // validator errors here
            $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                ["type" => "%type%", "messages" => $v->all()]);
            $CSRF->reset();
            return response()->json([
                'type' => false,
                'token' => $CSRF->get(),
                "message" => $alertView->render(),
                "error_keys" => $v->keys(),
            ], ResponseHTTP::HTTP_OK);
        } else {
            if ($v['token2'] !== ($CSRF)->get()) {
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => ($CSRF)->get(),
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                    "error_keys" => ['token2'],
                ], ResponseHTTP::HTTP_OK);
            }
            $status = Password::reset(
                [
                    "email" => $v["email"],
                    "password" => $v['password'],
                    "token" => $v["token"],
                ],
                function (Member $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                },
            );
            Log::info($status);
            /**
             * @type ELanguageText $ELanguageText
             */
            $ELanguageText = ELanguageText::valueof(str_replace(".", "_", $status));
            Log::info(var_export($ELanguageText, true));
            return $status === Password::PASSWORD_RESET
                ? response()->json([
                    "type" => true,
                    "message" => $i18N->getLanguage($ELanguageText),
                    "redirect" => route(RouteNameField::PageLogin->value),
                ])
                : response()->json([
                    "type" => true,
                    "message" => $i18N->getLanguage($ELanguageText),
                    "redirect" => route(RouteNameField::PageHome->value),
                ]);
        }
    }

    public function forgetPassword(Request $request)
    {
        return view('Member.forgot-password', Controller::baseControllerInit($request)->toArrayable());
    }

    public function forgetPasswordPost(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $fingerprint = $cgLCI->getFingerprint();

        $vb = new ValidatorBuilder($i18N, EValidatorType::FORGOTPASSWORD);
        $v = $vb->validate($request->all());
        $CSRF = new CSRF(RouteNameField::PageForgetPasswordPost->value);
        if ($v instanceof MessageBag) {
            // validator errors here
            return redirect(route(RouteNameField::PageForgetPassword->value))->withInput()->withErrors($v);
        } else {
            if ($v['token'] !== ($CSRF)->get()) {
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => ($CSRF)->get(),
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                    "error_keys" => ['token'],
                ], ResponseHTTP::HTTP_OK);
            }
            $cacheKey = $v['email'] . ":forgetpassword";
            if (Cache::has($cacheKey)) {
                $CSRF->reset();
                event((new UserNotification([
                    $i18N->getLanguage(ELanguageText::passwords_sent),
                    $i18N->getLanguage(ELanguageText::ResetPasswordAction1),
                    "info",
                    "10000",
                    $fingerprint,
                ])));
                return response()->json([
                    'type' => true,
                    "message" => $i18N->getLanguage(ELanguageText::passwords_sent),
                    'redirect' => route(RouteNameField::PageHome->value),
                ], ResponseHTTP::HTTP_OK);
            } else {
                ResetPassword::toMailUsing(function (Member $member, $token) use ($fingerprint, $cgLCI) {
                    Log::info("run1");
                    $i18N = $cgLCI->getI18N();
                    $m = (new MailMessage)
                        ->success()
                        ->subject($i18N->getLanguage(ELanguageText::ResetPasswordAction1))
                        ->line($i18N->getLanguage(ELanguageText::ResetPasswordLine1))
                        ->action(
                            $i18N->getLanguage(ELanguageText::ResetPasswordAction1),
                            url(
                                route(
                                    RouteNameField::PagePasswordReset->value,
                                    ['token' => $token, 'email' => $member->email],
                                    false,
                                ),
                            ),
                        )
                        ->greeting($i18N->getLanguage(ELanguageText::ResetPasswordgreeting, true)
                            ->placeholderParser("username", $member->username)
                            ->toString())
                        ->salutation($i18N->getLanguage(ELanguageText::MailSalutation, true)
                            ->placeholderParser("appname", Config::get('app.name'))
                            ->toString())
                        ->line($i18N->getLanguage(ELanguageText::ResetPasswordLine2));
                    event((new UserNotification([
                        $i18N->getLanguage(ELanguageText::passwords_sent),
                        $i18N->getLanguage(ELanguageText::ResetPasswordAction1),
                        "info",
                        "10000",
                        $fingerprint,
                    ])));
                    return $m;
                });
                $status = Password::sendResetLink(
                    ['email' => $v['email']],
                );
                Cache::put($cacheKey, true, now()->addSeconds(60));
                $ELanguageText = ELanguageText::valueof(str_replace(".", "_", $status));
                return $status === Password::RESET_LINK_SENT
                    ? response()->json([
                        'type' => true,
                        'message' => $i18N->getLanguage($ELanguageText),
                        'redirect' => route(RouteNameField::PageHome->value),
                    ])
                    : response()->json([
                        'type' => false,
                        'token' => $CSRF->get(),
                        'message' => $i18N->getLanguage($ELanguageText),
                        'error_keys' => ['email'],
                    ]);
            }
        }
    }

    public function resendEmail(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            return redirect(route(RouteNameField::PageHome->value))->with('mail_result', 1);
        }
        $cacheKey = $user->UUID . ":mail-sent";
        if (Cache::has($cacheKey)) {
            return redirect(route(RouteNameField::PageHome->value))->with('mail', false);
        } else {
            $user->notifyNow(new VerifyEmailNotification($i18N));
            Cache::put($cacheKey, true, now()->addSeconds(60));
            return redirect(route(RouteNameField::PageHome->value))->with('mail', true);
        }
    }


    public function loginPage(Request $request)
    {
        return view('Member.login', $this::baseControllerInit($request)->toArrayable());
    }

    public function showRegistrationForm(Request $request): Factory|\Illuminate\Foundation\Application|View|Application
    {
        return view('Member.register', $this::baseControllerInit($request)->toArrayable());
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function register(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $fingerprint = $cgLCI->getFingerprint();

        $vb = new ValidatorBuilder($i18N, EValidatorType::REGISTER);
        $v = $vb->validate($request->all(), [
            'password',
            'password_confirmation',
        ], true);
        $CSRF = new CSRF(RouteNameField::PageRegisterPost->value);
        if ($v instanceof MessageBag) {
            Log::info($request->ip() . ": " . PHP_EOL . "    Request(Json)=" . Json::encode($request->all()));
            event((new UserNotification([
                implode('<br>', $v->all()),
                $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                "error",
                "10000",
                $fingerprint,
            ])));
            $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                ["type" => "%type%", "messages" => $v->all()]);
            $CSRF->reset();
            return response()->json([
                'type' => false,
                'token' => $CSRF->get(),
                "message" => $alertView->render(),
                "error_keys" => $v->keys(),
            ], ResponseHTTP::HTTP_OK);
            //redirect('register')->withErrors($v)->withInput();
        } else {
            if ($v['token'] !== ($CSRF)->get()) {
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => ($CSRF)->get(),
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                    "error_keys" => ['token'],
                ], ResponseHTTP::HTTP_OK);
            }
            // 可以在这里实现登录逻辑，或者重定向到登录页面
            Log::info($v['username'] . ": registering");
            $user = Member::create([
                'username' => $v['username'],
                'email' => $v['email'],
                'phone' => $v['phone'],
                'password' => Hash::make($v['password']),
                'enable' => 'true',
                'administrator' => 'false',
            ]);
            Log::info($user->username . ": registered");

            // 发送验证邮件
            $cacheKey = $user->UUID . ":mail-sent";
            if (Cache::has($cacheKey)) {
                event((new UserNotification([
                    $i18N->getLanguage(ELanguageText::notification_email_description),
                    $i18N->getLanguage(ELanguageText::notification_email_verifyTitle),
                    "warning",
                    "5000",
                    $fingerprint,
                ])));
                return response()->json([
                    'type' => true,
                    "message" => $i18N->getLanguage(ELanguageText::registerDone), // 註冊成功請驗證信箱!!在 1 小時候驗證將過期
                    "redirect" => route(RouteNameField::PageHome->value),
                ]);
            } else {
                Log::info($user->username . ": mailing");
                $instance = new VerifyEmailNotification($i18N);
                $instance->delay(now()->addSeconds(5));
                $user->notifyNow($instance);
                Log::info($user->username . ": mailed");
                Cache::put($cacheKey, true, now()->addHours());
                Auth::login($user);
                event((new UserNotification([
                    $i18N->getLanguage(ELanguageText::notification_email_description),
                    $i18N->getLanguage(ELanguageText::notification_email_verifyTitle),
                    "success",
                    "5000",
                    $fingerprint,
                ])));
                return response()->json([
                    'type' => true,
                    "message" => $i18N->getLanguage(ELanguageText::registerDone),
                    "redirect" => route(RouteNameField::PageHome->value),
                ]);
            }
            //return redirect(route("home"))->with('mail', true);
        }
    }

    /**
     * @throws JsonException
     */
    public function loginPost(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $fingerprint = $cgLCI->getFingerprint();

        $vb = new ValidatorBuilder($i18N, EValidatorType::LOGIN);
        $v = $vb->validate($request->all(), [
            "password",
        ], true);
        $CSRF = new CSRF(RouteNameField::PageLoginPost->value);
        if ($v instanceof MessageBag) {
            Log::info($request->ip() . ": " . PHP_EOL . "    Request(Json)=" . Json::encode($request->all()));
            event((new UserNotification([
                $i18N->getLanguage(ELanguageText::notification_login_failed),
                $i18N->getLanguage(ELanguageText::notification_login_title),
                "error",
                "10000",
                $fingerprint,
            ])));
            $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                ["type" => "warning", "messages" => $v->all()]);
            $CSRF->reset();
            return response()->json([
                'type' => false,
                'token' => $CSRF->get(),
                "message" => $alertView->render(),
                "error_keys" => $v->keys(),
            ], ResponseHTTP::HTTP_OK);
        } else {
            Log::info(var_export([$v['token'], ($CSRF)->get()], true));
            if ($v['token'] !== ($CSRF)->get()) {
                event((new UserNotification([
                    $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                    $i18N->getLanguage(ELanguageText::notification_login_title),
                    "error",
                    "10000",
                    $fingerprint,
                ])));
                $errors = new MessageBag;
                $errors->add('token', $i18N->getLanguage(ELanguageText::CSRFVerificationFailed));
                $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                    ["type" => "danger", "messages" => $errors->all()]);
                $CSRF->reset();
                return response()->json([
                    'type' => false,
                    'token' => ($CSRF)->get(),
                    "message" => $alertView->render(),
                    "error_keys" => ['token'],
                ], ResponseHTTP::HTTP_OK);
            }
            $CSRF->reset();
            $user = Member::where("username", $v["username"])->first();
            if ($user !== null) {
                Log::info($v["password"]);
                if (Hash::check($v["password"], $user["password"])) {
                    Log::info($user->username . ": Logging in");
                    Auth::login($user);
                    Log::info($user->username . ": logined");
                    if (Auth::check() && Auth::user()->enable === "false") {
                        $errors = new MessageBag;
                        $errors->add('username', $i18N->getLanguage(ELanguageText::UserAccountBanMessage));
                        event((new UserNotification([
                            $i18N->getLanguage(ELanguageText::UserAccountBanMessage),
                            $i18N->getLanguage(ELanguageText::notification_login_title),
                            "warning",
                            10000,
                            $fingerprint,
                        ])));
                        $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                            ["type" => "danger", "messages" => $errors->all()]);
                        Auth::logout();
                        return response()->json([
                            'type' => false,
                            'token' => $CSRF->get(),
                            "message" => $alertView->render(),
                            "error_keys" => $v->keys(),
                        ], ResponseHTTP::HTTP_OK);
                    }
                    event((new UserNotification([
                        $i18N->getLanguage(ELanguageText::notification_login_success),
                        $i18N->getLanguage(ELanguageText::notification_login_title),
                        "success",
                        10000,
                        $fingerprint,
                    ])));
                    return response()->json([
                        'type' => true,
                        "message" => $i18N->getLanguage(ELanguageText::notification_login_success),
                        "redirect" => route(RouteNameField::PageHome->value),
                    ], ResponseHTTP::HTTP_OK);
                } else {
                    // 自訂錯誤訊息
                    $msg = $i18N->getLanguage(ELanguageText::login_faild, true)
                        ->Replace("%validator_field_username%",
                            $i18N->getLanguage(ELanguageText::validator_field_username))
                        ->Replace("%validator_field_password%",
                            $i18N->getLanguage(ELanguageText::validator_field_password))
                        ->toString();
                    $errors = new MessageBag;
                    $errors->add('username', $msg);
                    event((new UserNotification([
                        $msg,
                        $i18N->getLanguage(ELanguageText::notification_login_title),
                        "warning",
                        10000,
                        $fingerprint,
                    ])));
                    $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                        ["type" => "warning", "messages" => $errors->all()]);
                    Log::info($request->ip() . ": " . PHP_EOL . "    ValidationException=asd," . PHP_EOL . "    Request(Json)=" . Json::encode($request->all()));
                    return response()->json([
                        'type' => false,
                        'token' => $CSRF->get(),
                        "message" => $alertView->render(),
                        "error_keys" => $errors->keys(),
                    ], ResponseHTTP::HTTP_OK);
                }
            } else {
                $msg = $i18N->getLanguage(ELanguageText::login_username_notfound, true)
                    ->Replace("%validator_field_username%", $i18N->getLanguage(ELanguageText::validator_field_username))
                    ->toString();

                $errors = new MessageBag;
                $errors->add('username', $msg);
                $alertView = \Illuminate\Support\Facades\View::make('components.alert',
                    ["type" => "danger", "messages" => $errors->all()]);

                return response()->json([
                    'type' => false,
                    'token' => $CSRF->get(),
                    "message" => $alertView->render(),
                    "error_keys" => $errors->keys(),
                ], ResponseHTTP::HTTP_OK);
            }
        }
    }

    public function logout(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();
        $fingerprint = $cgLCI->getFingerprint();

        Log::info($request->user()["username"] . ": logout");
        event((new UserNotification([
            $i18N->getLanguage(ELanguageText::logout_context, true)->placeholderParser("s", 5)->toString(),
            $i18N->getLanguage(ELanguageText::logout_title),
            "warning",
            10000,
            $fingerprint,
        ]))->delay(now()->addSeconds(3)));
        Auth::logout();
        return view('Member.logout', $this::baseControllerInit($request)->toArrayable());
    }

    public function profile(Request $request)
    {
        return view('Member.profile', $this::baseControllerInit($request)->toArrayable());
    }

    /**
     * @throws Exception
     * @noinspection PhpUnreachableStatementInspection
     */
    public function profilePost(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::PROFILEGENERAL);
        $v = $vb->validate($request->all(), [
            'sendMailVerifyCodeToken',
        ], true);
        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ]);
        } else {
            if (!(new CSRF('profile.profilepost'))->equal($v['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                    "value" => [
                        (new CSRF('profile.profilepost'))->get(),
                        $v['token'],
                    ],
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            if (!isset($v['method'])) {
                return response()->json(["messages" => $i18N->getLanguage(ELanguageText::miss_method)],
                    ResponseHTTP::HTTP_BAD_REQUEST);
            }
            switch ($v['method']) {
                case 'email':
                    $vb1 = new ValidatorBuilder($i18N, EValidatorType::PROFILEUPDATEEMAIL);
                    $v1 = $vb1->validate($request->all());
                    if ($v1 instanceof MessageBag) {
                        // validator errors here
                        return response()->json([
                            "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                            'errors' => $v1->all(),
                        ]);
                    } else {
                        if (!Session::has('profile.email.newMailVerifyCode')) {
                            return response()->json(["message" => "沒有 Session 資料"], ResponseHTTP::HTTP_BAD_REQUEST);
                        }
                        if (Session::get('profile.email.newMailVerifyCode') !== $v1['verification']) {
                            return response()->json(["message" => "Session 資料不相同"],
                                ResponseHTTP::HTTP_BAD_REQUEST);
                        }
                        if (Session::get("profile.email.sendMailVerifyCodeToken") !== $v['sendMailVerifyCodeToken']) {
                            return response()->json([
                                "message" => $i18N->getLanguage(ELanguageText::sendMailVerifyCodeNotEqualValue, true)
                                    ->placeholderParser("validator_field_VerificationCode",
                                        $i18N->getLanguage(ELanguageText::validator_field_VerificationCode))
                                    ->toString(),
                            ], ResponseHTTP::HTTP_BAD_REQUEST);
                            // "錯誤 信箱身份驗證權杖" validator_field_VerificationCode
                        }
                        return $this->profilepost_email($v1, $i18N);
                    }
                case 'password':
                    $vb1 = new ValidatorBuilder($i18N, EValidatorType::PROFILEUPDATEPASSWORD);
                    //Log::info("password: ". \Psy\Util\Json::encode($request->all()));
                    $v1 = $vb1->validate($request->all(), [
                        "current-ps",
                        "password",
                        "password_confirmation",
                    ], true);
                    if ($v1 instanceof MessageBag) {
                        // validator errors here
                        //Log::info("MessageBag: ". serialize($v1));
                        return response()->json([
                            "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                            'errors' => $v1->all(),
                        ], ResponseHTTP::HTTP_BAD_REQUEST);
                    } else {
                        //Log::info("password: ". \Psy\Util\Json::encode($v1));
                        if (Session::get("profile.password.sendMailVerifyCodeToken") !== $v['sendMailVerifyCodeToken']) {
                            return response()->json([
                                "message" => $i18N->getLanguage(ELanguageText::sendMailVerifyCodeNotEqualValue, true)
                                    ->placeholderParser("validator_field_VerificationCode",
                                        $i18N->getLanguage(ELanguageText::validator_field_VerificationCode))
                                    ->toString(),
                            ], ResponseHTTP::HTTP_BAD_REQUEST);
                        }
                        $member = Auth::user();
                        //Log::info("password Verify: ".PHP_EOL.$v1['current-ps'].PHP_EOL.$member->getAuthPassword());
                        //Log::info("password Verify: ".Hash::check($v1['current-ps'], $member->getAuthPassword()));
                        //Log::info("test Verify: ".Hash::check("5as19fg1a9sg", $member->getAuthPassword()));
                        if (!Hash::check($v1['current-ps'], $member->getAuthPassword())) {
                            return response()->json(["message" => "錯誤 密碼"], ResponseHTTP::HTTP_BAD_REQUEST);
                        }
                        return $this->profilepost_password($v1, $i18N);
                    }
                    break;
            }
        }
        return redirect(route(RouteNameField::PageHome->value));
    }

    /**
     * @param array $v
     * @param I18N  $i18N
     *
     * @return JsonResponse
     */
    private function profilepost_email(array $v, I18N $i18N): JsonResponse
    {
        $member = Auth::user();
        if ($member instanceof Member) {
            $member->fill([
                'email' => $v['email'],
            ]);
            $member->save();
        }
        (new CSRF('profile.email.sendMailVerifyCode'))->release();
        Session::forget('profile.email.newMailVerifyCode');
        return response()->json([
            "message" => $i18N->getLanguage(ELanguageText::FieldDataUpdatedSuccessfully, true)
                ->placeholderParser("field", $i18N->getLanguage(ELanguageText::validator_field_email))
                ->toString(),
        ]);
    }

    private function profilepost_password(array $v, I18N $i18N)
    {
        $member = Auth::user();
        if ($member instanceof Member) {
            $member->forceFill([
                'password' => Hash::make($v['password']),
            ]);
            $member->save();
        }
        (new CSRF('profile.password.sendMailVerifyCode'))->release();
        Session::forget('profile.password.MailVerifyCode');
        return response()->json([
            "message" => $i18N->getLanguage(ELanguageText::FieldDataUpdatedSuccessfully, true)
                ->placeholderParser("field", $i18N->getLanguage(ELanguageText::validator_field_password))
                ->toString(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function sendMailVerifyCode_profile_email(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::SENDMAILVERIFYCODE);
        $v = $vb->validate($request->all());
        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ], ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            if (!(new CSRF('profile.email.sendMailVerifyCode'))->equal($request['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            $member = Auth::user();
            $cacheKey = $member->UUID . ":sendMailVerifyCode";

            $csrf = (new CSRF('profile.email.sendMailVerifyCode'))->reset()->get();
            if (Cache::has($cacheKey)) {
                return response()->json([
                    "message" => $i18N->getLanguage(
                            ELanguageText::sendMailVerifyCode_Response_error1) . " " .
                        $i18N->getLanguage(ELanguageText::ExpireTime, true)
                            ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                    "cooldown" => Cache::get($cacheKey),
                    "token" => $csrf,
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            } else {
                $random = Str::random(5);
                Session::put('profile.email.sendMailVerifyCode', $random);
                //dump($i18N);
                $notification = new SendMailVerifyCodeNotification($i18N, $random);
                Notification::send($member, $notification->delay(now()->addSeconds(5)));
                Cache::put($cacheKey, time() + 60, 60);
                return response()->json([
                    "message" => $i18N->getLanguage(
                            ELanguageText::sendMailVerifyCode_Response_success) . " " .
                        $i18N->getLanguage(ELanguageText::ExpireTime, true)
                            ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                    "cooldown" => Cache::get($cacheKey),
                    "token" => $csrf,
                ], ResponseHTTP::HTTP_OK);
            }
        }
        //return response()->json([
        //    "message" => $i18N->getLanguage(ELanguageText::HTTP_FORBIDDEN),
        //],ResponseHTTP::HTTP_FORBIDDEN);
    }

    /**
     * @throws Exception
     */
    public function newMailVerifyCode_profile_email(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::NEWMAILVERIFYCODE);
        $v = $vb->validate($request->all());

        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ]);
        } else {
            if (!(new CSRF('profile.email.newMailVerifyCode'))->equal($request['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            $member = Auth::user();
            if ($member instanceof Member) {
                $csrf = (new CSRF('profile.email.newMailVerifyCode'))->reset()->get();
                $cacheKey = $member->UUID . ":newMailVerifyCode";
                if ($v['email'] === $member->email) {
                    return response()->json([
                        "message" => $i18N->getLanguage(ELanguageText::Unable_to_change_the_same_field, true)
                            ->placeholderParser("field", $i18N->getLanguage(ELanguageText::validator_field_email))
                            ->toString(), // 無法更換相同的電子信箱 Unable to change the same email address
                        "token" => $csrf,
                    ], ResponseHTTP::HTTP_BAD_REQUEST);
                }
                if (Cache::has($cacheKey)) {
                    return response()->json([
                        "message" => $i18N->getLanguage(
                                ELanguageText::sendMailVerifyCode_Response_error1) . " " .
                            $i18N->getLanguage(ELanguageText::ExpireTime, true)
                                ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                        "token" => $csrf,
                    ], ResponseHTTP::HTTP_BAD_REQUEST);
                } else {
                    $random = Str::random(5);
                    Session::put('profile.email.newMailVerifyCode', $random);

                    $notification = new SendMailVerifyCodeNotification($i18N, $random);
                    $notification->delay(Carbon::now()->addSeconds(5));
                    Notification::route('mail', $v['email'])->notify($notification);
                    Cache::put($cacheKey, time() + 60, 60);
                    return response()->json([
                        "message" => $i18N->getLanguage(
                                ELanguageText::sendMailVerifyCode_Response_success) . " " .
                            $i18N->getLanguage(ELanguageText::ExpireTime, true)
                                ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                        "token" => $csrf,
                    ], ResponseHTTP::HTTP_OK);
                }
            }
        }
        return redirect(route(RouteNameField::PageHome->value));
    }

    /**
     * @throws Exception
     */
    public function verifyCode_profile_email(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::VERIFYCODE);
        $v = $vb->validate($request->all());

        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ], ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            if (!(new CSRF('profile.email.verifyCode'))->equal($request['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            $code = Session::get('profile.email.sendMailVerifyCode');
            if ($code === $v['code']) {
                Session::forget('profile.email.sendMailVerifyCode');
                $str = Str::random(10);
                Session::put("profile.email.sendMailVerifyCodeToken", $str);
                return response()->json([
                    "messages" => "驗證成功",
                    "access_token" => Utilsv2::encodeContext($str)['compress'],
                ]);
            } else {
                return response()->json(["messages" => "驗證碼錯誤"], ResponseHTTP::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function sendMailVerifyCode_profile_password(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::SENDMAILVERIFYCODE);
        $v = $vb->validate($request->all());
        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "message" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ], ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            if (!(new CSRF('profile.password.sendMailVerifyCode'))->equal($request['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            $member = Auth::user();
            $cacheKey = $member->UUID . ":profile.password.sendMailVerifyCode";

            $csrf = (new CSRF('profile.password.sendMailVerifyCode'))->reset()->get();
            if (Cache::has($cacheKey)) {
                return response()->json([
                    "message" => $i18N->getLanguage(
                            ELanguageText::sendMailVerifyCode_Response_error1) . " " .
                        $i18N->getLanguage(ELanguageText::ExpireTime, true)
                            ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                    "cooldown" => Cache::get($cacheKey),
                    "token" => $csrf,
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            } else {
                $random = Str::random(5);
                Session::put('profile.password.sendMailVerifyCode', $random);
                //dump($i18N);
                $notification = new SendMailVerifyCodeNotification($i18N, $random);
                Notification::send($member, $notification->delay(now()->addSeconds(5)));
                Cache::put($cacheKey, time() + 60, 60);
                return response()->json([
                    "message" => $i18N->getLanguage(
                            ELanguageText::sendMailVerifyCode_Response_success) . " " .
                        $i18N->getLanguage(ELanguageText::ExpireTime, true)
                            ->placeholderParser("timestamp", Utils::timeStamp(Cache::get($cacheKey)))->toString(),
                    "cooldown" => Cache::get($cacheKey),
                    "token" => $csrf,
                ], ResponseHTTP::HTTP_OK);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function verifyCode_profile_password(Request $request)
    {
        $cgLCI = self::baseControllerInit($request);
        $i18N = $cgLCI->getI18N();

        $vb = new ValidatorBuilder($i18N, EValidatorType::VERIFYCODE);
        $v = $vb->validate($request->all());

        if ($v instanceof MessageBag) {
            // validator errors here
            return response()->json([
                "messages" => $i18N->getLanguage(ELanguageText::ValidatorBuilderFailed),
                'errors' => $v->all(),
            ], ResponseHTTP::HTTP_BAD_REQUEST);
        } else {
            if (!(new CSRF('profile.password.verifyCode'))->equal($request['token'])) {
                return response()->json([
                    "message" => $i18N->getLanguage(ELanguageText::CSRFVerificationFailed),
                ], ResponseHTTP::HTTP_BAD_REQUEST);
            }
            $code = Session::get('profile.password.sendMailVerifyCode'); // 原始驗證碼
            if ($code === $v['code']) {
                Session::forget('profile.password.sendMailVerifyCode');
                $str = Str::random(10);
                Session::put("profile.password.sendMailVerifyCodeToken", $str); // 許可驗證碼
                return response()->json([
                    "messages" => "驗證成功",
                    "access_token" => Utilsv2::encodeContext($str)['compress'],
                ]);
            } else {
                return response()->json(["messages" => "驗證碼錯誤"], ResponseHTTP::HTTP_BAD_REQUEST);
            }
        }
    }
}

