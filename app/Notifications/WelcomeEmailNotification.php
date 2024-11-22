<?php

namespace App\Notifications;

use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use App\Lib\Utils\Utils;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class WelcomeEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public I18N $i18N, public WelcomeEmailDataStructure $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Member $notifiable): MailMessage
    {
        $email = $notifiable->email;
        return (new MailMessage)
            ->view('EmailTemplate.WelcomeMail', [
                'username' => $notifiable['username'],
                'email' => $email,
                'content' => $this->i18N->getLanguage($this->data->content),
                'title' => $this->i18N->getLanguage($this->data->title),
                'APP_NAME' => $this->data->APP_NAME,
                'APP_VERSION' => $this->data->APP_VERSION,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

class WelcomeEmailDataStructure
{
    public string $APP_NAME;
    public string $APP_VERSION;
    public ELanguageText $title;
    public ELanguageText $content;

    /**
     * @param string $APP_NAME
     * @param string $APP_VERSION
     * @param ELanguageText $title
     * @param ELanguageText $content
     */
    public function __construct(
        ELanguageText $title,
        ELanguageText $content,
        string $APP_NAME = "",
        string $APP_VERSION = "",
    ) {
        $this->APP_NAME = Utils::default($APP_NAME, Config::get('app.name'));
        $this->APP_VERSION = Utils::default($APP_VERSION, Config::get('app.version'));
        $this->title = $title;
        $this->content = $content;
    }
}
