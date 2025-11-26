<?php

use App\Http\Controllers\HTMLTemplateController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ShareTablesController;
use App\Http\Middleware\ClientConfigMiddleware;
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
Route::prefix("p")->group(function () {
    Route::get('share/{shortcode}', [ShareTablesController::class, 'publicShareableShareTableItem'])->name(RouteNameField::PageShareableShareTableItem->value);
    Route::get('download/{shortcode}/{fileId}', [ShareTablesController::class, 'publicShareableShareTableDownloadItem'])->name(RouteNameField::PagePublicShareTableDownloadItem->value)->withoutMiddleware(ClientConfigMiddleware::class);
    Route::get('preview/{shortcode}/{fileId}', [ShareTablesController::class, 'publicShareableShareTablePreviewItem'])->name(RouteNameField::PagePublicShareTablePreviewItem->value)->middleware('signed');
    Route::get('player/{shareTableId}/{fileId}/{fileName}', [ShareTablesController::class, 'publicShareableShareTablePlayerPreviewFile'])->name(RouteNameField::PagePublicShareTablePreviewFilePlayerDash->value);
    Route::get('dash/{shareTableId}/{fileId}/{fileName}', [ShareTablesController::class, 'publicShareableShareTableDashPreviewFile'])->name(RouteNameField::APIPublicShareTablePreviewFileDash->value);
});

Route::get('hello', [InternalController::class, 'getClientID'])->name(RouteNameField::PageGetClientID->value);
Route::post('hello', [InternalController::class, 'getClientIDPost'])->name(RouteNameField::PageGetClientIDPost->value);
Route::middleware("checkClientID")->group(function () {
    //Route::get('designcomponents', [InternalController::class,'designComponents'])->name(RouteNameField::PageDesignComponents->value);
    //Route::post('broadcast', [InternalController::class, 'broadcast_Notification_Notification'])->name(RouteNameField::APIBroadcast->value);
    //Route::post('language', [InternalController::class, 'language'])->name(RouteNameField::APILanguage->value);
    //Route::post('user', [InternalController::class, 'user']);
    //Route::post('browser', [InternalController::class, 'browser'])->name(RouteNameField::APIBrowser->value);
    Route::get('/', [InternalController::class, 'index'])->name(RouteNameField::PageHome->value);
    //Route::get('/index2', [InternalController::class, 'index2'])->name(RouteNameField::PageHome->value);
    Route::post('/clientconfig', [InternalController::class, 'getClientConfig'])->name(RouteNameField::APIClientConfig->value);
    Route::middleware(['auth', 'verified'])->prefix("sharetable")->group(function () {
        Route::post('share/{id}', [ShareTablesController::class, 'shareableShareTableItemPost'])->name(RouteNameField::APIShareableShareTableItem->value);
        Route::get('item/success', [ShareTablesController::class, 'successShareTable'])->name(RouteNameField::PageShareTableItemSuccess->value);
        Route::post('item/conversion/{id}/{fileId}', [ShareTablesController::class, 'conversionShareTableItem'])->name(RouteNameField::APIShareTableItemConversion->value);
        Route::get('item/download/{id}/{fileId}', [ShareTablesController::class, 'downloadShareTableItem'])->name(RouteNameField::PageShareTableItemDownload->value);
        Route::get('item/delete/{id}/{fileId}', [ShareTablesController::class, 'deleteShareTableItem'])->name(RouteNameField::PageShareTableItemDelete->value);
        Route::get('item/edit/{id}', [ShareTablesController::class, 'editShareTable'])->name(RouteNameField::PageShopItemEditor->value);
        Route::post('item/edit/{id}', [ShareTablesController::class, 'editShareTablePost'])->name(RouteNameField::APIShareTableItemEditPost->value);
        Route::post('item/edit2/{id}', [ShareTablesController::class, 'editShareTablePost2'])->name(RouteNameField::APIShareTableItemEditPost2->value);
        Route::get('delete/{id}', [ShareTablesController::class, 'deleteShareTable'])->name(RouteNameField::PageShareTableDelete->value);
        Route::get('item/{id}', [ShareTablesController::class, 'viewShareTableItem'])->name(RouteNameField::PageShareTableItemView->value);
        Route::get('my', [ShareTablesController::class, 'myShareTables'])->name(RouteNameField::PageMyShareTables->value);
        Route::post('add', [ShareTablesController::class, 'shareTableItemPost'])->name(RouteNameField::PageShareTableItemPost->value);
        Route::post('create', [ShareTablesController::class, 'shareTableItemCreatePost'])->name(RouteNameField::APIShareTableItemCreatePost->value);
        Route::post('upload', [ShareTablesController::class, 'shareTableItemUploadImagePost'])->name(RouteNameField::APIShareTableItemUploadImage->value);
        Route::delete('upload/revert', [ShareTablesController::class, 'shareTableItemUploadImageRevert'])->name(RouteNameField::APIShareTableItemUploadImageRevert->value);
        Route::patch('upload/patch/{fileinfo}', [ShareTablesController::class, 'shareTableItemUploadImagePatch'])->name(RouteNameField::APIShareTableItemUploadImagePatch->value);
        Route::get('upload/patch/{fileinfo}', [ShareTablesController::class, 'shareTableItemUploadImageHead'])->name(RouteNameField::APIShareTableItemUploadImageHead->value);
        Route::get('fetch/{fileId}', [ShareTablesController::class, 'shareTableItemUploadImageFetch'])->name(RouteNameField::APIShareTableItemUploadImageFetch->value);
        Route::get('preview/{fileId}', [ShareTablesController::class, 'apiPreviewFileTemporary'])->name(RouteNameField::APIPreviewFileTemporary->value)->middleware('signed'); // getTemporaryUrl used
        Route::get('preview/{shareTableId}/{fileId}', [ShareTablesController::class, 'apiPreviewFileTemporary2'])->name(RouteNameField::APIPreviewFileTemporary2->value)->middleware('signed'); // getTemporaryUrl used
        Route::get('previewThumbFile/{fileId}', [ShareTablesController::class, 'apiPreviewFileTemporary4'])->name(RouteNameField::APIPreviewFileTemporary4->value)->middleware('signed'); // getTemporaryUrl used
        Route::post('get_dash_progress', [ShareTablesController::class, 'getDashProgress'])->name(RouteNameField::APIDashProgress->value);
        Route::get('dash/{shareTableId}/{fileId}/{fileName}', [ShareTablesController::class, 'dashPreviewFile'])->name(RouteNameField::APIPreviewFileDash->value);
        Route::get('player/{shareTableId}/{fileId}/{fileName}', [ShareTablesController::class, 'playerPreviewFile'])->name(RouteNameField::PagePreviewFilePlayerDash->value);
    });
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

    Route::middleware('auth')->group(function () {
        Route::get('logout', [MemberController::class, 'logout'])->name(RouteNameField::PageLogout->value);
        Route::get('resendemail', [MemberController::class, 'resendEmail'])->name(RouteNameField::PageEmailReSendMailVerification->value);
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
