<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class CheckOutReminderNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;

    public function __construct(string $title = 'Pengingat Absen Keluar', string $message = 'Waktu kerja akan segera berakhir. Lakukan absen keluar sebelum Anda pulang.')
    {
        $this->title = $title;
        $this->message = $message;
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
            ->action('Absen Keluar', 'open_dashboard')
            ->data(['url' => route('employee.attendance.index')])
            ->options(['TTL' => 1000]);
    }
}
