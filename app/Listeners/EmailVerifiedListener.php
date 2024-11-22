<?php

namespace App\Listeners;

use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\I18N;
use App\Models\Member;
use App\Notifications\WelcomeEmailDataStructure;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class EmailVerifiedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        //
        $user = $event->user;
        if ($user instanceof Member) {
            Log::info('EmailVerifiedListener User verified', ['user_id' => $user->id, 'email' => $user->email]);
            $locale = App::getLocale();
            //dump($locale);
            $ELanguageCode = ELanguageCode::valueof($locale);
            if (ELanguageCode::isVaild($locale)) {
                $ELanguageCode = ELanguageCode::zh_TW;
            }
            $i18N = new I18N($ELanguageCode, limitMode: [
                ELanguageCode::zh_TW,
            ]);
            event((new WelcomeEmailNotification($i18N, new WelcomeEmailDataStructure("ELanguageText::EmailVerifiedListenerEmailTitle", "ELanguageText::EmailVerifiedListenerEmailContent")))->delay(now()->addSeconds(2)));
        }
    }
}
