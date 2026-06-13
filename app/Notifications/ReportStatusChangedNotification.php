<?php

namespace App\Notifications;

use App\Models\Report;
use App\Notifications\Concerns\BuildsWebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ReportStatusChangedNotification extends Notification
{
    use BuildsWebPushMessage;
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    /**
     * @return array<int, string|class-string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];

        if ($this->webPushEnabled()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->report->loadMissing(['hotel']);

        return (new MailMessage)
            ->subject('Status zgłoszenia: '.$this->report->status->label())
            ->line($this->body())
            ->action('Zobacz zgłoszenie', route('manage.reports.show', $this->report));
    }

    public function toWebPush(object $notifiable, self $notification): WebPushMessage
    {
        return $this->buildWebPushMessage($this->toArray($notifiable));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->report->loadMissing(['hotel']);

        return [
            'report_id' => $this->report->id,
            'hotel_id' => $this->report->hotel_id,
            'status' => $this->report->status->value,
            'title' => 'Status zgłoszenia: '.$this->report->status->label(),
            'message' => $this->body(),
            'url' => route('manage.reports.show', $this->report),
            'action_label' => 'Zobacz zgłoszenie',
        ];
    }

    private function body(): string
    {
        $excerpt = Str::limit($this->report->reason, 120);
        $status = $this->report->status->label();

        return "Status Twojego zgłoszenia #{$this->report->id} zmienił się na: {$status}. „{$excerpt}”";
    }

    private function webPushEnabled(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }
}
