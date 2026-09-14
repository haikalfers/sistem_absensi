<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class CheckInReminderNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;
    protected bool $isSecondReminder;

    public function __construct(string $title = 'Pengingat Absen Masuk', string $message = 'Selamat pagi! Jangan lupa untuk melakukan absen masuk hari ini.', bool $isSecondReminder = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->isSecondReminder = $isSecondReminder;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon('/favicon.ico')
            ->body($this->message)
            ->action('Absen Sekarang', 'open_dashboard')
            ->data(['url' => route('employee.attendance.index')])
            ->options(['TTL' => 1000]);
    }
}
