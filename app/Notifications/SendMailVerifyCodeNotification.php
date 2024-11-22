<?php

namespace App\Notifications;

use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use App\Lib\Type\String\CGStringable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendMailVerifyCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public I18N $i18N, public string $code)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        Log::info("=================================================================================================");
        Log::info("Process: VerifyEmailNotification");
        Log::info("Debug \$Instance: " . (new CGStringable($this)));
        Log::info("Debug \$i18N: " . $this->i18N->getLanguageCode()->name);
        Log::info("Debug \$code: 請複製號碼並輸入在驗證碼輸入框中。 驗證碼:" . $this->code);
        Log::info("=================================================================================================");
        return (new MailMessage)
            ->subject($this->i18N->getLanguage(ELanguageText::sendMailVerifyCode_Response_error1))
            ->line("請複製號碼並輸入在驗證碼輸入框中。 驗證碼:" . $this->code)
            ->line("如果你沒有發送驗證信件，請至帳號設定移除所有已登入裝置，並且更改密碼確保安全。");
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
