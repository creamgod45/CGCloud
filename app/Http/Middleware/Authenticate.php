<?php

namespace App\Http\Middleware;

use App\Events\UserNotification;
use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\I18N;
use App\Lib\Utils\CGLaravelControllerInit;
use App\Lib\Utils\RouteNameField;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::check() && Auth::user()->enable === "false") {
            //Log::info("Authenticate Middleware 1");
            $locale = App::getLocale();
            //dump($locale);
            $ELanguageCode = ELanguageCode::zh_TW;
            $i18N = new I18N($ELanguageCode, limitMode: [
                ELanguageCode::zh_TW,
            ]);
            //try {
            //    Log::info("Middleware Authenticate dump variables" . Json::encode([$ELanguageCode === null, $ELanguageCode instanceof ELanguageCode ? $ELanguageCode->name : $ELanguageCode, $locale, serialize($i18N), $i18N->getLanguage(ELanguageText::ResetPasswordLine1)], true));
            //} catch (JsonException $e) {
            //    Log::error("backtrace: " . $e->getMessage() . (new CGStringable(ClassUtils::varName($e))));
            //}
            $cgLCI = new CGLaravelControllerInit($i18N, [], $request);
            event((new UserNotification([
                "\$i18N->getLanguage(ELanguageText::UserAccountBanMessage)",
                "\$i18N->getLanguage(ELanguageText::notification_login_title)",
                "warning",
                10000,
                $cgLCI->getFingerprint(),
            ]))->delay(now()->addSeconds(5)));
            Auth::logout();
            //Log::info("Authenticate Middleware 2");
            return redirect(route(RouteNameField::PageLogin->value));
        }
        //Log::info("Authenticate Middleware 3");

        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route(RouteNameField::PageLogin->value);
    }
}
