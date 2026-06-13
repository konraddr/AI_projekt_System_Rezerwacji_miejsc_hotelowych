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

class ReportSubmittedNotification extends Notification
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
            ->subject('Zgłoszenie wysłane'.$this->hotelSuffix())
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
            'title' => 'Zgłoszenie wysłane',
            'message' => $this->body(),
            'url' => route('manage.reports.show', $this->report),
            'action_label' => 'Zobacz zgłoszenie',
        ];
    }

    private function body(): string
    {
        $excerpt = Str::limit($this->report->reason, 120);
        $status = $this->report->status->label();

        if ($this->report->hotel !== null) {
            return "Twoje zgłoszenie dotyczące hotelu {$this->report->hotel->name} zostało wysłane. Status: {$status}. „{$excerpt}”";
        }

        return "Twoje zgłoszenie zostało wysłane. Status: {$status}. „{$excerpt}”";
    }

    private function hotelSuffix(): string
    {
        if ($this->report->hotel === null) {
            return '';
        }

        return ' — '.$this->report->hotel->name;
    }

    private function webPushEnabled(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }
}
