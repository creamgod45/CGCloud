<?php

use App\Http\Controllers\HTMLTemplateController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ShareTablesController;
use App\Http\Middleware\EMiddleWareAliases;
use App\Lib\Utils\RouteNameField;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('hello', [InternalController::class, 'getClientID'])->name(RouteNameField::PageGetClientID->value);
Route::post('hello', [InternalController::class, 'getClientIDPost'])->name(RouteNameField::PageGetClientIDPost->value);
Route::middleware("checkClientID")->group(function () {
    //Route::get('designcomponents', [InternalController::class,'designComponents'])->name(RouteNameField::PageDesignComponents->value);
    //Route::post('broadcast', [InternalController::class, 'broadcast_Notification_Notification'])->name(RouteNameField::APIBroadcast->value);
    //Route::post('language', [InternalController::class, 'language'])->name(RouteNameField::APILanguage->value);
    //Route::post('user', [InternalController::class, 'user']);
    //Route::post('browser', [InternalController::class, 'browser'])->name(RouteNameField::APIBrowser->value);
    // password reset
    Route::get('/', [InternalController::class, 'index'])->name(RouteNameField::PageHome->value);
    Route::post('/clientconfig', [InternalController::class, 'getClientConfig'])->name(RouteNameField::APIClientConfig->value);
    Route::get('admin', [InternalController::class, 'SystemSettings'])->name(RouteNameField::PageSystemSettings->value);
    Route::post('admin/upload', [InternalController::class, 'SystemSettingsUploadFile'])->name(RouteNameField::APISystemSettingUpload->value);
    Route::get('custom/pages', [InternalController::class, 'CustomPages'])->name(RouteNameField::PageCustomPages->value);
    Route::get('custom/page/{id}', [InternalController::class, 'CustomPage'])->name(RouteNameField::PageCustomPage->value);
    Route::post('system/log', [InternalController::class, 'getSystemLogs'])->name(RouteNameField::APISystemLogs->value);
    Route::group(['prefix' => "sharetable"], function () {
        //Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('item/edit/{id}', [ShareTablesController::class, 'editor'])->name(RouteNameField::PageShopItemEditor->value);
            Route::post('add', [ShareTablesController::class, 'shareTableItemPost'])->name(RouteNameField::APIShareTableItemPost->value);
            Route::post('upload', [ShareTablesController::class, 'shareTableItemUploadImagePost'])->name(RouteNameField::APIShareTableItemUploadImage->value);
            Route::delete('upload/revert', [ShareTablesController::class, 'shareTableItemUploadImageRevert'])->name(RouteNameField::APIShareTableItemUploadImageRevert->value);
            Route::patch('upload/patch/{fileinfo}', [ShareTablesController::class, 'shareTableItemUploadImagePatch'])->name(RouteNameField::APIShareTableItemUploadImagePatch->value);
            Route::get('upload/patch/{fileinfo}', [ShareTablesController::class, 'shareTableItemUploadImageHead'])->name(RouteNameField::APIShareTableItemUploadImageHead->value);
            Route::get('fetch/{fileId}', [ShareTablesController::class, 'shareTableItemUploadImageFetch'])->name(RouteNameField::APIShareTableItemUploadImageFetch->value);
            Route::get('preview/{fileId}', [ShareTablesController::class, 'apiPreviewFileTemporary'])->name(RouteNameField::APIPreviewFileTemporary->value)->middleware('signed');
        //});
        Route::get('item/{id}', [ShareTablesController::class, 'index'])->name(RouteNameField::PageShopItem->value);
        Route::get('item/{id}/popover', [ShareTablesController::class, 'popover'])->name(RouteNameField::PageShopItemPopover->value);
        Route::get('search', [ShareTablesController::class, 'search'])->name(RouteNameField::PageSearchShopItem->value);
        Route::get('list', [ShareTablesController::class, 'list'])->name(RouteNameField::PageShopItemList->value);
        Route::post('list/API', [ShareTablesController::class, 'shareTableItemListJson'])->name(RouteNameField::APIShareTableItemList->value);
    });
    Route::get("image/{text}/{color}", [InternalController::class, "imageGen"])->name(RouteNameField::PageImageGenerator->value);
    Route::get('passwordreset', [MemberController::class, 'passwordReset'])->name(RouteNameField::PagePasswordReset->value);
    Route::post('passwordreset', [MemberController::class, 'passwordResetPost'])->name(RouteNameField::PagePasswordResetPost->value);
    // forgot password
    Route::get('forgot-password', [MemberController::class, 'forgetPassword'])->name(RouteNameField::PageForgetPassword->value);
    Route::post('forget-password', [MemberController::class, 'forgetPasswordPost'])->name(RouteNameField::PageForgetPasswordPost->value);
    // email verify
    Route::get('email/verify/{id}/{hash}', [MemberController::class, 'emailVerify'])->name(RouteNameField::PageEmailVerification->value);

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('members', [MemberController::class, 'index'])->name(RouteNameField::PageMembers->value);
    });

    Route::group(["prefix" => "HTMLTemplate"], function () {
        Route::post('Notification', [HTMLTemplateController::class, 'Notification'])->name(RouteNameField::APIHTMLTemplateNotification->value);
    });

    Route::middleware('auth')->group(function () {
        Route::get('logout', [MemberController::class, 'logout'])->name(RouteNameField::PageLogout->value);
        Route::get('resendemail', [MemberController::class, 'resendEmail'])->name(RouteNameField::PageEmailReSendMailVerification->value);
        Route::post('customer/order/list', [MemberController::class, 'CustomerOrderListPost'])
            ->name(RouteNameField::APICustomerOrderListPost->value);
        Route::get('customer/order/{id}', [MemberController::class, 'CustomerOrderList'])
            ->name(RouteNameField::PageCustomerOrderList->value);
        // Profile Start
        Route::get('profile', [MemberController::class, 'profile'])->name(RouteNameField::PageProfile->value);
        Route::post('profile', [MemberController::class, 'profilePost'])->name(RouteNameField::PageProfilePost->value);
        Route::group(['prefix' => 'profile'], function () {
            Route::group(['prefix' => 'email'], function () {
                Route::post('sendMailVerifyCode', [MemberController::class, 'sendMailVerifyCode_profile_email']);
                Route::post('verifyCode', [MemberController::class, 'verifyCode_profile_email']);
                Route::post('newMailVerifyCode', [MemberController::class, 'newMailVerifyCode_profile_email']);
            });
            Route::group(['prefix' => 'password'], function () {
                Route::post('sendMailVerifyCode', [MemberController::class, 'sendMailVerifyCode_profile_password']);
                Route::post('verifyCode', [MemberController::class, 'verifyCode_profile_password']);
            });
        });
        // Profile End
    });

    Route::middleware(EMiddleWareAliases::guest->name)->group(function () {
        Route::get('login', [MemberController::class, 'loginPage'])->name(RouteNameField::PageLogin->value);
        Route::post('login', [MemberController::class, 'loginPost'])->name(RouteNameField::PageLoginPost->value);
        Route::get('register', [MemberController::class, 'showRegistrationForm'])->name(RouteNameField::PageRegister->value);
        Route::post('register', [MemberController::class, 'register'])->name(RouteNameField::PageRegisterPost->value);
    });
});
